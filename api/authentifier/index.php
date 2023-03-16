In dev...
<?php
require('../files/jwt_utils.php');
require('../conn.php');
require('../files/api-utils.php');
header("Content-Type:application/json");
/// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];
// Check if the request is a POST
if ($http_method != 'POST') {
    // If not a POST, then send a 405 Method Not Allowed response
    header("HTTP/1.1 405 Method Not Allowed");
    exit();
}
// Get the JSON data
$data = json_decode(file_get_contents('php://input'), true);
// Check if the JSON contain the login and mdp fields
if (!isset($data['login']) || !isset($data['mdp'])) {
    // If not, then send a 400 Bad Request response
    exit();
}
