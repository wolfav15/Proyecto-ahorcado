-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 02-07-2026 a las 22:47:12
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ahorcaditosBD`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dispositivos`
--

CREATE TABLE `dispositivos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `identificador_dispositivo` varchar(255) NOT NULL,
  `ultima_partida` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `letras_intentadas`
--

CREATE TABLE `letras_intentadas` (
  `id` int(11) NOT NULL,
  `id_partida` int(11) NOT NULL,
  `letra` char(1) NOT NULL,
  `acierto` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `palabras`
--

CREATE TABLE `palabras` (
  `id_palabra` int(11) NOT NULL,
  `palabra` varchar(50) NOT NULL,
  `dificultad` enum('BAJA','MEDIA','ALTA') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `palabras`
--

INSERT INTO `palabras` (`id_palabra`, `palabra`, `dificultad`) VALUES
(1, 'SOL', 'BAJA'),
(2, 'CASA', 'BAJA'),
(3, 'PERRO', 'BAJA'),
(4, 'GATO', 'BAJA'),
(5, 'LIBRO', 'BAJA'),
(6, 'COMPUTADORA', 'ALTA'),
(7, 'PROGRAMACION', 'ALTA'),
(8, 'DESARROLLADOR', 'ALTA'),
(9, 'UNIVERSIDAD', 'ALTA'),
(10, 'MICROPROCESADOR', 'ALTA'),
(11, 'VENTANA', 'MEDIA'),
(12, 'TECLADO', 'MEDIA'),
(13, 'MONITOR', 'MEDIA'),
(14, 'ESCUELA', 'MEDIA'),
(15, 'CELULAR', 'MEDIA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidas`
--

CREATE TABLE `partidas` (
  `id_partida` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_palabra` int(11) NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `estado` enum('EN_CURSO','GANADA','PERDIDA','ABANDONADA') NOT NULL,
  `oportunidades_restantes` int(11) NOT NULL DEFAULT 10,
  `tiempo_segundos` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `pais` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `dispositivos`
--
ALTER TABLE `dispositivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `ultima_partida` (`ultima_partida`);

--
-- Indices de la tabla `letras_intentadas`
--
ALTER TABLE `letras_intentadas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_partida` (`id_partida`);

--
-- Indices de la tabla `palabras`
--
ALTER TABLE `palabras`
  ADD PRIMARY KEY (`id_palabra`);

--
-- Indices de la tabla `partidas`
--
ALTER TABLE `partidas`
  ADD PRIMARY KEY (`id_partida`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_palabra` (`id_palabra`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `dispositivos`
--
ALTER TABLE `dispositivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `letras_intentadas`
--
ALTER TABLE `letras_intentadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `palabras`
--
ALTER TABLE `palabras`
  MODIFY `id_palabra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `partidas`
--
ALTER TABLE `partidas`
  MODIFY `id_partida` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `dispositivos`
--
ALTER TABLE `dispositivos`
  ADD CONSTRAINT `dispositivos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dispositivos_ibfk_2` FOREIGN KEY (`ultima_partida`) REFERENCES `partidas` (`id_partida`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `letras_intentadas`
--
ALTER TABLE `letras_intentadas`
  ADD CONSTRAINT `letras_intentadas_ibfk_1` FOREIGN KEY (`id_partida`) REFERENCES `partidas` (`id_partida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `partidas`
--
ALTER TABLE `partidas`
  ADD CONSTRAINT `partidas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `partidas_ibfk_2` FOREIGN KEY (`id_palabra`) REFERENCES `palabras` (`id_palabra`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
