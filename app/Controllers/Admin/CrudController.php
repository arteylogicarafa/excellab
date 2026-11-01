<?php
/**
 * CRUD genérico del panel docente. Las entidades se describen en app/Core/Entities.php.
 * No se eliminan registros: se cambia su estado (activo / inactivo / archivado).
 */
class CrudController
{
    private const PER_PAGE = 20;

    private function entity(string $key): array
    {
        $e = Entities::get($key);
        if (!$e) abort(404);
        if (!empty($e['admin_only']) && Auth::role() !== 'admin') abort(403);
        $e['key'] = $key;
        // Alias de la tabla principal: el último FROM de la consulta (los anteriores son subconsultas)
        $e['alias'] = preg_match_all('/FROM\s+\w+\s+(\w+)/i', $e['sql'], $m) ? end($m[1]) : 't';
        return $e;
    }

    private function options(array $f): array
    {
        if (isset($f['options'])) return $f['options'];
        if (isset($f['sql'])) return DB::pdo()->query($f['sql'])->fetchAll(PDO::FETCH_KEY_PAIR);
        return [];
    }

    // ------------------------------------------------------------------ listado
    public function index(string $key): void
    {
        $e = $this->entity($key);
        $a = $e['alias'];
        $where = [];
        $params = [];
        if (!empty($e['where'])) $where[] = $e['where'];
        $q = input('q');
        if ($q !== '') {
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $ors = [];
            foreach ($e['search'] as $col) { $ors[] = "$col ILIKE ?"; $params[] = $like; }
            $where[] = '(' . implode(' OR ', $ors) . ')';
        }
        $status = input('status');
        if ($status === '') $where[] = "$a.status<>'archived'";
        elseif (in_array($status, ['active', 'inactive', 'archived'], true)) { $where[] = "$a.status=?"; $params[] = $status; }
        $filters = [];
        foreach ($e['filters'] ?? [] as [$field, $label, $source, $col]) {
            $opts = is_array($source) ? $source : DB::pdo()->query($source)->fetchAll(PDO::FETCH_KEY_PAIR);
            $val = input('f_' . $field);
            if ($val !== '' && in_array($val, array_map('strval', array_keys($opts)), true)) { $where[] = "$col=?"; $params[] = $val; }
            $filters[] = ['name' => 'f_' . $field, 'label' => $label, 'options' => $opts, 'value' => $val];
        }
        $w = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $total = (int)DB::val('SELECT COUNT(*) FROM (' . $e['sql'] . $w . ') cnt', $params);
        $pages = max(1, (int)ceil($total / self::PER_PAGE));
        $page = min(max(1, input_int('page', 1)), $pages);
        $rows = DB::all($e['sql'] . $w . ' ORDER BY ' . $e['order'] . ' LIMIT ' . self::PER_PAGE . ' OFFSET ' . (($page - 1) * self::PER_PAGE), $params);
        render('admin/crud/index', ['e' => $e, 'rows' => $rows, 'q' => $q, 'status' => $status, 'filters' => $filters, 'page' => $page, 'pages' => $pages, 'total' => $total]);
    }

    // --------------------------------------------------------------- formulario
    private function loadRow(array $e, int $id): array
    {
        $visible = DB::val('SELECT 1 FROM (' . $e['sql'] . (empty($e['where']) ? '' : ' WHERE ' . $e['where']) . ') v WHERE v.id=?', [$id]);
        $row = $visible ? DB::one('SELECT * FROM ' . $e['table'] . ' WHERE id=?', [$id]) : null;
        if (!$row) abort(404);
        unset($row['password_hash']);
        return $row;
    }

    public function form(string $key, int $id = 0): void
    {
        $e = $this->entity($key);
        $row = $id ? $this->loadRow($e, $id) : [];
        foreach ($e['fields'] as $f) {
            if (!isset($row[$f['name']]) && !$id && isset($f['default'])) $row[$f['name']] = $f['default'];
        }
        $hook = $e['hooks'] ?? '';
        if ($hook === 'exercises' && $id) {
            $lines = [];
            foreach (DB::all('SELECT label, is_correct FROM exercise_options WHERE exercise_id=? ORDER BY position, id', [$id]) as $o) $lines[] = ($o['is_correct'] ? '*' : '') . $o['label'];
            $row['options_text'] = implode("\n", $lines);
        }
        if ($hook === 'assessments' && $id) {
            $row['question_ids'] = array_column(DB::all('SELECT exercise_id FROM assessment_questions WHERE assessment_id=?', [$id]), 'exercise_id');
        }
        $checks = [];
        if ($hook === 'assessments') {
            $checks['question_ids'] = DB::all("SELECT e.id, e.title, e.type, e.difficulty, m.title AS module_title FROM exercises e JOIN modules m ON m.id=e.module_id WHERE e.status<>'archived' ORDER BY m.position, e.id");
        }
        $opts = [];
        foreach ($e['fields'] as $f) if ($f['type'] === 'select') $opts[$f['name']] = $this->options($f);
        $suggest = (!$id && $hook === 'users') ? 'Excel' . random_int(1000, 9999) . '#' : '';
        $old = $_SESSION['old'] ?? [];
        render('admin/crud/form', ['e' => $e, 'row' => $row, 'id' => $id, 'opts' => $opts, 'checks' => $checks, 'old' => $old, 'suggest' => $suggest]);
        clear_old();
    }

