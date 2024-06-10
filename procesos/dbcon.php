<?php

session_start();

//Función comodín que guardará los datos de la base de datos y enviará el resultado de la conexión a las diferentes funciones.
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

//Función que sirve para controlar el login y el registro dependiendo de los valores que pase.
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
    if (mysqli_num_rows($result) >= 1) {
        $_SESSION['usuario'] = $usuario;

        $fila = mysqli_fetch_assoc($result);

        //Y se asigna a la sesion el tipo de usuario que es
        $_SESSION['tipo'] = $fila['rol'];

        header("Location: ../index.php");
    }
    //Si no, devuelve a la pagina principal con un false.
    else {
        header("Location: ../index.php?login=false");    
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

//Función que devuelve true o false de la comprobación por si no existe ninguna asignatura para x profesor.
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

//Función que sirve para crear la asignatura para un profesor en concreto.
function crearAsignatura($nombre) {
    $conexion = conexionBD();

    $sql = "INSERT INTO asignaturas (id, nombre, usuario) VALUES(NULL, '$nombre', '".$_SESSION['usuario']."');";

    //Inserta en la tabla asignaturas la asignatura creada por el usuario profesor.
    $result = mysqli_query($conexion, $sql);
    header('Location: asignaturas.php');
}

//Función que lista las asignaturas que pertenecen a un profesor
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

//Función que comprueba que un alumno esté matriculado en asignaturas.
function comprobarAsigAlum($usuario) {

    $conexion = conexionBD();

    $sql = "SELECT id_alumno 
            FROM matricula
            WHERE id_alumno = '$usuario';"; 

    $result = mysqli_query($conexion, $sql);

    //Si el resultado da al menos una coincidencia, devuelve 1, si no, devuelve 0
    //Esta funcion sirve para que muestre o no un apartado
        if (mysqli_num_rows($result) >= 1) {
            return 1;
        }
        else {
            return 0;
        }
}

//Función que muestra las asignaturas a un alumno en las que no esté matriculado
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

//Función que muestra un listado de las asignaturas en las que sí esté matriculado el alumno
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

//Función que muestra la página de la asignatura, su profesor y los alumnos matriculados
function listadoMatricula($asignatura) {

    $conexion = conexionBD();

    $usuario = $_SESSION['usuario'];

    //Buscamos las asignaturas del alumno que esté matriculado en estas y las listamos
    $sql = "SELECT nombre, usuario 
            FROM asignaturas
            WHERE id = '$asignatura';"; 

    $result = mysqli_query($conexion, $sql);

    if($result) {
        //Guardamos el resultado en un array asociativo
        while ($row = mysqli_fetch_assoc($result)) {
            $consultaAsig[] = $row;
        }

        //Muestra la asignatura, el profesor que la imparte y un desplegable con el resultado de alumnos matriculados en esa asignatura
        
        echo "<h1> ".$consultaAsig[0]['nombre']." </h1>";
        echo "<a> Profesor: " . $consultaAsig[0]['usuario'] . "</a><span id='spanpag'></span>";

                $sql2 = "SELECT id_alumno
                FROM matricula
                WHERE id_asig = '$asignatura';"; 

        $resul2 = mysqli_query($conexion, $sql2);

        if(mysqli_num_rows($resul2) >= 1) {
            $consultaMatri = array();  // Inicializar el array antes de usarlo
            while ($row2 = mysqli_fetch_assoc($resul2)) {
                $consultaMatri[] = $row2; //Guardamos en el array los alumnos matriculados en la asignatura
            }

            echo '<div class="dropdown">
                    <a> Listado de Alumnos </a>
                    <div class="dropdown-content">';
                    foreach($consultaMatri as $alumnado) { //Iteramos los alumnos que se encuentren en el desplegable del listado de alumnos.
                        echo "<p>" . $alumnado['id_alumno']. "</p>";
                    }
            echo '</div>
                </div>';
        }
    }
    
}

//Función que lista los examenes que no se han realizado por el alumno en sesión y los muestra en formulario para seleccionarlos.
function listadoExamenes($asignatura) {

    $conexion = conexionBD();

    $usuario = $_SESSION['usuario'];

    //Recogemos el id del examen y su nombre, que concuerde con la asignatura seleccionada
    $sql = "SELECT examenes.id_exam, examenes.nombre 
    FROM examenes 
    LEFT JOIN notas ON examenes.id_exam = notas.id_exam AND notas.id_alumno = '$usuario'
    WHERE examenes.id_asig = '$asignatura' AND notas.id_exam IS NULL;"; 

    $result = mysqli_query($conexion, $sql);

    //Si hay al menos 1 resultado, crea un formulario con de tipo radio para que el alumno seleccione el examen a realizar
    if (mysqli_num_rows($result) > 0) {
        echo '<form method="post" action="../web/cuestionario.php">';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div>';
            echo '<input type="radio" id="exam_' . $row['id_exam'] . '" name="id_exam" value="' . $row['id_exam'] . '">';
            echo '<label id="campos" for="exam_' . $row['id_exam'] . '">' . $row['nombre'] . '</label>';
            echo '<input type="hidden" name="nombre_exam" value="' . $row['nombre'] . '">';
            echo '</div>';
        }
        echo '<br><input type="submit" value="Enviar"></input>';
        echo '</form>';
    } else {
        echo "No se encontraron exámenes para esta asignatura.";
    }

}

