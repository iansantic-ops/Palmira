-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-10-2025 a las 20:27:12
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `palmira`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin`
--

CREATE TABLE `admin` (
  `idAdmin` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admin`
--

INSERT INTO `admin` (`idAdmin`, `usuario`, `contrasena`) VALUES
(1, 'Ian', '2201');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anuncios_historial`
--

CREATE TABLE `anuncios_historial` (
  `id` int(11) NOT NULL,
  `ruta_imagen` varchar(255) NOT NULL,
  `fecha` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `anuncios_historial`
--

INSERT INTO `anuncios_historial` (`id`, `ruta_imagen`, `fecha`) VALUES
(4, 'assets/anuncioPalmira/1759631726_Imagen de WhatsApp 2025-08-06 a las 18.01.29_a95eaf4a.jpg', '2025-10-04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `imagen_anuncio` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `imagen_anuncio`) VALUES
(1, 'assets/anuncioPalmira/1759631726_Imagen de WhatsApp 2025-08-06 a las 18.01.29_a95eaf4a.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `idE` bigint(20) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `lugar` varchar(200) DEFAULT NULL,
  `aforo_max` bigint(20) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`idE`, `nombre`, `descripcion`, `fecha`, `hora`, `lugar`, `aforo_max`, `imagen`) VALUES
(13805901, 'Evento CUP Palmira', 'evento de prueba para palmira, con secciones', '2025-10-15', '14:00:00', 'palmira', 10000, NULL),
(98954401, 'prueba3', 'prueba3', '2027-11-11', '20:03:00', 'prueba3', 1000000000, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `idI` bigint(20) NOT NULL,
  `idR` int(11) NOT NULL,
  `idE` bigint(20) NOT NULL,
  `idSeccion` bigint(20) DEFAULT NULL,
  `fecha_inscripcion` timestamp NOT NULL DEFAULT current_timestamp(),
  `asistio` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_asistencia` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`idI`, `idR`, `idE`, `idSeccion`, `fecha_inscripcion`, `asistio`, `fecha_asistencia`) VALUES
(99167417, 34993872, 98954401, NULL, '2025-10-14 05:03:54', 0, NULL),
(93434711, 34993872, 93754390, NULL, '2025-10-14 17:33:17', 1, '2025-10-14 11:59:32'),
(59159193, 34993872, 93754390, NULL, '2025-10-14 18:00:52', 1, '2025-10-14 12:01:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros`
--

CREATE TABLE `registros` (
  `idR` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(200) NOT NULL,
  `lada` varchar(5) NOT NULL,
  `telefono` bigint(20) NOT NULL,
  `correo` varchar(200) NOT NULL,
  `medioE` varchar(500) NOT NULL,
  `origen` varchar(150) NOT NULL,
  `pais` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `registros`
--

INSERT INTO `registros` (`idR`, `nombre`, `apellidos`, `lada`, `telefono`, `correo`, `medioE`, `origen`, `pais`) VALUES
(34993872, 'Ian Santiago', 'Cruz Rodriguez', '+52', 5611056506, 'iansantic@gmail.com', 'internet', 'cecytem', 'MÉXICO'),
(99617995, 'Prueba', 'Prueba', '+52', 5511056506, 'prueba@prueba.com', 'estoy haciendo prueba', 'prueba', 'MÉXICO'),
(45554277, 'ERICK URIEL', 'Chávez Sánchez', '+52', 5517464973, 'erick.uriel7777@gmail.com', 'CORREO ELECTRÓNICO', 'No lo se', 'MÉXICO'),
(62037417, 'RAFAEL', 'CHAVEZ', '+52', 5537969100, 'memorafa.chavez80@gmail.com', 'REDES SOCIALES', 'escuela de cheves', 'MÉXICO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secciones_evento`
--

CREATE TABLE `secciones_evento` (
  `idSeccion` bigint(20) NOT NULL,
  `idE` bigint(20) NOT NULL,
  `nombre_seccion` varchar(150) NOT NULL,
  `hora_inicio` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `secciones_evento`
--

INSERT INTO `secciones_evento` (`idSeccion`, `idE`, `nombre_seccion`, `hora_inicio`) VALUES
(5, 13805901, 'primera ponencia', '14:10:00'),
(6, 13805901, 'segunda ponencia', '15:00:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`idAdmin`);

--
-- Indices de la tabla `anuncios_historial`
--
ALTER TABLE `anuncios_historial`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`idE`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD UNIQUE KEY `idR` (`idR`,`idE`,`idSeccion`),
  ADD KEY `fk_inscripciones_seccion` (`idSeccion`);

--
-- Indices de la tabla `secciones_evento`
--
ALTER TABLE `secciones_evento`
  ADD PRIMARY KEY (`idSeccion`),
  ADD KEY `idE` (`idE`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anuncios_historial`
--
ALTER TABLE `anuncios_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `secciones_evento`
--
ALTER TABLE `secciones_evento`
  MODIFY `idSeccion` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `fk_inscripciones_seccion` FOREIGN KEY (`idSeccion`) REFERENCES `secciones_evento` (`idSeccion`) ON DELETE SET NULL;

--
-- Filtros para la tabla `secciones_evento`
--
ALTER TABLE `secciones_evento`
  ADD CONSTRAINT `secciones_evento_ibfk_1` FOREIGN KEY (`idE`) REFERENCES `eventos` (`idE`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
