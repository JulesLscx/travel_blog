<?php
function deliver_response($status, $status_message, $data)
{

    $response = array();
    header("HTTP/1.1 $status $status_message");
    /// Paramétrage de la réponse retournée
    $response['status'] = $status;
    $response['status_message'] = $status_message;
    $response['data'] = $data;
    $json_response = json_encode($response);
    // var_dump($json_response);
    echo $json_response;
}
