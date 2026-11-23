<?php
// Small helpers shared by every controller

// Send any array as JSON and stop
function sendJson($data, $code = 200)
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Read the JSON body of a request as an asociative array
function readBody()
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        $data = [];
    }
    return $data;
}

// Read the id of the query string as an integer
function readId()
{
    return isset($_GET['id']) ? (int)$_GET['id'] : 0;
}

// The browser asked for a method this controller does not know
function methodNotAllowed()
{
    sendJson(['error' => 'Method not allowed'], 405);
}
