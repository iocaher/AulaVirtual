<?php

require 'dbcon.php';

$conexion = conexionBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_exam = $_POST['id_exam'];
    $id_alumno = $_POST['id_alumno'];
    $correcciones = $_POST['correcto'];

    print_r($id_alumno);
    foreach ($correcciones as $id_preg => $correcta) {
        
        //Inserta los resultados de la corrección en el apartado cuestionario.
        $sql_update = "UPDATE cuestionario SET correcta = '$correcta' WHERE id_preg = '$id_preg' AND id_alumno = '$id_alumno'";
        mysqli_query($conexion, $sql_update);
    }

    header('Location: ../web/listadoprofe.php?success=true&matricula='. $_SESSION["matricula"] . '');
}
?>
