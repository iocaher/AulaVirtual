<?php
    require '../procesos/dbcon.php';
    $_SESSION['examen'] = $_POST['id_exam'];
    $_SESSION['nombre_ex'] = $_POST['nombre_exam'];
?>

<html>
    <head>
        <title> Mostrando examen... </title>
        <link rel="stylesheet" href="../css/estilos.css?v=1.0">
        <script src="../js/examen.js"></script>
    </head>

    <body>
        <div class="container">
            <div class="examens">

                <h1 id="titex"> <?php echo $_SESSION['nombre_ex']; ?> </h1>

                <form method="post" action="../procesos/guardar_respuestas.php">
                    
                    <div id="preguns">
                        
                        <?php examenDesplegar()?>
                        
                    </div>
                    <br>
                    <input type="submit" value="Terminar examen">
                    <div align="right"><a href="asignaturas.php"> Volver </a></div>
                </form>

            </div>
        </div>
    </body>
</html>