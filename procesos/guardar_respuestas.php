<?php

require 'dbcon.php';

// Configuración de la base de datos
$conn = conexionBD();

// Verificar conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

    $id_alumno = $_SESSION['usuario'];

    foreach ($_POST['respuesta'] as $id_preg => $respuesta) {
        $id_preg = mysqli_real_escape_string($conn, $id_preg);
        $respuesta = mysqli_real_escape_string($conn, $respuesta);
        $sql = "INSERT INTO cuestionario (id_preg, id_alumno, respuesta) VALUES ('$id_preg', '$id_alumno', '$respuesta')";
    
        if (!mysqli_query($conn, $sql)) {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        else {
            header('Location: ../web/asignaturas.php');
        }
    }

?>