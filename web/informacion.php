<?php

    require '../procesos/dbcon.php';

?>

<html>

<meta charset="UTF-8">
    <head>
    <title> Información extra </title>
    <link rel="stylesheet" href="../css/estilos.css?v=1.0">
    <script src="../js/perfil.js"> </script>
    </head>

    <body>

        <div class="container">

            <?php plantillaPerfil($_SESSION['usuario'], $_SESSION['tipo']) ?>

        </div>
    </body>

</html>