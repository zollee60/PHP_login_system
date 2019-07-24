<?php
require_once "config.php";

require "class/RegForm.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $_SESSION["scope"] = basename(__FILE__,".php");
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/custom.css">
</head>
<body>
<div class="wrapper" id="form-wrapper">
    <h2>Regisztráció</h2>
    <p>Töltsd ki az alábbi mezőket a regisztrációhoz</p>
</div>
</body>
</html>
<script type="text/javascript" src="js/form.js"></script>