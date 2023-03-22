<?php
require('../files/jwt_utils.php');
require('../files/bdd_utils.php');
require('../files/api_utils.php');
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
$privilege = is_valid_user($login, $mdp);
if ($privilege >= 0) {
    $payload = array(
        "login" => $login,
        "privileges" => $privilege,
        "exp" => time() + 60
    );
    $header = array(
        "alg" => "HS256",
        "typ" => "JWT"
    );
    $jwt = generate_jwt($header, $payload);
    deliver_response(200, "OK", $jwt);
} else {
    deliver_response(401, "Unauthorized, invalid login or password", NULL);
}
