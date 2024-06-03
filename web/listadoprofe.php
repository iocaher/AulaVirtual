<?php
    require '../procesos/dbcon.php';
?>

<html>

<meta charset="UTF-8">
    <head>
    <title> Examenes </title>
    
    <link rel="stylesheet" href="../css/estilos.css?v=1.0">
    
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
                        examenCorregir($id_alumno, $id_exam);
                    }
                    else{

                    $_SESSION['matricula'] = $_GET['matricula'];
                    if (isset($_GET['success']) && $_GET['success'] == 'true') {
                        echo '<div class="success-message"><span>&#10004;</span> Examen corregido exitosamente.</div>';
                    }

                    listadoMatricula($_GET['matricula']);
                    
                    echo '<br><br><br>';

                    listadoExamenesRealizados($_SESSION['matricula']);

                    echo'<input type="submit" value="Corregir">';

                    }
                        echo'<a href="../web/listadoprofe.php"> Volver </a>';
                }
                else{
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

                }
            
                echo listadoAsignaturasPC();
                
                    



                }
            ?>
