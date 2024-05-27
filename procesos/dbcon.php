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

    //Pasamos a la función los 3 datos del registro, y, si el email es null (ya que se inicia sesion con el usuario)
    // Entonces detecta como un inicio de sesion en lugar de un registro
    $conexion = conexionBD();

    //Si es null = login
    if ($email == null) {

        //Hacemos la consulta que busque concordancias entre los datos de login introducidos y los existentes
        $sql = "SELECT ID, rol 
                FROM usuarios 
                WHERE ID = '$usuario' AND contraseña = '$pass';"; 

    $result = mysqli_query($conexion, $sql);

    //Si el resultado da alguna coincidencia, se crea la variable de sesion del usuario y se redirige a la pagina principal
    if ($result) {
        $_SESSION['usuario'] = $usuario;

        $fila = mysqli_fetch_assoc($result);

        //Y se asigna a la sesion el tipo de usuario que es
        $_SESSION['tipo'] = $fila['rol'];

        header("Location: ../index.php");
    }
    //Si no, devuelve a la pagina principal sin mas
    else {
        header("Location: ../index.php");    
    }

    }
    //Si email != null, entonces lo interpreta como un registro y lo añade a la base de datos.
    else {

        $sql = "INSERT INTO usuarios(email, ID, contraseña, rol) VALUES('$email', '$usuario', '$pass', '$tipo')";
        $result = mysqli_query($conexion, $sql);

        if($result) {
            //Inicio de sesion automatico al registrarse.
            $_SESSION['usuario'] = $usuario;
            $_SESSION['tipo'] = $tipo;

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

    //Si el resultado da al menos 1 resultado, devuelve 1, de lo contrario, devuelve 0 (explicado el por qué en su sección)
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

    //Inserta en la tabla asignaturas la asignatura creada por el usuario profesor.
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
        //Creamos la URL para que tenga los datos que nos hacen falta para los GET: la matricula, que es la asignatura impartida
        $url = 'asignaturas.php?matricula=' . $row['id'];
        //Escribimos la etiqueta para que cada botón rediriga a la URL especificada arriba, y como nombre visible de este botón
        //Será el nombre de la asignatura recogida anteriormente.
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

    //Si el resultado da al menos una coincidencia, devuelve 1, si no, devuelve 0
    //Esta funcion sirve para que muestre o no un apartado
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

    //Buscamos las asignaturas que no estén registradas en la matricula del alumno en cuestion
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
        //Creamos la URL para que tenga los datos que nos hacen falta para los GET: la matricula, que es la asignatura impartida
        $url = 'asignaturas.php?asignatura=' . $row['id'];
        //Escribimos la etiqueta para que cada botón rediriga a la URL especificada arriba, y como nombre visible de este botón
        //Será el nombre de la asignatura recogida anteriormente.
        $lineaDeBotones .= '<a href="' . $url . '">' . $row['nombre'] . '</a><br><br><br>';
    }
    //Y cerramos el contenedor del botón.
    $lineaDeBotones .= '</p><br>';

    echo $lineaDeBotones;
    

    //Para cuando seleccione una asignatura
    if(isset($_GET['asignatura'])) {

        echo '<h2> ¿Vas a matricularte en esta asignatura? </h2>';

        $asignaturaSeleccionada = $_GET["asignatura"];
        echo "<form action='' method='POST'>";
        echo '<input type="hidden" name="asignaturaSeleccionada" value="' . $asignaturaSeleccionada . '">
        
        <input type="submit" value="Matricularme">
        <a href="asignaturas.php"> Volver </a>
        </form>';
        //Formulario que registra y matricula al alumno en una asignatura
        if(isset($_POST['asignaturaSeleccionada'])) {

            $conexion = conexionBD();
            $alum = $_SESSION['usuario'];
            $asig = $_POST['asignaturaSeleccionada'];

            //Insert para registrar esa matricula
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
                //Creamos la URL para que tenga los datos que nos hacen falta para los GET: la matricula, que es la asignatura impartida
        $url = 'asignaturas.php?matricula=' . $row['id_asig'];
        //Escribimos la etiqueta para que cada botón rediriga a la URL especificada arriba, y como nombre visible de este botón
        //Será el nombre de la asignatura recogida anteriormente.
        $lineaDeBotones .= '<a href="' . $url . '">' . $row['nombre'] . '</a><br><br><br>';
    }
    //Y cerramos el contenedor del botón.
    $lineaDeBotones .= '</p><br>';

    echo $lineaDeBotones;
}

function listadoMatricula($asignatura) {

    $conexion = conexionBD();

    $usuario = $_SESSION['usuario'];

    //Buscamos las asignaturas del alumno que esté matriculado en estas y las listamos
    $sql = "SELECT matricula.id_asig, asignaturas.nombre, matricula.id_alumno, asignaturas.usuario 
            FROM matricula
            LEFT JOIN asignaturas ON matricula.id_asig = asignaturas.id
            WHERE matricula.id_asig = '$asignatura';"; 

    $result = mysqli_query($conexion, $sql);

    //Guardamos el resultado en un array asociativo
    while ($row = mysqli_fetch_assoc($result)) {
        $consulta[] = $row;
    }

    //Muestra la asignatura, el profesor que la imparte y un desplegable con el resultado de alumnos matriculados en esa asignatura
    echo "<h1> ".$consulta[0]['nombre']." </h1>";
    echo "<a> Profesor: " . $consulta[0]['usuario'] . "</a><span id='spanpag'></span>";

    echo '<div class="dropdown">
            <a> Listado de Alumnos </a>

            <div class="dropdown-content">';
            foreach($consulta as $alumnado) {
                echo "<p>" . $alumnado['id_alumno']. "</p>";
            }
                    
        echo '</div>
    </div>';
}

function listadoExamenes($asignatura) {

    $conexion = conexionBD();

    $usuario = $_SESSION['usuario'];

    //Recogemos el id del examen y su nombre, que concuerde con la asignatura seleccionada
    $sql = "SELECT id_exam, nombre
            FROM examenes
            WHERE id_asig = '$asignatura';"; 

    $result = mysqli_query($conexion, $sql);

    //Si hay al menos 1 resultado, crea un formulario con de tipo radio para que el alumno seleccione el examen a realizar
    if (mysqli_num_rows($result) > 0) {
        echo '<form method="post" action="../web/cuestionario.php">';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div>';
            echo '<input type="radio" id="exam_' . $row['id_exam'] . '" name="id_exam" value="' . $row['id_exam'] . '">';
            echo '<label for="exam_' . $row['id_exam'] . '">' . $row['nombre'] . '</label>';
            echo '<input type="hidden" name="nombre_exam" value="' . $row['nombre'] . '">';
            echo '</div>';
        }
        echo '<br><input type="submit" value="Enviar"></input>';
        echo '</form>';
    } else {
        echo "No se encontraron exámenes para esta asignatura.";
    }

}