//Función que despliega una plantilla del examen para que el alumno la rellene.
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

    foreach ($preguntas as $pregunta) { //Iteramos cada pregunta
        echo "<div>"; //Por cada pregunta escribimos su titulo
        echo "<label id='campos'>" . $pregunta['titulo'] . "</label><br><br><br>";

        //Y hacemos una consulta que recoja las respuestas a esas preguntas
        $id_preg = $pregunta['id_preg'];
        $sql_resp = "SELECT * FROM respuestas WHERE id_preg = $id_preg";
        $result_resp = mysqli_query($conexion, $sql_resp);
        $respuestas = mysqli_fetch_all($result_resp, MYSQLI_ASSOC);

        /*Si la pregunta contiene 0 respuestas, significa que era una pregunta de desarrollo, y si tiene más, era una respuesta de
        seleccion tipo test. */
        if (count($respuestas) > 0) {
            foreach ($respuestas as $respuesta) {
                echo "<label id='radios'>";
                echo "<input type='radio' name='respuesta[$id_preg]' value='" . $respuesta['respuesta'] . "'>";
                echo $respuesta['respuesta'];
                echo "</label><br><br>";
            }
        } else {
            echo "<input type='text' name='respuesta[$id_preg]'>";
            
        }
        echo "</div><br><hr><br>";
        
    }
    
}

