<?php

require_once "config.php";
require "model/User.php";

session_start();

if(!User::loggedIn()){
    header("location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üdvözöllek!</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/custom.css">
       
</head>
<body>
    <div class="wrapper" id="form-wrapper">
        <h1>Szia, <b><?= htmlspecialchars($_SESSION["surName"]); ?></b>. Üdvözöllek az oldalon.</h1>
        <p>
            <a href="logout.php" class="btn btn-danger">Kijelentkezés</a>
        </p>
    </div>
</body>
</html>