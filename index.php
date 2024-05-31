<?php
session_start();

$rol = $_SESSION['tipo'];

if ($rol == 'alumno') {   


echo '<html>
<meta charset="UTF-8">
<head>
        <title> Página del Alumno </title>
        <link rel="stylesheet" href="css/estilos.css?v=1.0">
    </head>

    <body>
    <div class="container">
        <div class="PaginaUsu">
            <H2> Hola, ' . $_SESSION["usuario"] . ': ¿qué desea hacer? </H2> <br>           
            <div class=botones>

                    <p> <a id="examsel" href="web/asignaturas.php"> Asignaturas </a> <span id="spanpag"></span>

                    <a href="web/listadoalum.php"> Consultar notas </a> </p><br>


                    <p> <a href="web/informacion.php"> Información </a>
                    
                        <a id="logout" href="web/login.php"> Cerrar sesión </a> </p>
        </div>
    </div>    
    </body>
</html>';
}

else if ($rol == 'profesor') {

    echo '<html>
    <meta charset="UTF-8">
    <head>
            <title> Página del Profesor </title>
            <link rel="stylesheet" href="css/estilos.css?v=1.0">
        </head>
    
        <body>
        <div class="container">
            <div class="PaginaUsu">
                <H2> Hola, ' . $_SESSION["usuario"] . ': ¿qué desea hacer? </H2> <br>
                <div class=botones>
                    <p> <a id="examsel" href="web/asignaturas.php"> Asignaturas </a> <span id="spanpag"></span>
    
                    <a href="web/listadoprofe.php"> Corregir examenes </a> </p><br>
    
    
                    <p> <a href="web/informacion.php"> Información </a>
                    <a id="logout" href="web/login.php"> Cerrar sesión </a> </p>
                </div>
            </div>
        </div>
        </body>
    </html>';
}
else {
    
    header('location:web/login.php?success=false');
}
?>