    // ----------------------------------------------------------------- guardado
    public function save(string $key): void
    {
        $e = $this->entity($key);
        $id = input_int('id');
        if ($id) $this->loadRow($e, $id); // valida alcance
        $hook = $e['hooks'] ?? '';
        $data = [];
        $errors = [];
        foreach ($e['fields'] as $f) {
            if (!empty($f['virtual'])) continue;
            $n = $f['name'];
            $raw = $_POST[$n] ?? '';
            $raw = is_array($raw) ? '' : trim((string)$raw);
            $label = $f['label'];
            if ($raw === '') {
                if (!empty($f['required'])) { $errors[] = "El campo «$label» es obligatorio."; continue; }
                if ($f['type'] === 'number') $data[$n] = $f['default'] ?? 0;
                elseif ($f['type'] === 'select') $data[$n] = !empty($f['nullable']) ? null : ($f['default'] ?? null);
                else $data[$n] = $f['default'] ?? null;
                continue;
            }
            switch ($f['type']) {
                case 'text': case 'textarea': case 'email': case 'html':
                    if (isset($f['max']) && mb_strlen($raw) > $f['max']) { $errors[] = "«$label» no puede superar {$f['max']} caracteres."; continue 2; }
                    if (isset($f['min']) && mb_strlen($raw) < $f['min']) { $errors[] = "«$label» debe tener al menos {$f['min']} caracteres."; continue 2; }
                    if ($f['type'] === 'email') {
                        $raw = strtolower($raw);
                        if (!filter_var($raw, FILTER_VALIDATE_EMAIL)) { $errors[] = "«$label» no es un correo válido."; continue 2; }
                    }
                    if (isset($f['pattern']) && !preg_match('/^' . $f['pattern'] . '$/', $raw)) { $errors[] = "«$label» tiene un formato no válido."; continue 2; }
                    $data[$n] = $f['type'] === 'html' ? clean_html($raw) : $raw;
                    break;
                case 'number':
                    if (!is_numeric($raw)) { $errors[] = "«$label» debe ser un número."; continue 2; }
                    $v = $raw + 0;
                    if ((isset($f['min']) && $v < $f['min']) || (isset($f['max']) && $v > $f['max'])) { $errors[] = "«$label» está fuera del rango permitido."; continue 2; }
                    $data[$n] = $v;
                    break;
                case 'select':
                    $opts = $this->options($f);
                    if (!array_key_exists($raw, $opts)) { $errors[] = "El valor de «$label» no es válido."; continue 2; }
                    $data[$n] = $raw;
                    break;
                case 'json':
                    $j = json_decode($raw, true);
                    if ($j === null && strtolower($raw) !== 'null') { $errors[] = "«$label» no es un JSON válido (" . json_last_error_msg() . ').'; continue 2; }
                    $data[$n] = json_encode($j, JSON_UNESCAPED_UNICODE);
                    break;
            }
        }

        $errors = array_merge($errors, $this->validate($hook, $e, $data, $id));
        if ($errors) {
            foreach ($errors as $m) flash('danger', $m);
            remember_input($_POST);
            redirect('admin/' . $key . ($id ? "/$id/editar" : '/nuevo'));
        }

        $data = array_merge($data, $e['fixed'] ?? []);
        $this->beforeSave($hook, $data, $id);
        $isNew = !$id;
        if ($id) {
            DB::update($e['table'], $data, $id);
        } else {
            $id = DB::insert($e['table'], $data);
        }
        $this->afterSave($hook, $id);
        Activity::log($isNew ? 'crear' : 'editar', $e['table'], $id, $data['title'] ?? $data['name'] ?? null);
        clear_old();
        flash('success', ucfirst($e['singular']) . ' guardado correctamente.');
        redirect('admin/' . $key);
    }

    private function passwordProblem(string $p): ?string
    {
        if (strlen($p) < 8 || !preg_match('/[A-Za-z]/', $p) || !preg_match('/\d/', $p)) return 'La contraseña debe tener al menos 8 caracteres e incluir letras y números.';
        return null;
    }