//Fnción que recoge el id del alumno y del examen y lo desplegará junto a una seleccion correcta o incorrecta para que los profesores los corrijan.
function examenCorregir($alumno, $id_exam) {
    $conexion = conexionBD();

    // Obtenemos el nombre del examen
    $sql_nombre = "SELECT nombre FROM examenes WHERE id_exam = '$id_exam';";
    $result_nombre = mysqli_query($conexion, $sql_nombre);
    $examen = mysqli_fetch_assoc($result_nombre);

    echo "<h1>" . $examen['nombre'] . "</h1>";

    echo "<form action='../procesos/procesar_correccion.php' method='POST'>";

    // Buscamos las preguntas que existan en la base de datos con el id del examen asociado
    $sql = "SELECT *
            FROM preguntas
            WHERE id_examen = '$id_exam';"; 

    $result = mysqli_query($conexion, $sql);
    // Guardamos como array asociativo las preguntas para operar con ellas luego
    $preguntas = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($preguntas as $pregunta) {
        echo "<div>"; // Por cada pregunta escribimos su título
        echo "<label id='campos'>" . $pregunta['titulo'] . "</label><br><br><br>";

        // Y hacemos una consulta que recoja las respuestas a esas preguntas del cuestionario
        $id_preg = $pregunta['id_preg'];
        $sql_resp = "SELECT * FROM cuestionario WHERE id_preg = $id_preg AND id_alumno = '$alumno'";
        $result_resp = mysqli_query($conexion, $sql_resp);
        $cuestionario = mysqli_fetch_all($result_resp, MYSQLI_ASSOC);

        
        if (count($cuestionario) > 0) {
            foreach ($cuestionario as $item) {
                echo "<div id='respuestas'>";
                echo "<table>";
                echo "<tr>";
                echo "<td rowspan='2'>" . $item['respuesta'] . "</td>";
                
                //Por versión anterior no lo borraré por si acaso, pero no lo utilizamos
                $correcto_checked = ($item['correcta'] == 'SI') ? 'checked' : '';
                $incorrecto_checked = ($item['correcta'] == 'NO') ? 'checked' : '';

                echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>";
                echo "<label id='correcto'>";
                echo "<input type='radio' name='correcto[$id_preg]' value='SI' $correcto_checked> Correcto";
                echo "</label>";
                echo "</td>";
                echo "</tr>";

                echo "<tr>";
                echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>";
                echo "<label id='incorrecto'>";
                echo "<input type='radio' name='correcto[$id_preg]' value='NO' $incorrecto_checked> Incorrecto";
                echo "</label>";
                echo "</td>";
                echo "</tr>";

                echo "</table>";
                echo "</div><br><br>";
            }
        }
        echo "</div><br><hr><br>";
    }

    echo "<input type='hidden' name='id_exam' value='$id_exam'>";
    echo "<input type='hidden' name='id_alumno' value='$alumno'>";
    echo "<input type='submit' value='Enviar Corrección'>";
    echo "</form>";
}

//Función que muestra el listado de signaturas para el apartado de Corregir Examenes en profesores.
function listadoAsignaturasPC() {


    $conexion = conexionBD();

    $sql = "SELECT id, nombre 
            FROM asignaturas 
            WHERE usuario = '".$_SESSION['usuario']."';"; 

    $result = mysqli_query($conexion, $sql);

    if(mysqli_num_rows($result) >= 1) {

        echo "<H2> Hola, " . $_SESSION['usuario'] . ", estas son las asignaturas que imparte </H2>";
        //Inicializamos la etiqueta para que se ordenen en fila en ese contenedor
        $lineaDeBotones = '<p>';

        // Bucle que recorre todo el array de resultados de la sentencia anterior y lo escribe
        while ($row = mysqli_fetch_assoc($result)) {
            //Creamos la URL para que tenga los datos que nos hacen falta para los GET: la matricula, que es la asignatura impartida
            $url = 'listadoprofe.php?matricula=' . $row['id'];
            //Escribimos la etiqueta para que cada botón rediriga a la URL especificada arriba, y como nombre visible de este botón
            //Será el nombre de la asignatura recogida anteriormente.
            $lineaDeBotones .= '<a href="' . $url . '">' . $row['nombre'] . '</a><br><br><br>';
        }
        //Y cerramos el contenedor del botón.
        $lineaDeBotones .= '</p><br>';

        echo $lineaDeBotones;
        echo '<a href="../index.php"> Volver </a>';
    }
}

