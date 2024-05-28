<?php

require 'dbcon.php';

$conexion = conexionBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_exam = $_POST['id_exam'];
    $id_alumno = $_POST['id_alumno'];
    $correcciones = $_POST['correcto'];

    print_r($id_alumno);
    foreach ($correcciones as $id_preg => $correcta) {
        // Procesa cada corrección aquí
        // Por ejemplo, podrías actualizar la base de datos con las correcciones
        $sql_update = "UPDATE cuestionario SET correcta = '$correcta' WHERE id_preg = '$id_preg' AND id_alumno = '$id_alumno'";
        mysqli_query($conexion, $sql_update);
    }

    echo "Correcciones guardadas exitosamente.";
}
?>
