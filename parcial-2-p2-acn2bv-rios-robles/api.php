<?php
header('Content-Type: application/json; charset=utf-8');

$DATA_FILE = __DIR__ . '/utils/data.json';

function read_data($file)
{
    if (!file_exists($file)) return [];
    $json = file_get_contents($file);
    $arr = json_decode($json, true);
    return is_array($arr) ? $arr : [];
}

function write_data($file, $data)
{
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $fp = fopen($file, 'c+');
    if (!$fp) return false;
    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        return false;
    }
    ftruncate($fp, 0);
    rewind($fp);
    $written = fwrite($fp, $json);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return $written !== false;
}


$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $nombre = $_GET['nombre'] ?? '';
    $categoria = $_GET['categoria'] ?? '';
    $page = max(1, intval($_GET['page'] ?? 1));
    $per_page = max(1, intval($_GET['per_page'] ?? 100));

    $items = read_data($DATA_FILE);

    $items = array_filter($items, function ($it) use ($nombre, $categoria) {
        $ok = true;
        if ($nombre !== '') {
            $ok = $ok && (stripos($it['name'], $nombre) !== false);
        }
        if ($categoria !== '') {
            $ok = $ok && ($it['categoria'] === $categoria);
        }
        return $ok;
    });

    $total = count($items);
    $items = array_values($items);
    $start = ($page - 1) * $per_page;
    $paged = array_slice($items, $start, $per_page);

    echo json_encode([
        'success' => true,
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page,
        'items' => $paged
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $data = json_decode($inputJSON, true);
    if (!is_array($data)) {
        $data = $_POST;
    }

    $name = trim($data['name'] ?? '');
    $categoria = trim($data['categoria'] ?? '');
    $descrip = trim($data['descrip'] ?? '');
    $url = trim($data['url'] ?? '');
    $link = trim($data['link'] ?? '');

    if ($name === '' || $categoria === '' || $descrip === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios (name, categoria, descrip).']);
        exit;
    }

    $items = read_data($DATA_FILE);

    $new = [
        'name' => $name,
        'categoria' => $categoria,
        'descrip' => $descrip,
        'url' => $url ?: 'https://via.placeholder.com/300x180?text=Imagen',
        'link' => $link ?: '#'
    ];

    $items[] = $new;
    $ok = write_data($DATA_FILE, $items);

    if ($ok) {
        echo json_encode(['success' => true, 'message' => 'Item agregado correctamente', 'item' => $new], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'No se pudo guardar el item (permisos?)']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Método no permitido']);