//Función que muestra en una tabla los examenes realizados por los alumnos si existieran
function listadoExamenesRealizados($asignatura) {

    $conexion = conexionBD();
    $alumno = $_SESSION['usuario'];

    if($_SESSION['tipo'] == 'profesor')
    {
        $sql = "SELECT notas.id_alumno, notas.id_exam, notas.nota, examenes.nombre, examenes.num_preg 
        FROM notas
        LEFT JOIN examenes ON notas.id_exam = examenes.id_exam
        WHERE examenes.id_asig = $asignatura";  

        $result = mysqli_query($conexion, $sql);

        $formulario = '<form action="#" method="post">';
        $formulario .= '<table class="table-form">';
        $formulario .= '<tr><th>Seleccionar</th><th>Nombre de Examen</th><th>Nombre de Alumno</th><th>Nota</th></tr>';

        while ($row = mysqli_fetch_assoc($result)) {
            $id_exam = $row['id_exam'];
            $id_alumno = $row['id_alumno'];
            $nombre = $row['nombre'];
            $nota = $row['nota'];
            $num_preg = $row['num_preg'];

            $formulario .= '<tr>';
            $formulario .= '<td><input type="radio" name="examen_alumno" value="' . $id_exam . '_' . $id_alumno . '"></td>';
            $formulario .= '<td>' . $nombre . '</td>';
            $formulario .= '<td>' . $id_alumno . '</td>';
            $formulario .= '<td>' . $nota . '/' . $num_preg . '</td>';
            $formulario .= '</tr>';
        }

        $formulario .= '</table><br>';

        echo $formulario;
    }
    else if($_SESSION['tipo'] == 'alumno'){
        $sql = "SELECT notas.id_alumno, notas.id_exam, notas.nota, examenes.nombre, examenes.num_preg 
        FROM notas
        LEFT JOIN examenes ON notas.id_exam = examenes.id_exam AND notas.id_alumno = '$alumno'
        WHERE examenes.id_asig = $asignatura";  

        $result = mysqli_query($conexion, $sql);

        $formulario = '<form action="#" method="post">';
        $formulario .= '<table class="table-form" border="1">';
        $formulario .= '<tr><th>Seleccionar</th><th>Nombre de Examen</th><th>Nombre de Alumno</th><th>Nota</th></tr>';

        while ($row = mysqli_fetch_assoc($result)) {
            $id_exam = $row['id_exam'];
            $id_alumno = $row['id_alumno'];
            $nombre = $row['nombre'];
            $nota = $row['nota'];
            $num_preg = $row['num_preg'];

            $formulario .= '<tr>';
            if($nota != null) {
                $formulario .= '<td><input type="radio" name="examen_alumno" value="' . $id_exam . '_' . $id_alumno . '"></td>';
            }
            else{
                $formulario .= '<td> Corrección pendiente... </td>';
            }
            $formulario .= '<td>' . $nombre . '</td>';
            $formulario .= '<td>' . $id_alumno . '</td>';
            $formulario .= '<td>' . ($nota != null ? $nota . '/' . $num_preg : 'Pendiente') . '</td>';
            $formulario .= '</tr>';
        }

        $formulario .= '</table><br>';

        echo $formulario;
    }
    else {

        $conexion = conexionBD();
    
        $sql = "SELECT notas.id_alumno, notas.id_exam, notas.nota, examenes.nombre, examenes.num_preg 
                FROM notas
                LEFT JOIN examenes ON notas.id_exam = examenes.id_exam";  
    
        $result = mysqli_query($conexion, $sql);
    
        echo "<h2> Listado de Exámenes del Aula </h2>";
        echo '<form action="#" method="post">';
        echo '<table class="table-form" border="1">';
        echo '<tr><th>Seleccionar</th><th>Nombre de Examen</th><th>Nombre de Alumno</th><th>Nota</th></tr>';
    
        while ($row = mysqli_fetch_assoc($result)) {
            $id_exam = $row['id_exam'];
            $id_alumno = $row['id_alumno'];
            $nombre = $row['nombre'];
            $nota = $row['nota'];
            $num_preg = $row['num_preg'];
    
            echo '<tr>';
            if ($nota != null) {
                echo '<td><input type="radio" name="examen_alumno" value="' . $id_exam . '_' . $id_alumno . '"></td>';
            } else {
                $url = 'listadoadmin.php?examen=' . $id_exam . '&alumno=' . $id_alumno;
                echo '<td><a class="borrar"href="' . $url . '">Borrar</a></td>';
            }
            echo '<td>' . $nombre . '</td>';
            echo '<td>' . $id_alumno . '</td>';
            echo '<td>' . ($nota != null ? $nota . '/' . $num_preg : 'Pendiente') . '</td>';
            echo '</tr>';
        }
    
        echo '</table><br>';
        echo '</form>';
    
        // Para cuando se seleccione un examen para borrar
        if (isset($_GET['examen'])) {
            $examenSeleccionado = $_GET["examen"];
            $alumnoSeleccionado = $_GET["alumno"];
            
            echo '<h2> ¿Vas a borrar este examen? </h2>';
            echo '<form action="" method="POST">';
            echo '<input type="hidden" name="examenSeleccionado" value="' . $examenSeleccionado . '">';
            echo '<input type="hidden" name="alumnoSeleccionado" value="' . $alumnoSeleccionado . '">';
            echo '<a class="borrar"><input type="submit" name="borrar" value="Borrar" class="borrar">';
            echo '</form>';
    
            // Formulario que elimina la nota del examen
            if (isset($_POST['borrar'])) {
                $conexion = conexionBD();
    
                $examen = $_POST['examenSeleccionado'];
                $alumno = $_POST['alumnoSeleccionado'];
    
                // Delete para eliminar la nota del examen
                $sql = "DELETE FROM notas WHERE id_exam = '$examen' AND id_alumno = '$alumno';";
    
                $result = mysqli_query($conexion, $sql);
                header('Location: listadoadmin.php');
            }
        }
    }
}

