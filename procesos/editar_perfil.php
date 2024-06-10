<?php

require 'dbcon.php';

$conexion = conexionBD();

$ID = $_SESSION['usuario'];
$nombre = $_POST['name'];
$apellidos = $_POST['surname'];
$email = $_POST['email'];
$dni = $_POST['dni'];
$telefono = $_POST['phone'];
$nacimiento = $_POST['birthdate'];
$codigo_postal = $_POST['postalcode'];
$direccion = $_POST['address'];
$ciudad = $_POST['city'];
$provincia = $_POST['province'];


//Condicion para controlar que se introduzca en alumno o en profesor los datos del formulario
if ($_SESSION['tipo'] == 'alumno') {

    $sql = "UPDATE alumnos SET nombre='$nombre', apellidos='$apellidos', email='$email', DNI='$dni', telefono=$telefono, nacimiento='$nacimiento', codigo_postal=$codigo_postal, direccion='$direccion', ciudad='$ciudad', provincia='$provincia' WHERE ID='$ID'";
}

else {

    $sql = "UPDATE profesores SET nombre='$nombre', apellidos='$apellidos', email='$email', DNI='$dni', telefono=$telefono, nacimiento='$nacimiento', codigo_postal=$codigo_postal, direccion='$direccion', ciudad='$ciudad', provincia='$provincia' WHERE ID='$ID'";
    
}

$result = mysqli_query($conexion, $sql);

header('location: ../web/informacion.php');
?>