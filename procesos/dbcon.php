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

function comprobarAsig($usuario) {

    $conexion = conexionBD();

    $sql = "SELECT id 
            FROM asignaturas 
            WHERE usuario = '$usuario';"; 

    $result = mysqli_query($conexion, $sql);

    //Si el resultado da alguna coincidencia, se crea la variable de sesion del usuario y se redirige a la pagina principal
        if (mysqli_num_rows($result) >= 1) {
            return 1;
        }
        else {
            return 0;
        }
}

function crearAsignatura($nombre) {
    $conexion = conexionBD();

    $sql = "INSERT INTO asignaturas (id, nombre, usuario) VALUES(NULL, '$nombre', '".$_SESSION['usuario']."');";

    $result = mysqli_query($conexion, $sql);
    header('Location: asignaturas.php');
}

function listadoAsignaturasP() {


    echo "<H2> Hola, " . $_SESSION['usuario'] . ", estas son las asignaturas que imparte </H2>";
    $conexion = conexionBD();

    $sql = "SELECT id, nombre 
            FROM asignaturas 
            WHERE usuario = '".$_SESSION['usuario']."';"; 

    $result = mysqli_query($conexion, $sql);

    //Inicializamos la etiqueta para que se ordenen en fila en ese contenedor
    $lineaDeBotones = '<p>';

    // Bucle que recorre todo el array de resultados de la sentencia anterior y lo escribe
    while ($row = mysqli_fetch_assoc($result)) {
        //Creamos la URL para que tenga los datos que nos hacen falta para los GET: la fecha de registro y el nombre del nodo a mostrar
        $url = 'asignaturas.php?matricula=' . $row['id'];
        //Escribimos la etiqueta para que cada botón rediriga a la URL especificada arriba, y como nombre visible de este botón
        //Será el nombre del nodo recogido anteriormente.
        $lineaDeBotones .= '<a href="' . $url . '">' . $row['nombre'] . '</a><br><br><br>';
    }
    //Y cerramos el contenedor del botón.
    $lineaDeBotones .= '</p><br>';

    echo $lineaDeBotones;
}

function comprobarAsigAlum($usuario) {

    $conexion = conexionBD();

    $sql = "SELECT id_alumno 
            FROM matricula
            WHERE id_alumno = '$usuario';"; 

    $result = mysqli_query($conexion, $sql);

    //Si el resultado da alguna coincidencia, se crea la variable de sesion del usuario y se redirige a la pagina principal
        if ($result) {
            return 1;
        }
        else {
            return 0;
        }
}

function listadoAsignaturas() {

    $conexion = conexionBD();

    $alumno = $_SESSION['usuario'];

    $sql = "SELECT asignaturas.id, asignaturas.nombre 
        FROM asignaturas
        LEFT JOIN matricula ON asignaturas.id = matricula.id_asig
        WHERE matricula.id_alumno != '$alumno' OR matricula.id_alumno IS NULL;"; 

    $result = mysqli_query($conexion, $sql);

    //Inicializamos la etiqueta para que se ordenen en fila en ese contenedor
    echo "<h2> Listado de Asignaturas donde no estás matriculado </h2>";
    $lineaDeBotones = '<p>';

    // Bucle que recorre todo el array de resultados de la sentencia anterior y lo escribe
    while ($row = mysqli_fetch_assoc($result)) {
        //Creamos la URL para que tenga los datos que nos hacen falta para los GET: la fecha de registro y el nombre del nodo a mostrar
        $url = 'asignaturas.php?asignatura=' . $row['id'];
        //Escribimos la etiqueta para que cada botón rediriga a la URL especificada arriba, y como nombre visible de este botón
        //Será el nombre del nodo recogido anteriormente.
        $lineaDeBotones .= '<a href="' . $url . '">' . $row['nombre'] . '</a><br><br><br>';
    }
    //Y cerramos el contenedor del botón.
    $lineaDeBotones .= '</p><br>';

    echo $lineaDeBotones;
    

    if(isset($_GET['asignatura'])) {

        echo '<h2> ¿Vas a matricularte en esta asignatura? </h2>';

        $asignaturaSeleccionada = $_GET["asignatura"];
        echo "<form action='' method='POST'>";
        echo '<input type="hidden" name="asignaturaSeleccionada" value="' . $asignaturaSeleccionada . '">
        
        <input type="submit" value="Matricularme">
        <a href="asignaturas.php"> Volver </a>
        </form>';
        if(isset($_POST['asignaturaSeleccionada'])) {

            $conexion = conexionBD();
            $alum = $_SESSION['usuario'];
            $asig = $_POST['asignaturaSeleccionada'];

            $sql = "INSERT INTO matricula(id_alumno, id_asig, curso) VALUES('$alum', '$asig', 2024);";

            $result = mysqli_query($conexion, $sql);
            header('Location: asignaturas.php');
            
        }
    }
    else {
        
    }
}

function listadoAsignaturasA() {

    $conexion = conexionBD();

    $alumno = $_SESSION['usuario'];

    $sql = "SELECT matricula.id_asig, asignaturas.nombre 
            FROM matricula
            LEFT JOIN asignaturas ON matricula.id_asig = asignaturas.id
            WHERE matricula.id_alumno = '$alumno';"; 

    $result = mysqli_query($conexion, $sql);

    //Inicializamos la etiqueta para que se ordenen en fila en ese contenedor
    $lineaDeBotones = '<p>';

    // Bucle que recorre todo el array de resultados de la sentencia anterior y lo escribe
    while ($row = mysqli_fetch_assoc($result)) {
        //Creamos la URL para que tenga los datos que nos hacen falta para los GET: la fecha de registro y el nombre del nodo a mostrar
        $url = 'asignaturas.php?matricula=' . $row['id_asig'];
        //Escribimos la etiqueta para que cada botón rediriga a la URL especificada arriba, y como nombre visible de este botón
        //Será el nombre del nodo recogido anteriormente.
        $lineaDeBotones .= '<a href="' . $url . '">' . $row['nombre'] . '</a><br><br><br>';
    }
    //Y cerramos el contenedor del botón.
    $lineaDeBotones .= '</p><br>';

    echo $lineaDeBotones;
}

function listadoMatricula($asignatura) {

    $conexion = conexionBD();

    $usuario = $_SESSION['usuario'];

    $sql = "SELECT matricula.id_asig, asignaturas.nombre, matricula.id_alumno, asignaturas.usuario 
            FROM matricula
            LEFT JOIN asignaturas ON matricula.id_asig = asignaturas.id
            WHERE matricula.id_asig = '$asignatura';"; 

    $result = mysqli_query($conexion, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $consulta[] = $row;
    }

    echo "<h1> ".$consulta[0]['nombre']." </h1>";
    echo "<a> Profesor: " . $consulta[0]['usuario'] . "</a><span id='spanpag'><br></span>";

    echo '<div class="dropdown">
            <a> Listado de Alumnos </a>

            <div class="dropdown-content">';
            foreach($consulta as $alumnado) {
                echo "<p>" . $alumnado['id_alumno']. "</p>";
            }
                    
        echo '</div>
    </div>';
}





?>