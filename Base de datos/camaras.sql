-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-02-2025 a las 17:36:27
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `camaras`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud`
--

CREATE TABLE `solicitud` (
  `Id` int(11) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `cedis` varchar(200) NOT NULL,
  `fecha` date DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `correo` varchar(200) NOT NULL,
  `fecha_hoy` date DEFAULT NULL,
  `notificacion` text NOT NULL,
  `visto` int(11) NOT NULL COMMENT '0 No Visto\r\n1 Visto',
  `usuario_solicitante` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitud`
--

INSERT INTO `solicitud` (`Id`, `descripcion`, `cedis`, `fecha`, `hora_inicio`, `hora_fin`, `correo`, `fecha_hoy`, `notificacion`, `visto`, `usuario_solicitante`) VALUES
(1, 'Prueba 1', 'PACHUCA', '2025-02-20', '10:31:00', '11:36:00', 'automotrizserva10@gmail.com', '2025-02-20', 'Solicitud de JonathanC: Prueba 1, CEDIS: PACHUCA, Fecha: 2025-02-20', 1, 'JonathanC');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuariosc`
--

CREATE TABLE `usuariosc` (
  `id` int(11) NOT NULL,
  `usuario` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `area` varchar(100) NOT NULL,
  `rol` enum('TI','Operador') DEFAULT 'Operador'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuariosc`
--

INSERT INTO `usuariosc` (`id`, `usuario`, `password`, `area`, `rol`) VALUES
(1, 'JonathanC', '$2y$10$Wsy3hBNnLpByTjAquHxdquCAgFc1F206oYJ/f4n5DRTsWvJgm4eoi', 'SISTEMAS', 'Operador'),
(2, 'Jona', '$2y$10$X8vmFADE18xhfwM47zwGseAciunh5rrx9ZVSKOJrFiNi96Hfm5ism', 'SISTEMAS', 'Operador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuariosti`
--

CREATE TABLE `usuariosti` (
  `id` int(11) NOT NULL,
  `usuario` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `area` varchar(100) NOT NULL,
  `rol` enum('TI','Operador') DEFAULT 'TI'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuariosti`
--

INSERT INTO `usuariosti` (`id`, `usuario`, `password`, `area`, `rol`) VALUES
(1, 'Jonathan', '$2y$10$Wsy3hBNnLpByTjAquHxdquCAgFc1F206oYJ/f4n5DRTsWvJgm4eoi', 'SISTEMAS', 'TI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `videos`
--

CREATE TABLE `videos` (
  `Id` int(11) NOT NULL,
  `Id_solicitud` int(11) NOT NULL,
  `usuario` varchar(200) NOT NULL,
  `descripcion` text NOT NULL,
  `video` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `videos`
--

INSERT INTO `videos` (`Id`, `Id_solicitud`, `usuario`, `descripcion`, `video`) VALUES
(1, 1, 'JonathanC', 'Respondo video para Prueba 1', '67b7590561e36_Video.mp4');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `solicitud`
--
ALTER TABLE `solicitud`
  ADD PRIMARY KEY (`Id`);

--
-- Indices de la tabla `usuariosc`
--
ALTER TABLE `usuariosc`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuariosti`
--
ALTER TABLE `usuariosti`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `solicitud`
--
ALTER TABLE `solicitud`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuariosc`
--
ALTER TABLE `usuariosc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuariosti`
--
ALTER TABLE `usuariosti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `videos`
--
ALTER TABLE `videos`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
