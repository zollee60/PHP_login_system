<?php
require "model/User.php";

$requestPayload = file_get_contents('php://input');
$userData = json_decode($requestPayload,true);
$user = new User($userData);
if($userData["scope"] == "register"){
    $user->registerUser();
} elseif ($userData["scope"] == "login"){
    $user->loginUser();
}
$error = json_encode($user->getAllErrors());
echo $error;
