-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-04-2026 a las 21:00:53
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
-- Base de datos: `concesionario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('employee','admin') NOT NULL DEFAULT 'employee',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin@concesionario.test', '$2y$10$WOkZvJoCwkgqx9UKBkWo0ODZS93uGCy3..fm.aXnNbPcplIGNFD6m', 'admin', '2026-04-18 22:59:13'),
(2, 'Facundo', 'user@user.com', '$2y$10$ZDJRaBTBD9M2SlQpv82Yo.OUhNMY6sBbDKMAPE8mdRHwuF.9krFMO', 'admin', '2026-04-19 00:14:35'),
(3, 'usario2', 'user2@user.com', '$2y$10$caZFDfwZjY4cRyCpACWvvOCxwFkcaTcGsviO..s4QmrEyZZH.0HPS', 'employee', '2026-04-19 00:38:19'),
(7, 'usario3', 'user3@user.com', '$2y$10$KYT9.GfpLWyfaynQGhKYZuoeu1nBUatp.ZiT.03NZfH8gSAnH.RPS', 'employee', '2026-04-20 14:41:12'),
(8, 'empleado', 'em@em.em', '$2y$10$NMyoyI7QUXcxdLvJsDeiGuDTXPbATSBoxiroeGFhy1tpA6WhkDlXm', 'employee', '2026-04-20 15:56:02'),
(9, 'user5', 'user5@user.com', '$2y$10$EvYzR5MOIl37W/tw6eWD0.z1A5J5LJ9HnqjOLbuUSfgVCaYfriY/e', 'employee', '2026-04-20 18:29:21'),
(10, 'user6', 'user6@user.com', '$2y$10$EvYzR5MOIl37W/tw6eWD0.z1A5J5LJ9HnqjOLbuUSfgVCaYfriY/e', 'employee', '2026-04-20 18:29:21'),
(11, 'user7', 'user7@user.com', '$2y$10$EvYzR5MOIl37W/tw6eWD0.z1A5J5LJ9HnqjOLbuUSfgVCaYfriY/e', 'employee', '2026-04-20 18:29:21'),
(12, 'user8', 'user8@user.com', '$2y$10$EvYzR5MOIl37W/tw6eWD0.z1A5J5LJ9HnqjOLbuUSfgVCaYfriY/e', 'employee', '2026-04-20 18:29:21'),
(13, 'user9', 'user9@user.com', '$2y$10$EvYzR5MOIl37W/tw6eWD0.z1A5J5LJ9HnqjOLbuUSfgVCaYfriY/e', 'employee', '2026-04-20 18:29:21'),
(14, 'user10', 'user10@user.com', '$2y$10$EvYzR5MOIl37W/tw6eWD0.z1A5J5LJ9HnqjOLbuUSfgVCaYfriY/e', 'employee', '2026-04-20 18:29:21'),
(15, 'user11', 'user11@user.com', '$2y$10$EvYzR5MOIl37W/tw6eWD0.z1A5J5LJ9HnqjOLbuUSfgVCaYfriY/e', 'employee', '2026-04-20 18:29:21'),
(16, 'user12', 'user12@user.com', '$2y$10$EvYzR5MOIl37W/tw6eWD0.z1A5J5LJ9HnqjOLbuUSfgVCaYfriY/e', 'employee', '2026-04-20 18:29:21'),
(17, 'user13', 'user13@user.com', '$2y$10$EvYzR5MOIl37W/tw6eWD0.z1A5J5LJ9HnqjOLbuUSfgVCaYfriY/e', 'employee', '2026-04-20 18:29:21'),
(18, 'user14', 'user14@user.com', '$2y$10$EvYzR5MOIl37W/tw6eWD0.z1A5J5LJ9HnqjOLbuUSfgVCaYfriY/e', 'employee', '2026-04-20 18:29:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `brand` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `year` smallint(5) UNSIGNED DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehicles`
--

INSERT INTO `vehicles` (`id`, `type`, `brand`, `model`, `year`, `price`, `created_at`) VALUES
(1, 'Auto', 'VW', 'gol', 1998, 8000.00, '2026-04-18 23:31:14'),
(3, 'Camioneta', 'Ford', 'F100', 2020, 887.00, '2026-04-19 00:32:01'),
(4, 'Moto', 'Suzuki', 'Ax-100', 2023, 99999999.99, '2026-04-19 01:01:52'),
(5, 'auto', 'Toyota', 'Corolla', 2020, 18500.00, '2026-04-20 18:22:44'),
(6, 'auto', 'Ford', 'Focus', 2019, 17200.00, '2026-04-20 18:22:44'),
(7, 'auto', 'Volkswagen', 'Golf', 2021, 24900.00, '2026-04-20 18:22:44'),
(8, 'auto', 'Chevrolet', 'Cruze', 2018, 16000.00, '2026-04-20 18:22:44'),
(9, 'auto', 'Honda', 'Civic', 2022, 26800.00, '2026-04-20 18:22:44'),
(10, 'auto', 'Peugeot', '208', 2023, 21500.00, '2026-04-20 18:22:44'),
(11, 'auto', 'Nissan', 'Versa', 2021, 17850.00, '2026-04-20 18:22:44'),
(12, 'auto', 'Renault', 'Sandero', 2017, 12900.00, '2026-04-20 18:22:44'),
(13, 'auto', 'Fiat', 'Cronos', 2024, 22300.00, '2026-04-20 18:22:44'),
(14, 'auto', 'Kia', 'Rio', 2020, 16750.00, '2026-04-20 18:22:44'),
(15, 'moto', 'Honda', 'CB 250', 2022, 6900.00, '2026-04-20 18:22:44'),
(16, 'moto', 'Yamaha', 'FZ-S', 2021, 6100.00, '2026-04-20 18:22:44'),
(17, 'moto', 'Suzuki', 'Gixxer', 2023, 7200.00, '2026-04-20 18:22:44'),
(18, 'moto', 'Bajaj', 'Rouser NS 200', 2020, 5400.00, '2026-04-20 18:22:44'),
(19, 'moto', 'Zanella', 'ZB 110', 2019, 2100.00, '2026-04-20 18:22:44'),
(20, 'camioneta', 'Toyota', 'Hilux', 2022, 38900.00, '2026-04-20 18:22:44'),
(21, 'camioneta', 'Ford', 'Ranger', 2023, 41700.00, '2026-04-20 18:22:44'),
(22, 'camioneta', 'Volkswagen', 'Amarok', 2021, 43500.00, '2026-04-20 18:22:44'),
(23, 'camion', 'Mercedes-Benz', 'Atego', 2020, 78500.00, '2026-04-20 18:22:44'),
(24, 'camion', 'Scania', 'P360', 2022, 99500.00, '2026-04-20 18:22:44');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- Indices de la tabla `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
