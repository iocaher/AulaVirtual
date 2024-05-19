<?php
session_start();

$rol = $_SESSION['tipo'];

if ($rol == 'alumno') {   


echo '<html>
<meta charset="UTF-8">
<head>
        <title> Página del Alumno </title>
        <link rel="stylesheet" href="css/estilos.css">
    </head>

    <body>
    <div class="container">
        <div class="PaginaUsu">
            <H2> Hola, <?php echo $_SESSION["Usuario"]; ?>: ¿qué desea hacer? </H2> <br>           
            <div class=botones>

                    <p> <a id="examsel" href="ListadoExamen.php"> Examen </a> <span id="spanpag"></span>

                    <a href="NotasAlumnos.php"> Consultar notas </a> </p><br>


                    <p> <a href="ExtraAlum.php"> Información </a>
                    
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
            <link rel="stylesheet" href="../css/estilos.css">
        </head>
    
        <body>
        <div class="container">
            <div class="PaginaUsu">
                <H2> Hola, <?php echo $_SESSION["Usuario"]; ?>: <br> ¿qué desea hacer? </H2> <br>
                <div class=botones>
                    <p> <a id="examsel" href="Examen.php"> Examen </a> <span id="spanpag"></span>
    
                    <a href="ListadoAlumnos.php"> Consultar notas </a> </p><br>
    
    
                    <p> <a href="ExtraProf.php"> Información </a>
                    <a id="logout" href="../Procesos/Logout.php"> Cerrar sesión </a> </p>
                </div>
            </div>
        </div>
        </body>
    </html>';
}
else {
    
    header('location:web/login.php');
}
?>