-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-06-2026 a las 01:41:04
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
-- Base de datos: `veterinaria_x`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `id_mascota` int(11) NOT NULL,
  `id_veterinario` int(11) NOT NULL,
  `motivo` varchar(200) NOT NULL,
  `estado` enum('Pendiente','Atendida','Cancelada') DEFAULT 'Pendiente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id`, `fecha`, `hora`, `id_mascota`, `id_veterinario`, `motivo`, `estado`, `fecha_registro`) VALUES
(1, '2025-11-10', '10:00:00', 1, 1, 'Vacunación anual', 'Atendida', '2025-11-05 07:37:09'),
(2, '2025-11-11', '14:30:00', 2, 2, 'Revisión general', 'Atendida', '2025-11-05 07:37:09'),
(3, '2025-11-05', '09:00:00', 3, 3, 'Problema de piel', 'Atendida', '2025-11-05 07:37:09'),
(4, '2025-11-12', '16:00:00', 4, 1, 'Control de peso', 'Atendida', '2025-11-05 07:37:09'),
(5, '2025-11-29', '07:30:00', 5, 1, 'Inyeccion contra la rabia', 'Atendida', '2025-11-22 16:25:24'),
(6, '2025-11-30', '21:30:00', 6, 1, 'Quimioterapia', 'Atendida', '2025-11-27 22:37:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `duenos`
--

CREATE TABLE `duenos` (
  `dni` varchar(8) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `duenos`
--

INSERT INTO `duenos` (`dni`, `id_usuario`, `nombre`, `telefono`, `direccion`, `fecha_registro`) VALUES
('11223344', NULL, 'Carlos Rodríguez Torres', '965432178', 'Calle Los Olivos 789, Lima', '2025-11-05 07:37:09'),
('12345678', NULL, 'Juan Pérez García', '987654321', 'Av. Los Pinos 123, Lima', '2025-11-05 07:37:09'),
('75085925', 2, 'Franco Mariano Pacheco', '978475665', 'mzd1 lt1 urb. virgen del rosario', '2025-11-27 17:29:05'),
('78945632', 3, 'Mauro Salvador Fuentes Vega', '986453125', 'Ventanilla', '2025-11-27 23:08:38'),
('87654321', NULL, 'María López Sánchez', '912345678', 'Jr. Las Flores 456, Lima', '2025-11-05 07:37:09'),
('88556413', NULL, 'Jose Miguel Cajo Rojas', '964587624', 'Los olivos Huandoy', '2025-11-22 16:23:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mascotas`
--

CREATE TABLE `mascotas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `especie` varchar(50) NOT NULL,
  `raza` varchar(50) NOT NULL,
  `edad` int(11) NOT NULL,
  `dni_dueno` varchar(8) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `mascotas`
--

INSERT INTO `mascotas` (`id`, `nombre`, `especie`, `raza`, `edad`, `dni_dueno`, `fecha_registro`) VALUES
(1, 'Firulais', 'Perro', 'Labrador', 3, '12345678', '2025-11-05 07:37:09'),
(2, 'Michi', 'Gato', 'Persa', 2, '87654321', '2025-11-05 07:37:09'),
(3, 'Rocky', 'Perro', 'Pastor Alemán', 5, '11223344', '2025-11-05 07:37:09'),
(4, 'Luna', 'Gato', 'Siamés', 1, '12345678', '2025-11-05 07:37:09'),
(5, 'Pancho', 'Perro', 'Pitbull', 5, '88556413', '2025-11-22 16:24:40'),
(6, 'Canela', 'Perro', 'Pitbull', 5, '75085925', '2025-11-27 22:35:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reclamaciones`
--

CREATE TABLE `reclamaciones` (
  `id` int(11) NOT NULL,
  `tipo_documento` varchar(20) NOT NULL,
  `numero_documento` varchar(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `direccion` text NOT NULL,
  `tipo_reclamo` varchar(50) NOT NULL,
  `detalle` text NOT NULL,
  `pedido` text DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'Pendiente',
  `respuesta` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `reclamaciones`
--

INSERT INTO `reclamaciones` (`id`, `tipo_documento`, `numero_documento`, `nombres`, `apellidos`, `telefono`, `email`, `direccion`, `tipo_reclamo`, `detalle`, `pedido`, `estado`, `respuesta`, `fecha_registro`) VALUES
(1, 'DNI', '88556413', 'Jose Miguel ', 'Cajo Rojas', '964587624', 'josemiguelrojas@gmail.com', 'Los olivos Huandoy', 'Reclamo', 'Impuntualidad', '', 'Resuelta', NULL, '2025-11-22 16:45:32'),
(2, 'DNI', '75085925', 'Franco Mariano', 'Pacheco Poemape', '978475665', 'fpachecop@ucvvirtual.edu.pe', 'mzd1 lt1 urb. virgen del rosario', 'Queja', 'No cumplir con el horario de atencion exacto, tardan 10 min tarde en atenderme.', '', 'Resuelta', NULL, '2025-11-27 23:02:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones`
--

CREATE TABLE `sesiones` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `fecha_inicio` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_expiracion` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sugerencias`
--

CREATE TABLE `sugerencias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `categoria` varchar(50) NOT NULL,
  `sugerencia` text NOT NULL,
  `estado` varchar(20) DEFAULT 'Pendiente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `sugerencias`
--

INSERT INTO `sugerencias` (`id`, `nombre`, `email`, `telefono`, `categoria`, `sugerencia`, `estado`, `fecha_registro`) VALUES
(1, 'Franco Mariano Pacheco', 'fpachecop@ucvvirtual.edu.pe', '978475665', 'Página Web', 'Poner un apartado de productos Pet Shop', 'Pendiente', '2025-11-27 23:02:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `dni` varchar(8) DEFAULT NULL,
  `rol` enum('usuario','administrador') DEFAULT 'usuario',
  `activo` tinyint(1) DEFAULT 1,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `telefono`, `direccion`, `dni`, `rol`, `activo`, `fecha_registro`) VALUES
(1, 'Administrador', 'Sistema', 'admin@ralahpets.com', '$2y$10$9kMd1jkYvyQq2nzyuyPHIeRIyRzUEIx.eIunVFHHo84SRL8kTIN7i', NULL, NULL, NULL, 'administrador', 1, '2025-11-27 17:18:30'),
(2, 'Franco Mariano', 'Pacheco', 'fpachecop@ucvvirtual.edu.pe', '$2y$10$LjAo42hSwb1G120.TnN0kukl2WC4NeubVzxANLExrDZapUWLiKKFO', '978475665', 'mzd1 lt1 urb. virgen del rosario', '75085925', 'usuario', 1, '2025-11-27 17:29:05'),
(3, 'Mauro Salvador', 'Fuentes Vega', 'mfuentesve@ucvvirtual.edu.pe', '$2y$10$4C9pfS95JGyM5mdggXh9IeiosygVy1IhbExQ52UWPGk4e6KCknRsm', '986453125', 'Ventanilla', '78945632', 'usuario', 1, '2025-11-27 23:08:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `veterinarios`
--

CREATE TABLE `veterinarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `especialidad` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `veterinarios`
--

INSERT INTO `veterinarios` (`id`, `nombre`, `especialidad`, `correo`, `telefono`, `fecha_registro`) VALUES
(1, 'Dr. Roberto Vega', 'Medicina General', 'rvega@veterinaria.com', '999888777', '2025-11-05 07:37:09'),
(2, 'Dra. Ana Martínez', 'Cirugía Veterinaria', 'amartinez@veterinaria.com', '988777666', '2025-11-05 07:37:09'),
(3, 'Dr. Luis Fernández', 'Dermatología', 'lfernandez@veterinaria.com', '977666555', '2025-11-05 07:37:09'),
(4, 'Dr Junior Alva', 'Neurología', 'JuniorAlva@gmail.com', '995678473', '2025-12-08 20:15:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `url_video` varchar(255) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `videos`
--

INSERT INTO `videos` (`id`, `titulo`, `descripcion`, `url_video`, `categoria`, `activo`, `fecha_registro`) VALUES
(1, 'Cuidados básicos para perros', 'Aprende los cuidados esenciales para tu mascota', 'https://www.youtube.com/embed/TbgB5zH8_3E', 'Consejos', 1, '2025-11-05 07:55:42'),
(2, 'Vacunación de gatos', 'Conoce el calendario de vacunación para gatos', 'https://www.youtube.com/embed/b-OV1Pv3A5w', 'Servicios', 1, '2025-11-05 07:55:42'),
(3, 'Alimentación saludable', 'Tips para una alimentación balanceada', 'https://www.youtube.com/embed/ex8my8af_E8', 'Servicios', 1, '2025-11-05 07:55:42');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_mascota` (`id_mascota`),
  ADD KEY `id_veterinario` (`id_veterinario`);

--
-- Indices de la tabla `duenos`
--
ALTER TABLE `duenos`
  ADD PRIMARY KEY (`dni`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dni_dueno` (`dni_dueno`);

--
-- Indices de la tabla `reclamaciones`
--
ALTER TABLE `reclamaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `sugerencias`
--
ALTER TABLE `sugerencias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `veterinarios`
--
ALTER TABLE `veterinarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `reclamaciones`
--
ALTER TABLE `reclamaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sugerencias`
--
ALTER TABLE `sugerencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `veterinarios`
--
ALTER TABLE `veterinarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`id_mascota`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`id_veterinario`) REFERENCES `veterinarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `duenos`
--
ALTER TABLE `duenos`
  ADD CONSTRAINT `fk_dueno_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`dni_dueno`) REFERENCES `duenos` (`dni`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD CONSTRAINT `sesiones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
