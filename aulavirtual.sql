-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-06-2024 a las 18:36:56
-- Versión del servidor: 10.4.18-MariaDB
-- Versión de PHP: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `aulavirtual`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `ID` varchar(7) COLLATE utf8_spanish_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `apellidos` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `DNI` varchar(9) COLLATE utf8_spanish_ci DEFAULT NULL,
  `telefono` int(9) DEFAULT NULL,
  `nacimiento` date DEFAULT NULL,
  `codigo_postal` int(5) DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ciudad` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `provincia` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaturas`
--

CREATE TABLE `asignaturas` (
  `id` int(10) NOT NULL,
  `nombre` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `usuario` varchar(7) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuestionario`
--

CREATE TABLE `cuestionario` (
  `id_alumno` varchar(7) COLLATE utf8_spanish_ci NOT NULL,
  `id_preg` int(100) NOT NULL,
  `respuesta` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `correcta` varchar(2) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Disparadores `cuestionario`
--
DELIMITER $$
CREATE TRIGGER `after_insert_cuestionario` AFTER INSERT ON `cuestionario` FOR EACH ROW BEGIN
    DECLARE exam_count INT;

    -- Contar el número de preguntas respondidas para el examen y el alumno específico
    SELECT COUNT(*) INTO exam_count
    FROM cuestionario
    WHERE id_alumno = NEW.id_alumno
      AND id_preg IN (SELECT id_preg FROM preguntas WHERE id_examen = (SELECT id_examen FROM preguntas WHERE id_preg = NEW.id_preg LIMIT 1));

    -- Si es la primera respuesta para este examen y alumno, insertamos un registro en notas
    IF exam_count = 1 THEN
        INSERT INTO notas (id_alumno, id_exam, fecha, nota)
        VALUES (
            NEW.id_alumno,
            (SELECT id_examen FROM preguntas WHERE id_preg = NEW.id_preg LIMIT 1),
            NOW(),
            NULL
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_cuestionario` AFTER UPDATE ON `cuestionario` FOR EACH ROW BEGIN
    DECLARE correctas_count INT;
    DECLARE examen_id INT;

    -- Obtener el ID del examen a través de la tabla preguntas
    SELECT id_examen INTO examen_id
    FROM preguntas
    WHERE id_preg = NEW.id_preg;

    -- Contar las respuestas correctas ('SI') en la tabla cuestionario para el alumno y examen específicos
    SELECT COUNT(*) INTO correctas_count
    FROM cuestionario
    WHERE id_alumno = NEW.id_alumno
    AND id_preg IN (SELECT id_preg FROM preguntas WHERE id_examen = examen_id)
    AND correcta = 'SI';

    -- Actualizar la nota en la tabla notas
    UPDATE notas
    SET nota = correctas_count
    WHERE id_alumno = NEW.id_alumno
    AND id_exam = examen_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `examenes`
--

CREATE TABLE `examenes` (
  `id_exam` int(100) NOT NULL,
  `nombre` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `num_preg` int(10) DEFAULT NULL,
  `realizados` int(10) DEFAULT NULL,
  `id_asig` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula`
--

CREATE TABLE `matricula` (
  `id_alumno` varchar(7) COLLATE utf8_spanish_ci NOT NULL,
  `id_asig` int(10) NOT NULL,
  `curso` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `id_alumno` varchar(7) COLLATE utf8_spanish_ci NOT NULL,
  `id_exam` int(100) NOT NULL,
  `nota` int(2) DEFAULT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Disparadores `notas`
--
DELIMITER $$
CREATE TRIGGER `after_insert_notas` AFTER INSERT ON `notas` FOR EACH ROW BEGIN
    DECLARE exam_count INT;

    -- Contar el número de veces que aparece el id_exam en la tabla notas
    SELECT COUNT(*) INTO exam_count
    FROM notas
    WHERE id_exam = NEW.id_exam;

    -- Actualizar la columna realizados en la tabla examenes
    UPDATE examenes
    SET realizados = exam_count
    WHERE id_exam = NEW.id_exam;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_delete_notas` BEFORE DELETE ON `notas` FOR EACH ROW BEGIN
    -- Eliminar las respuestas correspondientes de la tabla 'cuestionario'
    DELETE FROM cuestionario
    WHERE id_alumno = OLD.id_alumno
      AND id_preg IN (
          SELECT id_preg
          FROM preguntas
          WHERE id_examen = OLD.id_exam
      );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas`
--

CREATE TABLE `preguntas` (
  `id_examen` int(100) NOT NULL,
  `id_preg` int(100) NOT NULL,
  `titulo` varchar(255) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Disparadores `preguntas`
--
DELIMITER $$
CREATE TRIGGER `after_insert_preguntas` AFTER INSERT ON `preguntas` FOR EACH ROW BEGIN
    DECLARE preguntas_count INT;

    -- Contar el número de preguntas para el examen específico
    SELECT COUNT(*) INTO preguntas_count
    FROM preguntas
    WHERE id_examen = NEW.id_examen;

    -- Actualizar la columna num_preg en la tabla examenes
    UPDATE examenes
    SET num_preg = preguntas_count
    WHERE id_exam = NEW.id_examen;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `ID` varchar(7) COLLATE utf8_spanish_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `apellidos` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `DNI` varchar(9) COLLATE utf8_spanish_ci DEFAULT NULL,
  `telefono` int(9) DEFAULT NULL,
  `nacimiento` date DEFAULT NULL,
  `codigo_postal` int(5) DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ciudad` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `provincia` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestas`
--

CREATE TABLE `respuestas` (
  `id_preg` int(100) NOT NULL,
  `id_resp` int(100) NOT NULL,
  `respuesta` varchar(255) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID` varchar(7) COLLATE utf8_spanish_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `contraseña` varchar(30) COLLATE utf8_spanish_ci NOT NULL,
  `rol` varchar(10) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID`, `email`, `contraseña`, `rol`) VALUES
('admin', 'admin@admin.com', 'admin', 'admin');

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `after_user_insert` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
    IF NEW.rol = 'alumno' THEN
        INSERT INTO alumnos (id, email)
        VALUES (NEW.id, NEW.email);
    ELSEIF NEW.rol = 'profesor' THEN
        INSERT INTO profesores (id, email)
        VALUES (NEW.id, NEW.email);
    END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asig_prof` (`usuario`);

--
-- Indices de la tabla `cuestionario`
--
ALTER TABLE `cuestionario`
  ADD PRIMARY KEY (`id_alumno`,`id_preg`),
  ADD KEY `preg_cuest` (`id_preg`) USING BTREE;

--
-- Indices de la tabla `examenes`
--
ALTER TABLE `examenes`
  ADD PRIMARY KEY (`id_exam`),
  ADD KEY `exa_asig` (`id_asig`);

--
-- Indices de la tabla `matricula`
--
ALTER TABLE `matricula`
  ADD PRIMARY KEY (`id_alumno`,`id_asig`),
  ADD KEY `asig_matri` (`id_asig`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id_alumno`,`id_exam`,`fecha`) USING BTREE,
  ADD KEY `exam_nota` (`id_exam`);

--
-- Indices de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD PRIMARY KEY (`id_preg`),
  ADD KEY `exa_preg` (`id_examen`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD PRIMARY KEY (`id_resp`),
  ADD KEY `resp_preg` (`id_preg`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `examenes`
--
ALTER TABLE `examenes`
  MODIFY `id_exam` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  MODIFY `id_preg` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  MODIFY `id_resp` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD CONSTRAINT `usu_alu` FOREIGN KEY (`ID`) REFERENCES `usuarios` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  ADD CONSTRAINT `asig_prof` FOREIGN KEY (`usuario`) REFERENCES `profesores` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cuestionario`
--
ALTER TABLE `cuestionario`
  ADD CONSTRAINT `alum_resp` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `preg_cuest` FOREIGN KEY (`id_preg`) REFERENCES `preguntas` (`id_preg`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `examenes`
--
ALTER TABLE `examenes`
  ADD CONSTRAINT `exa_asig` FOREIGN KEY (`id_asig`) REFERENCES `asignaturas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `matricula`
--
ALTER TABLE `matricula`
  ADD CONSTRAINT `alum_matri` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `asig_matri` FOREIGN KEY (`id_asig`) REFERENCES `asignaturas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `alum_nota` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_nota` FOREIGN KEY (`id_exam`) REFERENCES `examenes` (`id_exam`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD CONSTRAINT `exa_preg` FOREIGN KEY (`id_examen`) REFERENCES `examenes` (`id_exam`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD CONSTRAINT `usu_prof` FOREIGN KEY (`ID`) REFERENCES `usuarios` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD CONSTRAINT `resp_preg` FOREIGN KEY (`id_preg`) REFERENCES `preguntas` (`id_preg`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
