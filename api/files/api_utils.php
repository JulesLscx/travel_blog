<?php
function deliver_response(int $status, string $status_message, $data): void
{

    $response = array();
    header("HTTP/1.1 $status $status_message");
    /// Paramétrage de la réponse retournée
    $response['status'] = $status;
    $response['status_message'] = $status_message;
    $response['data'] = $data;
    $json_response = json_encode($response);
    echo $json_response;
}
/**
 * Function is_authorized
 * Check if the user is authorized to access the resource he is trying to access
 * @param int $privileges_required : the privileges required to access the resource
 * @return bool : true if the user is authorized, false otherwise
 **/
function is_authorized(int $privileges_required): bool
{
    require_once('jwt_utils.php');
    $jwt = get_bearer_token();
    if ($jwt == null) {
        return false;
    }
    if (is_jwt_valid($jwt)) {
        $payload = json_decode(base64_decode(explode('.', $jwt)[1]));
        if ($payload->privileges == $privileges_required) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
/**
 * Function is_valid_user
 * @param string $login : the login of the user
 * @param string $mdp : the password of the user
 * @return bool|int : false if the user is not valid, the role of the user if it is valid
 */
function is_valid_user(string $login, string $mdp): int
{
    require_once('bdd_utils.php');
    $conn = DBConnection::getInstance()->getConnection();
    $sql = "SELECT * FROM users WHERE login = '$login' AND mdp = '$mdp'";
    $result = $conn->query($sql);
    $result->execute();
    if ($result->rowCount() == 1) {
        $datas = $result->fetch();
        return $datas['ROLE'];
    } else {
        return -1;
    }
}
