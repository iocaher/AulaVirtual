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
                if (isset($_GET['success']) && $_GET['success'] == 'true') {
                    echo '<div class="success-message"><span>&#10004;</span> Examen creado exitosamente.</div>';
                }
                if($_SESSION['tipo'] == 'profesor') {

                    if(isset($_GET['matricula'])) {
                        
                        $_SESSION['matricula'] = $_GET['matricula'];

                        listadoMatricula($_GET['matricula']);
                        echo '<br><br><br>
                            <h2> Herramienta de creación del examen </h2>';
                        echo'<form action="examenes.php" method="POST">

                            <label id="campos"> Título del Examen </label>
                            <input type="text" name="Examen" placeholder="Trimestral 1" required><br><br>

                            <input type="submit" value="Comenzar">
                            <a href="../web/asignaturas.php"> Volver </a>';
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
                }
                else {
                    if(isset($_GET['matricula'])) {
                        
                        listadoMatricula($_GET['matricula']);

                        echo '<h2> Examenes de esta asignatura </h2>';
                        listadoExamenes($_GET['matricula']);
                        echo '<br><br><div align="right" ><a href="../index.php"> Volver </a></div>';
                    }
                    else{

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
                        echo '<br><br><div align="right" ><a href="../index.php"> Volver </a></div>';
                    }
                    else {
                        echo "<H2> Hola, " . $_SESSION['usuario'] . ", estas son las asignaturas en las que está matriculado </H2>";
                        listadoAsignaturasA();
                        

                        echo listadoAsignaturas();

                        echo '<br><br><div align="right" ><a href="../index.php"> Volver </a></div>';
                    }
                }
            }
                ?>

                </div>
        </div>
    </body>

</html>