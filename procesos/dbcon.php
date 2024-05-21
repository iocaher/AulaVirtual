<?php

session_start();

function conexionBD() {

    $host="127.0.0.1";
    $usuario="root";
    $pass="";
    $nom_db = "aulavirtual";

    //Se le pasa los parámetros para la conexión con MySQL
    $conexion = mysqli_connect($host, $usuario, $pass);
    mysqli_select_db($conexion, $nom_db);

    // Control de errores
    if(!$conexion){
        echo "<script>console.log('Fallo en la conexión');</script>";;
    }
    else {
        return $conexion;
    }

}

function login($usuario, $email, $pass, $tipo) {

    //Pasamos a la función los 3 datos del registro, y, si el usuario es null (ya que se inicia sesion con el email)
    // Entonces detecta como un inicio de sesion en lugar de un registro
    $conexion = conexionBD();

    //Si es null = login
    if ($email == null) {

        $sql = "SELECT ID, rol 
                FROM usuarios 
                WHERE ID = '$usuario' AND contraseña = '$pass';"; 

    $result = mysqli_query($conexion, $sql);

    //Si el resultado da alguna coincidencia, se crea la variable de sesion del usuario y se redirige a la pagina principal
    if ($result) {
        $_SESSION['usuario'] = $usuario;

        $fila = mysqli_fetch_assoc($result);

        $_SESSION['tipo'] = $fila['rol'];

        header("Location: ../index.php");
    }
    //Si no, devuelve a la pagina principal sin mas
    else {
        $_SESSION['usuario'] = 'incorrecto';
        header("Location: ../index.php");    
    }

    }
    //Si usuario != null, entonces lo interpreta como un registro y lo añade a la base de datos.
    else {

        $sql = "INSERT INTO usuarios(email, ID, contraseña, rol) VALUES('$email', '$usuario', '$pass', '$tipo')";
        $result = mysqli_query($conexion, $sql);

        if($result) {
            $_SESSION['usuario'] = $usuario;
            header("Location: ../index.php");
        }
        
        
    }

}


?>