function examenDesplegar() {

    $conexion = conexionBD();

    $usuario = $_SESSION['usuario'];
    $id_exam = $_SESSION['examen'];

    //Buscamos las preguntas que existan en la base de datos con el id del examen asociado
    $sql = "SELECT *
            FROM preguntas
            WHERE id_examen = '$id_exam';"; 

    $result = mysqli_query($conexion, $sql);
    //Guardamos como array asociativo las preguntas para operar con ellas luego
    $preguntas = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($preguntas as $pregunta) {
        echo "<div>"; //Por cada pregunta escribimos su titulo
        echo "<h3>" . $pregunta['titulo'] . "</h3>";

        //Y hacemos una consulta que recoja las respuestas a esas preguntas
        $id_preg = $pregunta['id_preg'];
        $sql_resp = "SELECT * FROM respuestas WHERE id_preg = $id_preg";
        $result_resp = mysqli_query($conexion, $sql_resp);
        $respuestas = mysqli_fetch_all($result_resp, MYSQLI_ASSOC);

        //Si la pregunta contiene 0 respuestas, significa que era una pregunta de desarrollo, y si tiene más, era una respuesta de
        //seleccion tipo test.
        if (count($respuestas) > 0) {
            foreach ($respuestas as $respuesta) {
                echo "<label>";
                echo "<input type='radio' name='respuesta[$id_preg]' value='" . $respuesta['respuesta'] . "'>";
                echo $respuesta['respuesta'];
                echo "</label>";
            }
        } else {
            echo "<input type='text' name='respuesta[$id_preg]'>";
        }
        echo "</div>";
    }
}


?>