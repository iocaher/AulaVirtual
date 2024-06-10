<?php
    require '../procesos/dbcon.php';
    if($_SESSION['tipo'] != 'admin') {
        header('location: ../index.php');
    }
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


                echo listadoExamenesRealizados(null);

                echo '<br><br><a href="../index.php"> Volver </a>';
                
                
            ?>
