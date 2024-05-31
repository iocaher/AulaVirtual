<?php
    require '../procesos/dbcon.php';
?>

<html>

<meta charset="UTF-8">
    <head>
    <title> Examenes </title>
    
    <link rel="stylesheet" href="../css/estilos.css?v=1.0">
    <script src="../js/consultar.js"> </script>
    
    </head>

    <body>
        <div class="container">

            <div class="examens">

            <?php

                if(isset($_GET['matricula'])) {

                    if(isset($_POST['examen_alumno'])){

                        $examen_alumno = $_POST['examen_alumno'];

                        // Separar los valores del id_exam y id_alumno
                        list($id_exam, $id_alumno) = explode('_', $examen_alumno);
                        examenConsultar($id_alumno, $id_exam);
                    }
                    else{

                    $_SESSION['matricula'] = $_GET['matricula'];

                    listadoMatricula($_GET['matricula']);
                    
                    echo '<br><br><br>';

                    listadoExamenesRealizados($_SESSION['matricula']);

                    echo'<input type="submit" value="Consultar">';

                    }
                        echo'<a href="../web/listadoalum.php"> Volver </a>';
                }
                else{
                    $comprobante = comprobarAsig($_SESSION['usuario']);

                    if($comprobante == 1) {

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
                        listadoAsignaturasAC();
                        

                        //echo listadoAsignaturas();

                    }
            
                

                    echo '<br><br><div align="right" ><a href="../index.php"> Volver </a></div>';                    



                }
            ?>
