<?php
    require '../procesos/dbcon.php';
    $_SESSION['examen'] = $_POST['Examen'];
?>

<html>
    <head>
        <title> Creando preguntas... </title>
        <link rel="stylesheet" href="../css/estilos.css?v=1.0">
        <script src="../js/examen.js"></script>
    </head>

    <body>
        <div class="container">
            <div class="examens">

                <h1 id="titex"> <?php echo $_SESSION['examen']; ?> </h1>

                <form action="../procesos/ejecucionExamen.php" method="POST">
                    
                    <div id="preguns">
                        
                        
                        
                    </div>
                    <br><a onclick="anadirPregunta()"> Añadir Pregunta </a><span id="spanpag"></span>
                    
                    <input type="submit" value="Crear Examen">
                </form>

            </div>
        </div>
    </body>
</html>