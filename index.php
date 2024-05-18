<?php

session_start(); #Guardar el usuario para las siguientes páginas

$_SESSION['Alumno']=$_SESSION['Usuario']; #Guardamos la sesión del usuario que recibe del login como sesion de alumno

if ($_SESSION['tipo'] == 'alumnos') {   


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
                    
                         <a id="logout" href="../Procesos/Logout.php"> Cerrar sesión </a> </p>
        </div>
    </div>    
    </body>
</html>';
}

else if ($_SESSION['tipo'] == 'profesor') {

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
    header('location: web/login.php');
}
?>