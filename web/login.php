<?php

    session_start();
    session_destroy();
    
require '../procesos/dbcon.php';

?>

<!DOCTYPE html>
<meta charset="UTF-8">
<html>

    <head>
        <title> Conectarse </title>
        <link rel="stylesheet" href="../css/estilos.css?v=1.0">
        
    </head>

    <body>

        <div class="container">
            <div class="login">
                <H1> Iniciar Sesión </H1>
                <form id="formulario" action="#" method="POST">

                <?php if (isset($_GET['success']) && $_GET['success'] == 'false') {
                echo '<div class="success-message"> &#9747; Usuario o contraseña incorrecta </div><br>'; } ?>

                    <label id="campos"> Nombre de Usuario </label> <BR>
                    <input type="text" name="Usuario" placeholder="usuario" size="28" maxlength="7" required><br><br>

                    <label id="campos"> Contraseña </label><br>
                    <input type="password" name="secreto" size="28" maxlength="30" required><br><br>

                    <input type="submit" value="Entrar">
                    <a href="register.php"> No estoy registrado </a>
                </form>
            </div>
        </div>
    </body>
</html>

<?php

    if (isset($_POST['Usuario'])) {

        login($_POST['Usuario'], null, $_POST['secreto'], null);
        
    }

?>