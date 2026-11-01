<?php
/** Acceso a datos con PDO (PostgreSQL / Neon) y consultas preparadas. */
class DB
{
    private static ?PDO $pdo = null;
    /** @var array<string, string[]> */
    private static array $columns = [];

    public static function pdo(): PDO
    {
        if (!self::$pdo) {
            self::$pdo = self::connect();
            $tz = str_replace("'", "''", (string)config('timezone'));
            self::$pdo->exec("SET TIME ZONE '$tz'");
        }
        return self::$pdo;
    }

    /**
     * Conecta adaptando la URL a versiones antiguas de libpq:
     * - retira opciones que la librería no reconoce (p. ej. channel_binding);
     * - si no hay SNI, añade el identificador del endpoint de Neon (options=endpoint=...).
     */
    private static function connect(): PDO
    {
        $url = (string)config('db.url');
        if ($url === '') {
            throw new RuntimeException('Falta la variable de entorno DATABASE_URL con la conexión de Neon.');
        }
        $u = parse_url($url);
        if ($u === false || empty($u['host'])) {
            throw new RuntimeException('DATABASE_URL no es una URL válida.');
        }
        $host = $u['host'];
        $dsn = self::dsn($u);
        // Neon con libpq antiguo (sin SNI): identificar el endpoint en el primer intento.
        if (str_starts_with($host, 'ep-') && stripos($dsn, 'options=') === false) {
            $dsn .= ';options=endpoint=' . preg_replace('/-pooler$/', '', explode('.', $host)[0]);
        }
        $opts = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $user = isset($u['user']) ? rawurldecode($u['user']) : null;
        $pass = isset($u['pass']) ? rawurldecode($u['pass']) : null;

        for ($try = 0; $try < 5; $try++) {
            try {
                return new PDO($dsn, $user, $pass, $opts);
            } catch (PDOException $e) {
                $msg = $e->getMessage();
                if (preg_match('/invalid connection option "([^"]+)"/i', $msg, $m)) {
                    $dsn = self::dropParam($dsn, $m[1]);   // opción no soportada por la librería
                    continue;
                }
                if (stripos($msg, 'endpoint ID is not specified') !== false && str_starts_with($host, 'ep-')) {
                    $ep = preg_replace('/-pooler$/', '', explode('.', $host)[0]);
                    $dsn .= ';options=endpoint=' . $ep;    // Neon sin soporte SNI en libpq antiguo
                    continue;
                }
                throw $e;
            }
        }
        throw new RuntimeException('No se pudo conectar con DATABASE_URL.');
    }

    /** DSN de PostgreSQL construido a partir de la variable de entorno DATABASE_URL. */
    private static function dsn(array $u): string
    {
        $dsn = 'pgsql:host=' . $u['host']
             . ';port=' . ($u['port'] ?? 5432)
             . ';dbname=' . ltrim($u['path'] ?? '', '/');
        // Parámetros de la URL (sslmode=require, application_name, etc.)
        if (!empty($u['query'])) {
            parse_str($u['query'], $q);
            foreach ($q as $k => $v) {
                if (is_string($v) && $v !== '' && preg_match('/^[a-z_]+$/', $k)) {
                    $dsn .= ';' . $k . '=' . $v;
                }
            }
        }
        return $dsn;
    }

    private static function dropParam(string $dsn, string $name): string
    {
        $kept = [];
        foreach (explode(';', $dsn) as $p) {
            $key = explode('=', $p, 2)[0];
            if (strcasecmp($key, 'pgsql:' . $name) === 0 || strcasecmp($key, $name) === 0) {
                continue;
            }
            $kept[] = $p;
        }
        return implode(';', $kept);
    }

    /** Identificador entre comillas dobles (PostgreSQL no usa acentos graves). */
    private static function ident(string $name): string
    {
        return '"' . str_replace('"', '""', $name) . '"';
    }

    /** Columnas de una tabla (información_schema, consultado una sola vez por tabla). */
    private static function columns(string $table): array
    {
        if (!isset(self::$columns[$table])) {
            $rows = self::all(
                "SELECT column_name FROM information_schema.columns WHERE table_schema=current_schema() AND table_name=?",
                [$table]
            );
            self::$columns[$table] = array_map('strval', array_column($rows, 'column_name'));
        }
        return self::$columns[$table];
    }

    public static function q(string $sql, array $params = []): PDOStatement
    {
        // Sin parámetros: consulta simple de ida y vuelta única (misma semántica que prepare+execute).
        if ($params === [] && !str_contains($sql, '?')) {
            return self::pdo()->query($sql);
        }
        $st = self::pdo()->prepare($sql);
        $st->execute(array_values($params));
        return $st;
    }

    public static function all(string $sql, array $params = []): array
    {
        return self::q($sql, $params)->fetchAll();
    }

    public static function one(string $sql, array $params = []): ?array
    {
        $r = self::q($sql, $params)->fetch();
        return $r === false ? null : $r;
    }

    public static function val(string $sql, array $params = [])
    {
        $r = self::q($sql, $params)->fetchColumn();
        return $r === false ? null : $r;
    }

    public static function exec(string $sql, array $params = []): int
    {
        return self::q($sql, $params)->rowCount();
    }

    /** INSERT ... RETURNING id (PostgreSQL no tiene lastInsertId()). */
    public static function insert(string $table, array $data): int
    {
        $cols = array_keys($data);
        $quoted = implode(',', array_map([self::class, 'ident'], $cols));
        $sql = 'INSERT INTO ' . self::ident($table) . " ($quoted) VALUES ("
             . implode(',', array_fill(0, count($cols), '?')) . ')';
        $hasId = in_array('id', self::columns($table), true);
        if ($hasId) {
            $sql .= ' RETURNING id';
        }
        $st = self::q($sql, array_values($data));
        return $hasId ? (int)$st->fetchColumn() : 0;
    }

    /** UPDATE; si la tabla tiene updated_at, se actualiza como hacía ON UPDATE CURRENT_TIMESTAMP. */
    public static function update(string $table, array $data, int $id): void
    {
        $set = implode(',', array_map(fn($c) => self::ident($c) . '=?', array_keys($data)));
        if (in_array('updated_at', self::columns($table), true) && !array_key_exists('updated_at', $data)) {
            $set .= ',"updated_at"=NOW()';
        }
        self::q('UPDATE ' . self::ident($table) . " SET $set WHERE id=?", array_merge(array_values($data), [$id]));
    }

    public static function setting(string $key, string $default = ''): string
    {
        $v = self::val('SELECT svalue FROM settings WHERE skey=?', [$key]);
        return $v === null ? $default : (string)$v;
    }
}
