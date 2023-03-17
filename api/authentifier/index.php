In dev...
<?php
require('../files/jwt_utils.php');
require('../conn.php');
require('../files/api-utils.php');
header("Content-Type:application/json");
$http_method = $_SERVER['REQUEST_METHOD'];

if ($http_method != 'POST') {
    deliver_response(405, "Method Not Allowed", NULL);
    exit();
}
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['login']) || !isset($data['mdp'])) {
    deliver_response(400, "Bad Request, POST body must contain login & mdp fields", NULL);
    exit();
}
$login = $data['login'];
$mdp = $data['mdp'];
