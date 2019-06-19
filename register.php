<?php
require_once "config.php";
require "model/User.php";
require "class/RegForm.php";

$form = new RegForm();
$user = new User();

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $user->setAttributes($_POST);
    $user->registerUser();
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
<div class="wrapper">
    <h2>Regisztráció</h2>
    <p>Töltsd ki az alábbi mezőket a regisztrációhoz</p>
    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <?php
        $i=0;
        while ($i<5){
            ?>

            <div class="form-group <?= (!empty($user->getError($form->getError($i)))) ? 'has-error' : ''; ?>">
                <label><?= $form->getLabel($i) ?></label>
                <input type="<?= $form->getItype($i); ?>" name="<?= $form->getFname($i); ?>" class="form-control" required>
                <span class="help-block"><?= $user->getError($form->getError($i)); ?></span>
            </div>

            <?php
            $i=$i+1;
        }
        ?>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Regisztráció">
            <input type="reset" class="btn btn-default" value="Törlés">
        </div>
        <p>Már van fiókod? <a href="login.php">Bejelentkezés</a>.</p>
    </form>
</div>
</body>
</html>