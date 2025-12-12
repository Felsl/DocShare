<?php
// site/controllers/_helpers.php
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function getJsonBody() {
    $body = file_get_contents('php://input');
    return $body ? json_decode($body, true) : [];
}