//Función que muesta el listado de asignaturas para el apartadi de Consultar Examenes en alumnos
function listadoAsignaturasAC() {

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
        $url = 'listadoalum.php?matricula=' . $row['id_asig'];
        //Escribimos la etiqueta para que cada botón rediriga a la URL especificada arriba, y como nombre visible de este botón
        //Será el nombre de la asignatura recogida anteriormente.
        $lineaDeBotones .= '<a href="' . $url . '">' . $row['nombre'] . '</a><br><br><br>';
    }
    //Y cerramos el contenedor del botón.
    $lineaDeBotones .= '</p><br>';

    echo $lineaDeBotones;
}

//Función que  servirá para que los alumnos puedan visualizar su examen con sus respuestas.
function examenConsultar($alumno, $id_exam) {
    
    $conexion = conexionBD();

    // Obtenemos el nombre del examen
    $sql_nombre = "SELECT nombre FROM examenes WHERE id_exam = '$id_exam';";
    $result_nombre = mysqli_query($conexion, $sql_nombre);
    $examen = mysqli_fetch_assoc($result_nombre);

    echo "<h1>" . $examen['nombre'] . "</h1>";

    // Buscamos las preguntas que existan en la base de datos con el id del examen asociado
    $sql = "SELECT *
            FROM preguntas
            WHERE id_examen = '$id_exam';"; 

    $result = mysqli_query($conexion, $sql);
    // Guardamos como array asociativo las preguntas para operar con ellas luego
    $preguntas = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($preguntas as $pregunta) {
        echo "<div class='pregunta'>"; // Por cada pregunta escribimos su título
        echo "<label id='campos'>" . $pregunta['titulo'] . "</label><br><br><br>";

        // Y hacemos una consulta que recoja las respuestas a esas preguntas del cuestionario
        $id_preg = $pregunta['id_preg'];
        $sql_resp = "SELECT * FROM cuestionario WHERE id_preg = $id_preg AND id_alumno = '$alumno'";
        $result_resp = mysqli_query($conexion, $sql_resp);
        $cuestionario = mysqli_fetch_all($result_resp, MYSQLI_ASSOC);

        if (count($cuestionario) > 0) {
            foreach ($cuestionario as $item) {
                $correcta = $item['correcta'];
                echo "<div class='respuesta' data-correcta='$correcta'>";
                echo "<table>";
                echo "<tr>";
                echo "<td rowspan='2'>" . $item['respuesta'] . "</td>";
                echo "</tr>";
                echo "</table>";
                echo "</div><br><br>";
            }
        }
        echo "</div><br><hr><br>";
    }
}

