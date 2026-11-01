<?php
class PracticalCaseController
{
    public function index(): void
    {
        $sid = Auth::id();
        $rows = DB::all("SELECT c.id, c.category, c.title, c.context, c.mode, c.points,
                (SELECT MAX(score) FROM case_submissions s WHERE s.case_id=c.id AND s.student_id=?) AS best,
                (SELECT COUNT(*) FROM case_submissions s WHERE s.case_id=c.id AND s.student_id=?) AS subs
            FROM practical_cases c WHERE c.status='active' ORDER BY CASE c.category WHEN 'Administración' THEN 1 WHEN 'Ventas' THEN 2 WHEN 'Inventario' THEN 3 WHEN 'Recursos Humanos' THEN 4 WHEN 'Educación' THEN 5 END, c.id", [$sid, $sid]);
        $grouped = [];
        foreach ($rows as $r) $grouped[$r['category']][] = $r;
        render('cases/index', ['grouped' => $grouped]);
    }

    private function find(int $id): array
    {
        $c = DB::one("SELECT * FROM practical_cases WHERE id=? AND status='active'", [$id]);
        if (!$c) abort(404);
        return $c;
    }

    public function show(int $id): void
    {
        $c = $this->find($id);
        $subs = Auth::role() === 'estudiante' ? DB::all('SELECT * FROM case_submissions WHERE case_id=? AND student_id=? ORDER BY id DESC LIMIT 6', [$id, Auth::id()]) : [];
        render('cases/show', [
            'c' => $c, 'sheet' => json_decode((string)$c['data_json'], true), 'questions' => json_decode((string)$c['questions_json'], true) ?: [],
            'subs' => $subs, 'maxMb' => config('upload.max_mb'), 'justSubmitted' => (int)input_int('ok'),
        ]);
    }

    public function template(int $id): void
    {
        $c = $this->find($id);
        $sheet = json_decode((string)$c['data_json'], true);
        if (!$sheet) abort(404);
        $bin = XlsxWriter::build($sheet['headers'], $sheet['rows'], mb_substr($c['title'], 0, 28));
        $name = 'ExcelLab_caso_' . $id . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Content-Length: ' . strlen($bin));
        echo $bin;
        exit;
    }

    public function submit(int $id): void
    {
        $c = $this->find($id);
        if ($c['mode'] === 'platform') {
            $this->submitPlatform($c);
        } else {
            $this->submitFile($c);
        }
        $badges = BadgeService::check(Auth::id());
        foreach ($badges as $b) flash('info', 'Nuevo logro: ' . $b['name']);
        redirect('casos/' . $id . '?ok=1');
    }

    private function submitPlatform(array $c): void
    {
        $qs = json_decode((string)$c['questions_json'], true) ?: [];
        $given = $_POST['answers'] ?? [];
        $results = [];
        $correct = 0;
        foreach ($qs as $i => $q) {
            $a = is_array($given) ? trim((string)($given[$i] ?? '')) : '';
            $ok = $a !== '' && ExerciseEvaluator::matches($q['kind'] ?? 'text', $a, (string)$q['answer'], (float)($q['tolerance'] ?? 0.01));
            $results[] = ['answer' => mb_substr($a, 0, 200), 'ok' => $ok];
            $correct += $ok ? 1 : 0;
        }
        $score = $qs ? round($c['points'] * $correct / count($qs), 2) : 0;
        $id = DB::insert('case_submissions', [
            'case_id' => $c['id'], 'student_id' => Auth::id(), 'answers_json' => json_encode($results, JSON_UNESCAPED_UNICODE),
            'score' => $score, 'status' => 'reviewed', 'feedback' => "Calificación automática: $correct de " . count($qs) . ' respuestas correctas.',
        ]);
        Activity::log('caso_entregado', 'practical_cases', (int)$c['id'], "Auto: $correct/" . count($qs));
        flash($correct === count($qs) ? 'success' : 'warning', "Caso calificado: $correct de " . count($qs) . " respuestas correctas ($score pts).");
    }

    private function submitFile(array $c): void
    {
        $back = 'casos/' . $c['id'];
        $f = $_FILES['file'] ?? null;
        if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) {
            flash('danger', 'Selecciona el archivo de Excel que vas a entregar.');
            redirect($back);
        }
        if ($f['error'] !== UPLOAD_ERR_OK) {
            flash('danger', $f['error'] === UPLOAD_ERR_INI_SIZE || $f['error'] === UPLOAD_ERR_FORM_SIZE ? 'El archivo supera el tamaño permitido.' : 'No se pudo subir el archivo. Inténtalo de nuevo.');
            redirect($back);
        }
        $maxBytes = config('upload.max_mb') * 1024 * 1024;
        if ($f['size'] > $maxBytes) {
            flash('danger', 'El archivo supera los ' . config('upload.max_mb') . ' MB permitidos.');
            redirect($back);
        }
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, config('upload.extensions'), true)) {
            flash('danger', 'Solo se aceptan archivos ' . implode(', ', array_map(fn($e) => '.' . $e, config('upload.extensions'))) . '.');
            redirect($back);
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
        $head = (string)file_get_contents($f['tmp_name'], false, null, 0, 8);
        $magicOk = match ($ext) {
            'xlsx' => str_starts_with($head, "PK\x03\x04"),
            'xls'  => str_starts_with($head, "\xD0\xCF\x11\xE0"),
            default => !str_contains((string)file_get_contents($f['tmp_name'], false, null, 0, 2048), "\0"), // csv: texto
        };
        if (!in_array($mime, config('upload.mimes'), true) || !$magicOk) {
            flash('danger', 'El contenido del archivo no corresponde a una hoja de cálculo válida.');
            redirect($back);
        }
        $stored = bin2hex(random_bytes(16)) . '.' . $ext;
        if (!move_uploaded_file($f['tmp_name'], ROOT . '/storage/uploads/' . $stored)) {
            flash('danger', 'No se pudo guardar el archivo en el servidor.');
            redirect($back);
        }
        $origName = mb_substr(preg_replace('/[^\p{L}\p{N}\s._()-]/u', '', basename($f['name'])), 0, 150) ?: ('entrega.' . $ext);
        $subId = DB::insert('case_submissions', [
            'case_id' => $c['id'], 'student_id' => Auth::id(), 'file_path' => $stored, 'original_name' => $origName, 'status' => 'submitted',
        ]);
        Activity::log('caso_entregado', 'practical_cases', (int)$c['id'], $origName);
        Notifier::staff('Nueva entrega: ' . $c['title'], Auth::user()['name'] . ' entregó un archivo para revisar.', 'admin/entregas/' . $subId);
        flash('success', 'Archivo entregado correctamente. Tu docente lo revisará pronto.');
    }

    /** Descarga protegida: solo el autor o el personal docente. */
    public function download(int $id): void
    {
        $s = DB::one('SELECT * FROM case_submissions WHERE id=?', [$id]);
        if (!$s || !$s['file_path']) abort(404);
        if (!Auth::isStaff() && (int)$s['student_id'] !== Auth::id()) abort(403);
        $path = ROOT . '/storage/uploads/' . basename($s['file_path']);
        if (!is_file($path)) abort(404, 'El archivo ya no está disponible.');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . str_replace('"', '', $s['original_name']) . '"');
        header('Content-Length: ' . filesize($path));
        header('X-Content-Type-Options: nosniff');
        readfile($path);
        exit;
    }
}
