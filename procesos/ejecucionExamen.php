<?php
require 'dbcon.php';

// Configuración de la base de datos
$conn = conexionBD();

// Verificar conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Verificar si el método de la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el nombre del examen y las preguntas del formulario
    $nombre_examen = mysqli_real_escape_string($conn, $_SESSION['examen']);
    $preguntas = $_POST['preguntas'];

    // Insertar el examen en la base de datos
    $sql = "INSERT INTO examenes (nombre, id_asig) VALUES ('$nombre_examen', " . $_SESSION['matricula'] . ")";
    if (mysqli_query($conn, $sql)) {
        // Obtener el ID del examen recién insertado
        $id_examen = mysqli_insert_id($conn);

        // Iterar sobre las preguntas y añadirlas a la base de datos
        foreach ($preguntas as $pregunta) {
            echo $pregunta['texto'];
            $titulo = mysqli_real_escape_string($conn, $pregunta['texto']);

            // Insertar la pregunta
            $sql = "INSERT INTO preguntas (id_examen, titulo) VALUES ($id_examen, '$titulo')";
            if (mysqli_query($conn, $sql)) {
                // Obtener el ID de la pregunta recién insertada
                $id_preg = mysqli_insert_id($conn);

                // Si la pregunta es de tipo 'test', insertar las opciones de respuesta
                if (isset($pregunta['opciones'])) {
                    foreach ($pregunta['opciones'] as $opcion) {
                        $texto_opcion = mysqli_real_escape_string($conn, $opcion['texto']);
                        $correcta = isset($opcion['correcta']) ? 1 : 0;

                        // Insertar la opción de respuesta
                        $sql = "INSERT INTO respuestas (id_preg, respuesta) VALUES ('$id_preg', '$texto_opcion')";
                        mysqli_query($conn, $sql);
                    }
                }
            }
        }

        header('location:../web/asignaturas.php?success=true');
    } else {
        echo "Error al guardar el examen: " . mysqli_error($conn);
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>