//Función que muestra los datos de los usuarios segun si son profesores o alumnos independientemente.
function plantillaPerfil($user, $tipo) {
    
    $conexion = conexionBD();

    if($tipo == 'alumno'){

        $sql = "SELECT  ID, nombre, apellidos, email, DNI, telefono, nacimiento, codigo_postal, direccion, ciudad, provincia 
                FROM alumnos
                WHERE ID = '$user';"; 
    }
    else {

        $sql = "SELECT  ID, nombre, apellidos, email, DNI, telefono, nacimiento, codigo_postal, direccion, ciudad, provincia 
                FROM profesores
                WHERE ID = '$user';"; 
    }

    $result = mysqli_query($conexion, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        // Obtener los datos del usuario
        $row = mysqli_fetch_assoc($result);

        $id = $row['ID'];
        $nombre = $row['nombre'];
        $apellidos = $row['apellidos'];
        $email = $row['email'];
        $dni = $row['DNI'];
        $telefono = $row['telefono'];
        $nacimiento = $row['nacimiento'];
        $codigo_postal = $row['codigo_postal'];
        $direccion = $row['direccion'];
        $ciudad = $row['ciudad'];
        $provincia = $row['provincia'];

    } else {
    }

    if (isset($_GET['edit']) && $_GET['edit'] == 'true') {

        echo '<div class="profile">
                <h1 align="center">Perfil de ' . $id . '</h1>
                <form action="../procesos/editar_perfil.php" method="post" class="editable">
                <div class="infoextr profile-grid">
                    
                        <label for="name">Nombre: </label><input type="text" id="name" name="name" value="' . $nombre . '" required>
                        <label for="surname">Apellidos: </label><input type="text" id="surname" name="surname" value="' . $apellidos . '" required>
                        <label for="email">Email: </label><input type="email" id="email" name="email" value="' . $email . '" required>
                        <label for="dni">DNI: </label><input type="text" id="dni" name="dni" value="' . $dni . '" required>
                        <label for="phone">Teléfono: </label><input type="text" id="phone" name="phone" value="' . $telefono . '" required>
                        <label for="birthdate">Nacimiento: </label><input type="date" id="birthdate" name="birthdate" value="' . $nacimiento . '" required>
                        <label for="postalcode">Código Postal: </label><input type="text" id="postalcode" name="postalcode" value="' . $codigo_postal . '" required>
                        <label for="address">Dirección: </label><input type="text" id="address" name="address" value="' . $direccion . '" required>
                        <label for="city">Ciudad: </label><input type="text" id="city" name="city" value="' . $ciudad . '" required>
                        <label for="province">Provincia: </label><input type="text" id="province" name="province" value="' . $provincia . '" required>
                        <br><input type="submit" value="Guardar" id="guardar"> 
                        <div align="right"> <a id="volver" href="informacion.php"> Volver </a> </div>
                    
                </div>
                </form>
            </div>';
    }
    else {

        echo '<div class="profile">
        <h1 align="center">Perfil de ' . $id . '</h1>
        <div class="infoextr profile-grid">
            <label><strong>Nombre:</strong></label><span id="name-value">' . $nombre . '</span>
            <label><strong>Apellidos:</strong></label><span id="surname-value">' . $apellidos . '</span>
            <label><strong>Email:</strong></label><span id="email-value">' . $email . '</span>
            <label><strong>DNI:</strong></label><span id="dni-value">' . $dni . '</span>
            <label><strong>Teléfono:</strong></label><span id="phone-value">' . $telefono . '</span>
            <label><strong>Nacimiento:</strong></label><span id="birthdate-value">' . $nacimiento . '</span>
            <label><strong>Código Postal:</strong></label><span id="postalcode-value">' . $codigo_postal . '</span>
            <label><strong>Dirección:</strong></label><span id="address-value">' . $direccion . '</span>
            <label><strong>Ciudad:</strong></label><span id="city-value">' . $ciudad . '</span>
            <label><strong>Provincia:</strong></label><span id="province-value">' . $provincia . '</span>
            <a href="informacion.php?edit=true" id="editar">Editar perfil</a>
            <a href="../index.php" id="volver"> Volver </a>
        </div>
    </div>';
                            
    }

}

