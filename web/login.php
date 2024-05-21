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
        <link rel="stylesheet" href="../css/estilos.css">
    </head>

    <body>

        <div class="container">
            <div class="login">
                <H1> Conectarse </H1>
                <form id="formulario" action="#" method="POST">

                    <label id="campos"> Nombre de Usuario </label> <BR>
                    <input type="text" name="Usuario" placeholder="usuario" size="28" required><br><br>

                    <label id="campos"> Contraseña </label><br>
                    <input type="password" name="secreto" size="28" required><br><br>

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