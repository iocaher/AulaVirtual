<?php
    require '../procesos/dbcon.php';
?>

<html>

<meta charset="UTF-8">
    <head>
    <title> Examenes </title>
    
    <link rel="stylesheet" href="../css/estilos.css">
    
    </head>

    <body>
        <div class="container">

            <div class="examens">

            <?php

                if($_SESSION['tipo'] == 'profesor') {

                    $comprobante = comprobarAsig($_SESSION['usuario']);

                    if($comprobante == 0) {

                        echo '<H2> Hola ' . $_SESSION["usuario"] . ', parece que no imparte ninguna asignatura aún </H2> <br>
                        <form action="#" method="POST">

                        <label id="campos"> Nombre de la asignatura </label>
                        <input type="text" name="Asignatura" placeholder="Asignatura" required><br><br>

                        <input type="submit" value="Comenzar">
                        <a href="../index.php"> Volver </a>
                        </form>';

                        if (isset($_POST['Asignatura'])) {

                            crearAsignatura($_POST['Asignatura']);
                        }
                        else {
                            
                        }
                    }

                    else {

                    
                
                    echo listadoAsignaturasP() .'

                        <H2> ¿Quieres impartir otra asignatura más? </H2>
                        <form action="#" method="POST">

                        <label id="campos"> Nombre de la asignatura </label>
                        <input type="text" name="Asignatura" placeholder="Asignatura" required><br><br>

                        <input type="submit" value="Comenzar">
                        <a href="../index.php"> Volver </a>
                        </form>';

                        if (isset($_POST['Asignatura'])) {

                            crearAsignatura($_POST['Asignatura']);
                        }
                        else {
                            
                        }

                    }
                }
                else {
                    $comprobante = comprobarAsigAlum($_SESSION['usuario']);

                    if($comprobante == 0) {
                        
                        echo "<H2> Hola " . $_SESSION['usuario'] . ", parece que no está matriculado en ninguna asignatura.
                        <br>Elija una del listado a continuación </H2> <br>
                        <form action='' method='POST'>";
                        echo listadoAsignaturas();

                        if (isset($_POST['Asignatura'])) {

                            crearAsignatura($_POST['Asignatura']);
                        }
                        else {
                            
                        }
                    }
                    else {
                        echo "<H2> Hola, " . $_SESSION['usuario'] . ", estas son las asignaturas en las que está matriculado </H2>";
                        listadoAsignaturasA();
                        echo '<br><br><div align="right" ><a href="../index.php"> Volver </a></div>';

                        echo listadoAsignaturas();
                    }
                }
                ?>

                </div>
        </div>
    </body>

</html>