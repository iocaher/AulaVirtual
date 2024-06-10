<?php

require '../procesos/dbcon.php';

?>

<!DOCTYPE html>
<meta charset="UTF-8">

    <head>
        <title> Registro </title>
        <link rel="stylesheet" href="../css/estilos.css?v=1.0">
    </head>

    <body>

        <div class="container">

            <div class="registro">
                <H1> Registrarse </H1>

                <form id="registro" action="#" method="POST">

                    <label id="campos"> Nombre de Usuario </label> <BR>
                    <input type="text" name="Usuario" placeholder="usuario" size="35" maxlength="7" required><br><br>

                    <label id="campos"> Email </label> <BR>
                    <input type="text" id="email" name="email" placeholder="email" size="35" maxlength="255" required><br><br>

                    <label id="campos"> Contraseña </label><br>
                    <input type="password" name="secreto" size="35" maxlength="30" required><br><br>

                    <label id="campos"> Tipo de usuario </label><br><br>
                    <input type="radio" id='rol_profesor' name="rol" value="profesor" checked>

                    <label id="seleccion"> Profesor</label> <span></span>
                    <input type="radio" id='rol_alumno' name="rol" value="alumno"> 
                    <label id="seleccion"> Alumno</label> <br><br>

                    <div id='oculto'>
                        <label id="campos"> Introduce clave única del instituto </label><br>
                        <div id="errorMensaje" style="color: red; display: none;">Código introducido no válido.</div>
                        <input type="password" name="clavecole" size="35" maxlength="20"><br><br>
                    </div>

                    <input type="submit" value="Registrarme">
                    <a href="login.php"> Ya estoy registrado </a>
                </form>
            </div>
        </div>
        <script src="../js/registro.js"> </script>
    </body>
    
</html>

<?php

    if (isset($_POST['Usuario'])) {

        login($_POST['Usuario'], $_POST['email'], $_POST['secreto'], $_POST['rol']);
    }

?>