    private function validate(string $hook, array $e, array &$data, int $id): array
    {
        $err = [];
        switch ($hook) {
            case 'users':
                if (isset($data['email']) && DB::val('SELECT 1 FROM users WHERE email=? AND id<>?', [$data['email'], $id])) $err[] = 'Ya existe un usuario con ese correo.';
                $pass = (string)($_POST['password'] ?? '');
                if (!$id && $pass === '' && $e['key'] !== 'estudiantes') $err[] = 'Indica una contraseña inicial.';
                if ($pass !== '' && ($m = $this->passwordProblem($pass))) $err[] = $m;
                if ($id === Auth::id() && ($data['status'] ?? 'active') !== 'active') $err[] = 'No puedes deshabilitar tu propia cuenta.';
                if ($id === Auth::id() && isset($data['role_id']) && (int)$data['role_id'] !== 1 && Auth::role() === 'admin') $err[] = 'No puedes quitarte el rol de administrador a ti mismo.';
                break;
            case 'exercises':
                if (($data['answer_kind'] ?? '') === 'option') {
                    $lines = array_values(array_filter(array_map('trim', preg_split('/\R/', (string)($_POST['options_text'] ?? ''))), fn($l) => $l !== ''));
                    $correct = count(array_filter($lines, fn($l) => $l[0] === '*'));
                    if (count($lines) < 2) $err[] = 'Agrega al menos dos alternativas (una por línea).';
                    elseif ($correct !== 1) $err[] = 'Marca exactamente una alternativa correcta con * al inicio de la línea.';
                    $data['correct_answer'] = null;
                } else {
                    if (empty($data['correct_answer'])) $err[] = 'Indica la respuesta correcta.';
                    elseif ($data['answer_kind'] === 'number') {
                        foreach (explode('||', $data['correct_answer']) as $alt) if (ExerciseEvaluator::parseNumber($alt) === null) $err[] = "«$alt» no es un número válido.";
                    }
                }
                break;
            case 'cases':
                if (($data['mode'] ?? '') === 'platform') {
                    $qs = json_decode((string)($data['questions_json'] ?? ''), true);
                    if (!is_array($qs) || !$qs) $err[] = 'Un caso en plataforma necesita preguntas con respuesta (JSON).';
                    else foreach ($qs as $q) if (!is_array($q) || empty($q['label']) || !isset($q['answer'])) { $err[] = 'Cada pregunta debe tener "label" y "answer".'; break; }
                } elseif (empty($data['data_json'])) {
                    $err[] = 'Un caso con entrega de archivo necesita datos de trabajo (JSON) para generar la plantilla de Excel.';
                }
                break;
            case 'assessments':
                $ids = $_POST['question_ids'] ?? [];
                if (!is_array($ids) || !array_filter($ids, 'is_numeric')) $err[] = 'Selecciona al menos una pregunta para el banco de la evaluación.';
                break;
        }
        if ($e['key'] === 'logros' && DB::val('SELECT 1 FROM achievements WHERE code=? AND id<>?', [$data['code'] ?? '', $id])) $err[] = 'Ya existe una insignia con ese código.';
        return $err;
    }

    private function beforeSave(string $hook, array &$data, int $id): void
    {
        if ($hook === 'users') {
            $pass = (string)($_POST['password'] ?? '');
            if ($pass !== '') {
                $data['password_hash'] = password_hash($pass, PASSWORD_DEFAULT);
                $data['must_change_password'] = 1;
                $data['failed_attempts'] = 0;
                $data['locked_until'] = null;
            } elseif (!$id) {
                // Estudiantes: entran solo con su correo; se guarda una clave aleatoria que nadie usa
                $data['password_hash'] = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
                $data['must_change_password'] = 0;
            }
        }
        if ($hook === 'exercises' && empty($data['lesson_id'])) $data['lesson_id'] = null;
    }

    private function afterSave(string $hook, int $id): void
    {
        if ($hook === 'exercises') {
            DB::exec('DELETE FROM exercise_options WHERE exercise_id=?', [$id]);
            if (($_POST['answer_kind'] ?? '') === 'option') {
                $pos = 1;
                foreach (preg_split('/\R/', (string)($_POST['options_text'] ?? '')) as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    $ok = $line[0] === '*';
                    DB::insert('exercise_options', ['exercise_id' => $id, 'label' => mb_substr(ltrim($ok ? substr($line, 1) : $line), 0, 400), 'is_correct' => (int)$ok, 'position' => $pos++]);
                }
            }
        }
        if ($hook === 'assessments') {
            $ids = array_values(array_unique(array_map('intval', array_filter((array)($_POST['question_ids'] ?? []), 'is_numeric'))));
            DB::exec('DELETE FROM assessment_questions WHERE assessment_id=?', [$id]);
            foreach ($ids as $p => $exId) {
                if (DB::val('SELECT 1 FROM exercises WHERE id=?', [$exId])) DB::insert('assessment_questions', ['assessment_id' => $id, 'exercise_id' => $exId, 'position' => $p + 1]);
            }
        }
    }

    // ------------------------------------------------------------------- estado
    public function status(string $key, int $id): void
    {
        $e = $this->entity($key);
        $this->loadRow($e, $id);
        $new = input('status');
        if (!in_array($new, ['active', 'inactive', 'archived'], true)) abort(404);
        if ($e['table'] === 'users' && $id === Auth::id()) {
            flash('danger', 'No puedes cambiar el estado de tu propia cuenta.');
            redirect('admin/' . $key);
        }
        DB::update($e['table'], ['status' => $new], $id);
        Activity::log('estado_' . $new, $e['table'], $id);
        flash('success', 'Estado actualizado: ' . Entities::statusOptions()[$new] . '.');
        redirect('admin/' . $key);
    }
}
