<?php
require "model/User.php";

$requestPayload = file_get_contents('php://input');
$userData = json_decode($requestPayload,true);
$user = new User($userData);
if(isset($_SESSION["scope"]) && $_SESSION["action"] == "register"){
    $user->registerUser();
} elseif (isset($_SESSION["scope"]) && $_SESSION["action"] == "login"){
    $user->loginUser();
    if(array_key_exists("email", $user->getAllErrors())){
        $error = ["email" => $user->getError("email")];
        $errorJSON = json_encode($error);
        echo $errorJSON;
    }elseif (array_key_exists("password", $user->getAllErrors())){
        $error = ["password" => $user->getError("password")];
        $errorJSON = json_encode($error);
        echo $errorJSON;
    }else{
        $error = ["noerror" => "nothing"];
        $errorJSON = json_encode($error);
        echo $errorJSON;
    }
}

