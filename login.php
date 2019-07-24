<?php

require_once "config.php";
require "model/User.php";
require "class/RegForm.php";

session_start();

if(User::loggedIn()){
    header("location: index.php");
    exit;
}
$form = new RegForm();
$user = new User();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user->setAttributes($_POST);
    $user->loginUser();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/custom.css">
    <script type="text/javascript" src="js/form.js"></script>
</head>
<body>
<div class="wrapper" id="form-wrapper">
    <h2 id="a">Bejelentkezés</h2>
</div>
</body>
</html>