//Función que muestra el listado de asignaturas global para el admin
function listadoAsignaturasADMIN() {
    $conexion = conexionBD();

    //Buscamos las asignaturas que no estén registradas en la matricula del alumno en cuestión
    $sql = "SELECT id, nombre, usuario FROM asignaturas;"; 

    $result = mysqli_query($conexion, $sql);

    //Inicializamos la etiqueta para que se ordenen en una tabla en ese contenedor
    echo "<h2> Listado de Asignaturas del Aula </h2>";
    echo '<table class="table-form" border="1">';
    echo '<tr>
            <th>Nombre</th> 
            <th>Profesor</th> 
            <th>Acción</th> 
        </tr>';

    // Bucle que recorre todo el array de resultados de la sentencia anterior y lo escribe
    while ($row = mysqli_fetch_assoc($result)) {
        //Creamos la URL para que tenga los datos que nos hacen falta para los GET: la matricula, que es la asignatura impartida
        $url = 'asignaturas.php?asignatura=' . $row['id'];
        //Escribimos la fila de la tabla con el nombre de la asignatura y el profesor
        echo '<tr>';
        echo '<td>' . $row['nombre'] . '</td>';
        echo '<td>' . $row['usuario'] . '</td>';
        echo '<td><a class="borrar" href="' . $url . '">Borrar</a></td>';
        echo '</tr>';
    }

    // Cerramos la tabla
    echo '</table>';

    //Para cuando seleccione una asignatura
    if(isset($_GET['asignatura'])) {
        echo '<h2> ¿Vas a borrar esta asignatura? </h2>';

        $asignaturaSeleccionada = $_GET["asignatura"];
        echo "<form action='' method='POST'>";
        echo '<input type="hidden" name="asignaturaSeleccionada" value="' . $asignaturaSeleccionada . '">';
        echo '<input type="submit" value="Borrar">';
        echo '<a href="asignaturas.php"> Volver </a>';
        echo '</form>';

        //Formulario que borra una asignatura
        if(isset($_POST['asignaturaSeleccionada'])) {
            $conexion = conexionBD();

            $asig = $_POST['asignaturaSeleccionada'];

            //Delete para eliminar esa asignatura
            $sql = "DELETE FROM asignaturas WHERE id = '$asig';";

            $result = mysqli_query($conexion, $sql);
            header('Location: asignaturas.php');
        }
    }
}

function listadoUsuarios() {
    $conexion = conexionBD();

    //Buscamos las asignaturas que no estén registradas en la matricula del alumno en cuestión
    $sql = "SELECT * FROM usuarios WHERE rol != 'admin';"; 

    $result = mysqli_query($conexion, $sql);

    //Inicializamos la etiqueta para que se ordenen en una tabla en ese contenedor
    echo "<h2> Listado de Usuarios del Aula </h2>";
    echo '<table class="table-form" border="1">';
    echo '<tr>
            <th>Usuario</th> 
            <th>email</th>
            <th>Rol</th> 
            <th>Acción</th> 
        </tr>';

    // Bucle que recorre todo el array de resultados de la sentencia anterior y lo escribe
    while ($row = mysqli_fetch_assoc($result)) {
        //Creamos la URL para que tenga los datos que nos hacen falta para los GET: la matricula, que es la asignatura impartida
        $url = 'perfiles.php?usuario=' . $row['ID'];
        //Escribimos la fila de la tabla con el nombre de la asignatura y el profesor
        echo '<tr>';
        echo '<td>' . $row['ID'] . '</td>';
        echo '<td>' . $row['email'] . '</td>';
        echo '<td>' . $row['rol'] . '</td>';
        echo '<td><a class="borrar" href="' . $url . '">Borrar</a></td>';
        echo '</tr>';
    }

    // Cerramos la tabla
    echo '</table>';

    //Para cuando seleccione una asignatura
    if(isset($_GET['usuario'])) {
        echo '<h2> ¿Vas a borrar este usuario? </h2>';

        $usuarioSeleccionado = $_GET["usuario"];
        echo "<form action='' method='POST'>";
        echo '<input type="hidden" name="usuarioSeleccionado" value="' . $usuarioSeleccionado . '">';
        echo '<input type="submit" value="Borrar">';
        echo '</form>';

        //Formulario que borra una asignatura
        if(isset($_POST['usuarioSeleccionado'])) {
            $conexion = conexionBD();

            $usu = $_POST['usuarioSeleccionado'];

            //Delete para eliminar esa asignatura
            $sql = "DELETE FROM usuarios WHERE ID = '$usu';";

            $result = mysqli_query($conexion, $sql);
            header('Location: perfiles.php');
        }
    }
}
?>