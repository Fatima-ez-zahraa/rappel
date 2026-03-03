-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3308
-- Généré le : lun. 02 mars 2026 à 17:15
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `beta-db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `beta-db`;

CREATE USER IF NOT EXISTS 'beta-user'@'localhost' IDENTIFIED BY 'Fatima2026++';
GRANT ALL PRIVILEGES ON `beta-db`.* TO 'beta-user'@'localhost';
FLUSH PRIVILEGES;

--
-- Base de données : `rappel`
--

-- --------------------------------------------------------

--
-- Structure de la table `client_partner_notes`
--

CREATE TABLE `client_partner_notes` (
  `id` char(36) NOT NULL,
  `lead_id` char(36) NOT NULL,
  `client_id` char(36) NOT NULL,
  `provider_id` char(36) NOT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `client_partner_notes`
--

INSERT INTO `client_partner_notes` (`id`, `lead_id`, `client_id`, `provider_id`, `rating`, `comment`, `created_at`) VALUES
('c8b243db-44bb-4f88-bdfa-db35ba39cc22', 'e6e689d4-7354-491c-a1aa-dd97bf5f0d07', 'e8cfd9e0-e72f-4d84-8a95-a276c6ba531e', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', 5, 'such a good provider', '2026-03-02 14:30:59');

-- --------------------------------------------------------

--
-- Structure de la table `invoices`
--

CREATE TABLE `invoices` (
  `id` char(36) NOT NULL,
  `provider_id` char(36) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'EUR',
  `stripe_customer_id` varchar(255) DEFAULT NULL,
  `stripe_payment_id` varchar(255) DEFAULT NULL,
  `stripe_session_id` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'paid',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `leads`
--

CREATE TABLE `leads` (
  `id` char(36) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `need` text DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT 0.00,
  `avatar_url` varchar(255) DEFAULT NULL,
  `time_slot` varchar(50) DEFAULT 'Non spécifié',
  `preferred_date` date DEFAULT NULL,
  `doc_path` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `leads`
--

INSERT INTO `leads` (`id`, `user_id`, `name`, `email`, `phone`, `zip_code`, `city`, `address`, `sector`, `need`, `budget`, `avatar_url`, `time_slot`, `preferred_date`, `doc_path`, `status`, `created_at`, `updated_at`) VALUES
('06a10473-4a90-42a2-bb02-637ff4d96b90', NULL, 'uyftgdvg fbh', 'hicham@ycndev.com', '0623541256', NULL, NULL, '23122', 'assurance', '', 0.00, NULL, 'soir', NULL, NULL, 'assigned', '2026-02-20 11:30:54', '2026-02-23 08:18:58'),
('088fae5d-f7a2-4674-9e98-2e2c8bae85c6', NULL, 'dff dfdf', 'fatimaezahra@ycndev.com', '0623541256', NULL, NULL, '', 'finance', '', 0.00, NULL, 'matin', NULL, NULL, 'assigned', '2026-02-19 08:58:10', '2026-02-20 08:31:52'),
('148665ab-f352-490d-ad29-fb3cac4514ec', NULL, 'test2 test2', 'ahmed@ycndev.com', '0632541230', NULL, NULL, '85000', 'telecom', '', 0.00, NULL, 'apres-midi', NULL, NULL, 'pending', '2026-02-23 16:18:06', '2026-02-23 16:18:06'),
('14cf60dc-921f-42e0-bfbc-1adf2b0a773a', NULL, 'Test User 881', 'test6134@example.com', '0676025423', NULL, NULL, NULL, 'energie', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-10-13 17:42:12', '2026-02-27 08:55:17'),
('17754028-5544-459a-b240-611de5a8d842', 'e8cfd9e0-e72f-4d84-8a95-a276c6ba531e', 'Client Test', 'fbendriss0@gmail.com', '0762214100', '', '', '', 'renovation', ':knjnjkk', 3500.00, NULL, 'Dès que possible', NULL, NULL, 'confirm?', '2026-02-27 12:30:33', '2026-03-02 10:33:45'),
('2dee4683-69e7-4313-81eb-fb6e8dc31c27', 'e8cfd9e0-e72f-4d84-8a95-a276c6ba531e', 'Client Test', 'fbendriss0@gmail.com', '0762214100', '', '', '', 'renovation', 'finance', 1500.00, NULL, 'Dès que possible', NULL, NULL, 'assigned', '2026-02-27 10:59:57', '2026-02-27 16:49:46'),
('3322af7f-564f-4a61-9098-6b6025e453bd', NULL, 'Test User 972', 'test9935@example.com', '0673429924', NULL, NULL, NULL, 'assurance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2026-02-11 09:53:28', '2026-02-27 12:40:33'),
('35536ccd-64ae-42d2-ab2f-85426b24af0a', NULL, 'Test User 763', 'test1190@example.com', '0693079016', NULL, NULL, NULL, 'finance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-11-27 09:17:26', '2026-02-27 08:55:17'),
('47f1d567-4b1b-4740-9d09-53f150168dd0', NULL, 'Test User 565', 'test6684@example.com', '0690104851', NULL, NULL, NULL, 'telecom', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'new', '2026-01-11 17:24:48', '2026-02-26 16:52:45'),
('4956d5f9-6833-4cee-b806-df70c845ca2c', 'e8cfd9e0-e72f-4d84-8a95-a276c6ba531e', 'Client FZ', 'fbendriss0@gmail.com', '0762214100', '', '', '', 'garage', 'ggg', 1500.00, NULL, '', '2026-03-28', NULL, 'processed', '2026-03-02 14:23:49', '2026-03-02 15:01:35'),
('4d55dd82-89e7-40bd-99fe-c3e56f9114a6', NULL, 'Mouad', 'mouad@ycndev.com', '0632541252', '', '', 'dfsgrg', 'garage', '', 200.00, NULL, 'Après-midi (14h – 18h)', NULL, NULL, 'pending', '2026-02-26 14:44:22', '2026-02-26 14:44:22'),
('778e99d8-81cf-4616-ba1d-c8644319671d', NULL, 'ljhhvuvgu', 'ahmed@ycndev.com', '0623541256', NULL, NULL, '', 'renovation', '', 0.00, NULL, 'soir', NULL, NULL, 'completed', '2026-02-24 15:13:40', '2026-03-02 11:47:36'),
('7897ea5d-89c6-49ee-b0f1-548783cffa25', '4548d08f-8014-4bd7-8620-7a5f7e014a46', 'jean ben', 'benabbeshicham1@gmail.com', '0696435720', '75000', 'paris', '', 'garage', 'je veux ............................................', 0.00, NULL, 'Midi (12h – 14h)', NULL, NULL, 'quote_sent', '2026-02-27 15:50:05', '2026-02-27 16:38:10'),
('7919f120-309e-41a1-b9cd-00e31f9feb0a', NULL, 'Test User 131', 'test5732@example.com', '0671506154', NULL, NULL, NULL, 'renovation', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-10-28 17:00:13', '2026-02-27 08:55:17'),
('798c0967-894a-434c-a792-569b54ea9813', NULL, 'Test User 846', 'test5620@example.com', '0678492713', NULL, NULL, NULL, 'finance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-12-03 08:09:33', '2026-02-27 08:55:17'),
('7b68df98-12f5-4514-89eb-c08a175808a5', NULL, 'Test User 963', 'test3147@example.com', '0695698374', NULL, NULL, NULL, 'assurance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2026-01-23 19:16:31', '2026-02-27 12:40:33'),
('7b81d83f-3758-4a03-96d9-ce6107f57731', NULL, 'htt hth', 'fatimaezahra@ycndev.com', '0623541256', NULL, NULL, '23122', 'renovation', '', 0.00, NULL, 'weekend', NULL, NULL, 'assigned', '2026-02-19 09:29:59', '2026-02-20 08:31:52'),
('81a1b8d5-5fa7-48df-a960-77a7e53db421', NULL, 'Yassir Ouali', 'yassir@ycndev.com', '06325411256', NULL, NULL, '23000', 'energie', '', 0.00, NULL, 'Matin (09h – 12h)', NULL, NULL, 'pending', '2026-02-25 14:56:50', '2026-02-26 10:56:59'),
('87ff3ea3-6090-4c1f-9cef-1b8a2e8ef9bc', NULL, 'Test User 290', 'test4980@example.com', '0665096686', NULL, NULL, NULL, 'energie', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-11-17 01:52:35', '2026-02-27 08:55:17'),
('8b6a7d9c-b59c-495a-b2aa-d043f0de0532', NULL, 'jjjjjjjj', 'fatimaezahra@ycndev.com', '01515151515', NULL, NULL, '85200', 'garage', '', 0.00, NULL, 'weekend', NULL, NULL, 'assigned', '2026-02-23 16:33:27', '2026-02-26 16:37:44'),
('8d5966bc-9cd1-45ca-95e6-c0b068c1c5a6', NULL, 'Test User 239', 'test6689@example.com', '0634979760', NULL, NULL, NULL, 'renovation', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-09-25 08:46:32', '2026-02-27 08:55:17'),
('8d98b686-dc21-49f9-8b68-2e60eb9b2e9d', NULL, 'Test User 256', 'test1519@example.com', '0614195743', NULL, NULL, NULL, 'finance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-10-20 06:57:13', '2026-02-27 08:55:17'),
('8f18f8a2-8730-4c7f-b22a-3a21f75ce9dc', NULL, 'kkkkk', 'ftft@gmail.com', '0632541210', NULL, NULL, 'jkhjv', 'renovation', 'jkhk', 200000.00, NULL, '', NULL, NULL, 'processed', '2026-02-19 14:37:10', '2026-02-24 11:20:57'),
('90250f21-1fe6-417b-abbb-c9b309a38433', NULL, 'Adnane AITHAMOU', 'adnane@ycndev.com', '0632541230', NULL, NULL, '75000', 'finance', '', 0.00, NULL, 'matin', NULL, NULL, 'assigned', '2026-02-24 08:11:15', '2026-02-26 16:37:44'),
('933c4d52-b6a6-4db6-b959-4b61aeb0fb52', NULL, 'Test User 420', 'test9334@example.com', '0632980769', NULL, NULL, NULL, 'energie', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-11-11 18:56:13', '2026-02-27 08:55:17'),
('93fa8ebc-90e4-4168-af30-e1bca8185bf1', NULL, 'sqsfezg scq', 'mouad@ycndev.com', '0623541256', NULL, NULL, '21515', 'assurance', '', 0.00, NULL, 'matin', NULL, NULL, 'assigned', '2026-02-19 13:20:48', '2026-02-20 08:31:52'),
('95eabbb1-6f7d-4a0b-b78c-d09160ec0feb', NULL, 'Test User 173', 'test5318@example.com', '0665747674', NULL, NULL, NULL, 'renovation', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-09-06 00:58:45', '2026-02-27 08:55:17'),
('9669dea0-d3e3-4a71-9347-02b1a58a1e94', NULL, 'Test User 654', 'test5984@example.com', '0626571365', NULL, NULL, NULL, 'garage', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-12-02 17:27:34', '2026-02-27 16:49:46'),
('98271553-0307-4639-855a-f277307a7844', NULL, 'Test User 774', 'test9759@example.com', '0686220455', NULL, NULL, NULL, 'renovation', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2026-02-15 00:29:24', '2026-02-27 08:55:17'),
('9a53dbe9-9f98-4093-8bbd-00621e9da6b2', NULL, 'Test User 540', 'test8319@example.com', '0694472991', NULL, NULL, NULL, 'energie', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-11-08 02:33:54', '2026-02-27 08:55:17'),
('9cd785db-b724-4d29-9334-74cc7d841f41', NULL, 'Test User 585', 'test2280@example.com', '0621030042', NULL, NULL, NULL, 'energie', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-09-02 00:05:51', '2026-02-27 08:55:17'),
('a096b474-5387-4b33-a975-8c679be390c3', NULL, 'Test User 702', 'test4534@example.com', '0645420616', NULL, NULL, NULL, 'energie', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-09-22 06:10:21', '2026-02-27 08:55:17'),
('a19103a9-f65a-467b-80d1-f7faf4f37912', 'e8cfd9e0-e72f-4d84-8a95-a276c6ba531e', 'Client FZ', 'fbendriss0@gmail.com', '0762214100', '8500', 'Paris', '19 rue du bois', 'energie', 'goooooooo', 500.00, NULL, 'Heure precise (16:15)', '2026-03-11', NULL, 'pending', '2026-03-02 14:14:38', '2026-03-02 14:14:38'),
('a3222da8-a424-4e5c-b4ac-36a98d69465e', NULL, 'Test User 408', 'test6693@example.com', '0659795450', NULL, NULL, NULL, 'telecom', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'new', '2025-09-17 03:57:52', '2026-02-26 16:52:45'),
('a402487e-fe4b-4315-ab4c-c6127fbcc745', NULL, 'Test User 355', 'test3885@example.com', '0656410909', NULL, NULL, NULL, 'energie', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-11-13 00:59:09', '2026-02-27 08:55:17'),
('a42a2dd4-d3f5-4759-809c-b2f7f427fdf9', NULL, 'Test User 257', 'test1369@example.com', '0679048543', NULL, NULL, NULL, 'telecom', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'cancelled', '2025-09-02 08:31:47', '2026-02-26 16:52:45'),
('a54ce329-5b24-4736-8980-8b85c6222232', NULL, 'Test User 234', 'test1290@example.com', '0623920198', NULL, NULL, NULL, 'finance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-12-02 11:26:07', '2026-02-27 08:55:17'),
('a96c9c3b-7d33-4612-b2c5-722a7e3ccbad', NULL, 'ggj', 'ftft@gmail.com', '0632541210', NULL, NULL, 'jkhjv', 'renovation', 'jkhk', 200000.00, NULL, '', NULL, NULL, 'processed', '2026-02-19 14:44:59', '2026-02-25 08:42:55'),
('ab1b48e3-790a-433e-8f44-6bcceef78361', NULL, 'Test User 465', 'test2355@example.com', '0669952546', NULL, NULL, NULL, 'assurance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-12-07 02:48:16', '2026-02-27 12:40:33'),
('acf1fd6a-dce8-4f40-a0a7-ca1c838ab91f', NULL, 'Test User 140', 'test6441@example.com', '0624285879', NULL, NULL, NULL, 'energie', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2026-01-13 01:22:30', '2026-02-27 16:49:46'),
('aebee2c4-abf6-4335-a997-0735eb1ae43b', NULL, 'Test User 797', 'test1443@example.com', '0619883389', NULL, NULL, NULL, 'finance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2026-01-13 02:37:25', '2026-02-27 08:55:17'),
('af3bafe1-f60d-471b-b823-d5c0e1d518d5', NULL, 'KKKK', 'ggg@gmail.com', '065421555666', NULL, NULL, 'gbrrtr', 'renovation', 'gfbbg', 2000.00, NULL, 'Après-midi (14h – 18h)', NULL, NULL, 'quote_sent', '2026-02-19 15:30:27', '2026-02-27 14:17:13'),
('afba97e7-d0bd-4ae1-ad8c-50cd7652828f', NULL, 'Test User 194', 'test9062@example.com', '0649293935', NULL, NULL, NULL, 'energie', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-10-06 21:53:55', '2026-02-27 08:55:17'),
('b6a7b1b8-865c-4ff1-8f0b-6b071c410592', NULL, 'Test User 146', 'test1344@example.com', '0699062400', NULL, NULL, NULL, 'finance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2026-01-17 23:43:27', '2026-02-27 08:55:17'),
('bd6485c9-bdde-4af5-b2b5-26369885e8be', NULL, 'Test User 569', 'test9128@example.com', '0646875644', NULL, NULL, NULL, 'telecom', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'confirmé', '2025-11-18 16:58:22', '2026-02-26 16:52:45'),
('c0f59a0e-a31e-4170-b969-1abbac8efbb9', NULL, 'Test User 715', 'test9369@example.com', '0640988047', NULL, NULL, NULL, 'assurance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2026-01-26 00:30:21', '2026-02-27 12:40:33'),
('c4319054-c232-4886-95dd-94c8fdf3ea28', NULL, 'Mouad Benmekki', 'mouad@ycndev.com', '0632541200', NULL, NULL, '26000', 'telecom', '', 300.00, NULL, 'Midi (12h – 14h)', NULL, NULL, 'quote_sent', '2026-02-25 15:30:44', '2026-03-02 10:33:45'),
('c4af6bce-9906-42f9-b730-18d415675138', NULL, 'Test User 738', 'test5222@example.com', '0691498044', NULL, NULL, NULL, 'finance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-10-19 16:27:58', '2026-02-27 08:55:17'),
('ccd28913-8e1a-49c8-86a1-25fe6d621c57', NULL, 'Test User 145', 'test2863@example.com', '0630351560', NULL, NULL, NULL, 'telecom', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'confirmé', '2026-01-14 11:33:50', '2026-02-26 16:52:45'),
('d320bcd3-7784-46e2-9978-831fcbb4d864', NULL, 'ddhzgzr zrgrz', 'yassir@ycndev.com', '0623541256', NULL, NULL, '54454', 'assurance', '', 0.00, NULL, 'matin', NULL, NULL, 'assigned', '2026-02-20 09:17:40', '2026-02-23 08:18:58'),
('d5c2225a-84ac-4e47-8a9e-6a6e10a2e465', NULL, 'Test User 500', 'test6069@example.com', '0624513794', NULL, NULL, NULL, 'telecom', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'closed', '2025-09-02 05:58:28', '2026-02-26 16:52:45'),
('da5f8343-c973-42b6-810f-632dd1bdbfb1', NULL, 'Test User 409', 'test4515@example.com', '0676319745', NULL, NULL, NULL, 'assurance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-11-02 03:08:12', '2026-02-27 12:40:33'),
('dc13b224-3f91-4101-8b09-1105f867f595', NULL, 'Test User 575', 'test6383@example.com', '0681700247', NULL, NULL, NULL, 'telecom', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'processed', '2025-11-18 21:49:36', '2026-02-26 16:52:45'),
('ddb14b29-14ab-4d8e-9e1c-0f98913bc35c', NULL, 'Test User 766', 'test7312@example.com', '0610521286', NULL, NULL, NULL, 'finance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-09-12 17:16:15', '2026-02-27 08:55:17'),
('dfdab265-f9a5-49b9-8294-3be5e673a7cb', NULL, 'Test User 611', 'test8971@example.com', '0634489567', NULL, NULL, NULL, 'telecom', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'confirmé', '2025-11-02 19:19:30', '2026-02-26 16:52:45'),
('e35469f5-2ee6-4319-ae71-108e65c9369a', NULL, 'Test User 798', 'test2251@example.com', '0652464385', NULL, NULL, NULL, 'finance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-09-04 19:58:34', '2026-02-27 08:55:17'),
('e46d55a6-9030-4642-92fe-777977fde71a', NULL, 'Test User 885', 'test4185@example.com', '0671251349', NULL, NULL, NULL, 'assurance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2026-02-17 06:50:11', '2026-02-27 12:40:34'),
('e6e689d4-7354-491c-a1aa-dd97bf5f0d07', 'e8cfd9e0-e72f-4d84-8a95-a276c6ba531e', 'Client Test', 'fbendriss0@gmail.com', '0762214100', '20000', 'Paris', '', 'garage', 'je souhaite comparer des offres d&#039;assurance', 2000.00, NULL, 'Midi (12h – 14h)', NULL, NULL, 'completed', '2026-02-27 14:02:42', '2026-03-02 14:26:33'),
('e78afb50-fe00-44f0-89ae-1800debcc351', NULL, 'Fatima ez-zahraa BENDRISS', 'fatimaezahra@ycndev.com', '07224125200', NULL, NULL, '52000', 'assurance', '', 0.00, NULL, 'apres-midi', NULL, NULL, 'pending', '2026-02-24 08:33:07', '2026-02-24 08:33:07'),
('ecd2dbec-605a-4a63-973d-675a396ce961', NULL, 'Test User 589', 'test7929@example.com', '0670358047', NULL, NULL, NULL, 'garage', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-11-17 23:21:58', '2026-02-27 08:55:17'),
('ee8bc784-207e-443a-8dfc-166053b53f7b', NULL, 'Test User 416', 'test8428@example.com', '0676718178', NULL, NULL, NULL, 'renovation', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2026-01-15 07:10:00', '2026-02-27 08:55:17'),
('efd871f2-c7cc-4d73-9923-8e9d61c6dca4', NULL, 'Test User 660', 'test3479@example.com', '0693857361', NULL, NULL, NULL, 'telecom', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'pending', '2026-01-11 01:58:56', '2026-02-26 16:52:45'),
('f0872bf3-3875-43ac-857e-bc1f21edc80a', NULL, 'Test User 367', 'test6524@example.com', '0661073664', NULL, NULL, NULL, 'garage', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2026-02-24 16:25:23', '2026-02-27 16:49:46'),
('f0dc8528-adc2-4a78-89d4-db73eefdcf78', NULL, 'Test User 476', 'test6112@example.com', '0642364075', NULL, NULL, NULL, 'finance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-12-11 12:27:24', '2026-02-27 08:55:17'),
('f3979c20-8b20-4608-a21f-da4cda494f50', NULL, 'Test User 529', 'test4039@example.com', '0650227994', NULL, NULL, NULL, 'finance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-10-27 13:58:54', '2026-02-27 08:55:17'),
('f59c542b-5b25-4d3c-a92b-5536f6b9b758', 'e8cfd9e0-e72f-4d84-8a95-a276c6ba531e', 'Client Test', 'fbendriss0@gmail.com', '0762214100', '', '', 'adresssssss', 'telecom', 'télécom', 9500.00, NULL, 'Après-midi (14h – 18h)', NULL, NULL, '', '2026-02-27 11:21:30', '2026-02-27 15:20:37'),
('f7f3a0a0-75dc-4559-b312-9b0b26b95218', NULL, 'Test User 690', 'test3542@example.com', '0650207344', NULL, NULL, NULL, 'garage', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2026-02-24 11:21:11', '2026-02-27 16:49:46'),
('f91eda6b-cdd2-4ea4-a6e7-0557d88443ea', NULL, 'Test User 897', 'test8138@example.com', '0670202195', NULL, NULL, NULL, 'energie', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2026-01-25 14:10:11', '2026-02-27 16:49:46'),
('f95bcc74-998f-4f1f-8efa-91c7742bfd30', NULL, 'Test User 905', 'test2397@example.com', '0648375437', NULL, NULL, NULL, 'assurance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-11-20 05:33:47', '2026-02-27 12:40:33'),
('fe1603e3-8309-496e-a2af-a7ea3aa25c68', NULL, 'Test User 312', 'test1056@example.com', '0684891892', NULL, NULL, NULL, 'finance', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-12-07 23:36:23', '2026-02-27 08:55:17'),
('fe34de6b-ab73-43ac-95aa-dbc3dd00c38e', NULL, 'Test User 416', 'test9596@example.com', '0615148992', NULL, NULL, NULL, 'garage', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-09-13 07:27:31', '2026-02-27 08:55:17'),
('ffc9c799-6e9b-4b4e-8989-e19dfb2a86f2', NULL, 'Test User 717', 'test7391@example.com', '0679522186', NULL, NULL, NULL, 'garage', NULL, 0.00, NULL, 'Non spécifié', NULL, NULL, 'assigned', '2025-11-05 18:21:21', '2026-02-27 08:55:17');

-- --------------------------------------------------------

--
-- Structure de la table `lead_assignments`
--

CREATE TABLE `lead_assignments` (
  `id` char(36) NOT NULL,
  `lead_id` char(36) NOT NULL,
  `provider_id` char(36) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lead_assignments`
--

INSERT INTO `lead_assignments` (`id`, `lead_id`, `provider_id`, `created_at`) VALUES
('04673ac7-f248-4a4a-b82d-4c43eaddee8c', '14cf60dc-921f-42e0-bfbc-1adf2b0a773a', 'c7f18aee-f14b-483a-8792-4967de52cf77', '2026-02-27 08:55:17'),
('04adae45-f379-4bce-8b6f-554e536547e3', '93fa8ebc-90e4-4168-af30-e1bca8185bf1', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-20 08:31:52'),
('0643e1dd-49eb-41a4-ba15-db5d22eeaa91', 'c4319054-c232-4886-95dd-94c8fdf3ea28', '5cabdddf-53f2-4215-8c47-705eb26fc10b', '2026-02-26 10:51:56'),
('09784a81-d0d2-4db4-90d6-c4707b5abf4b', 'aebee2c4-abf6-4335-a997-0735eb1ae43b', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-27 08:55:17'),
('11b6d64c-d1bb-44c5-9879-3cc7132864e4', 'afba97e7-d0bd-4ae1-ad8c-50cd7652828f', 'c7f18aee-f14b-483a-8792-4967de52cf77', '2026-02-27 08:55:17'),
('1f3e1f37-7317-400d-bf85-473d989b3aed', '06a10473-4a90-42a2-bb02-637ff4d96b90', 'c7f18aee-f14b-483a-8792-4967de52cf77', '2026-02-23 08:18:58'),
('2feb395c-c247-419b-8f8d-643e37ea0d93', '778e99d8-81cf-4616-ba1d-c8644319671d', '5cabdddf-53f2-4215-8c47-705eb26fc10b', '2026-02-26 14:13:29'),
('3423a9bd-15ab-44a6-8f68-8701e7084f86', 'e78afb50-fe00-44f0-89ae-1800debcc351', '5cabdddf-53f2-4215-8c47-705eb26fc10b', '2026-02-26 13:33:38'),
('43c5380d-bb25-44e2-bf4e-e1389cc2cf64', '98271553-0307-4639-855a-f277307a7844', '1ebf2b57-44c8-4095-8613-fbc1fce34079', '2026-02-27 08:55:17'),
('45f7e882-d8fc-4c75-9edc-4419f59a60b8', '8b6a7d9c-b59c-495a-b2aa-d043f0de0532', '1ebf2b57-44c8-4095-8613-fbc1fce34079', '2026-02-26 16:37:44'),
('4a31395e-04d4-4e51-93b4-b93f89a4af71', 'a96c9c3b-7d33-4612-b2c5-722a7e3ccbad', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-19 14:44:59'),
('4ae80162-dc39-400a-87d9-efcc8d3d10f4', 'ffc9c799-6e9b-4b4e-8989-e19dfb2a86f2', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-27 08:55:17'),
('52881ef8-8c3e-4905-89ba-7c89fb20a547', '7919f120-309e-41a1-b9cd-00e31f9feb0a', '1ebf2b57-44c8-4095-8613-fbc1fce34079', '2026-02-27 08:55:17'),
('562c0af8-b2e8-4d2a-8bdf-3fcf2d0aa657', '4956d5f9-6833-4cee-b806-df70c845ca2c', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-03-02 14:24:35'),
('6448bbbe-1ed2-4772-afe4-a4402b43ddfa', 'ab1b48e3-790a-433e-8f44-6bcceef78361', '5cabdddf-53f2-4215-8c47-705eb26fc10b', '2026-02-27 12:40:33'),
('64721b5d-5f93-48c1-96c2-000d4f9e8f8d', '4d55dd82-89e7-40bd-99fe-c3e56f9114a6', '5cabdddf-53f2-4215-8c47-705eb26fc10b', '2026-02-26 14:44:22'),
('64a66497-f1ef-4c1a-b134-360fe47cd406', 'f0872bf3-3875-43ac-857e-bc1f21edc80a', '1ebf2b57-44c8-4095-8613-fbc1fce34079', '2026-02-27 16:49:46'),
('64a6f018-3038-4a23-9564-a4d537072c14', '8f18f8a2-8730-4c7f-b22a-3a21f75ce9dc', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-20 08:31:52'),
('6739a514-6f72-4777-9ec7-712934c87e68', 'f95bcc74-998f-4f1f-8efa-91c7742bfd30', '5cabdddf-53f2-4215-8c47-705eb26fc10b', '2026-02-27 12:40:33'),
('690886ee-3967-4d5c-bfc5-35070d704726', 'a096b474-5387-4b33-a975-8c679be390c3', 'c7f18aee-f14b-483a-8792-4967de52cf77', '2026-02-27 08:55:17'),
('6bf1b164-f2dd-4596-be0a-4072a2b14d82', 'c4af6bce-9906-42f9-b730-18d415675138', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-27 08:55:17'),
('6c4ff47f-0fe6-4987-8332-a026369df386', 'a402487e-fe4b-4315-ab4c-c6127fbcc745', 'c7f18aee-f14b-483a-8792-4967de52cf77', '2026-02-27 08:55:17'),
('6d570779-ed35-43d7-beb3-1c394aa47dd6', '9a53dbe9-9f98-4093-8bbd-00621e9da6b2', 'c7f18aee-f14b-483a-8792-4967de52cf77', '2026-02-27 08:55:17'),
('6df8518a-4253-405a-9f53-44caef99d449', 'ddb14b29-14ab-4d8e-9e1c-0f98913bc35c', '38969070-6741-4f2d-b0dd-b76cf4b3956c', '2026-02-27 08:55:17'),
('6fb9abb1-15d7-42e8-a723-460c3ae90f7e', 'fe34de6b-ab73-43ac-95aa-dbc3dd00c38e', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-27 08:55:17'),
('73363b47-a7d2-458f-b71e-8e67ed48c068', 'f7f3a0a0-75dc-4559-b312-9b0b26b95218', '1ebf2b57-44c8-4095-8613-fbc1fce34079', '2026-02-27 16:49:46'),
('753a5b42-8349-43a4-9ab3-7bb8d9e1dfc0', '7897ea5d-89c6-49ee-b0f1-548783cffa25', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-27 16:27:27'),
('77329882-d812-46af-b4c9-a52b3baafa0f', '35536ccd-64ae-42d2-ab2f-85426b24af0a', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-27 08:55:17'),
('7ca88329-6af2-406d-9bf0-cbf6c74894ed', '9cd785db-b724-4d29-9334-74cc7d841f41', 'c7f18aee-f14b-483a-8792-4967de52cf77', '2026-02-27 08:55:17'),
('842a49a9-b865-4961-9959-c5ab3daabf4f', '7b68df98-12f5-4514-89eb-c08a175808a5', '5cabdddf-53f2-4215-8c47-705eb26fc10b', '2026-02-27 12:40:33'),
('84f426fc-3cba-4c21-8b8c-db71517a3997', '17754028-5544-459a-b240-611de5a8d842', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-27 15:20:17'),
('8b18e60d-2138-4a77-855e-3b9a8eefab62', 'ee8bc784-207e-443a-8dfc-166053b53f7b', '1ebf2b57-44c8-4095-8613-fbc1fce34079', '2026-02-27 08:55:17'),
('9602aaf6-4cba-427a-8411-59c323919c43', 'f59c542b-5b25-4d3c-a92b-5536f6b9b758', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-27 15:20:27'),
('a17141d9-db1f-4125-a0d8-e1cd7237d317', '95eabbb1-6f7d-4a0b-b78c-d09160ec0feb', 'c7f18aee-f14b-483a-8792-4967de52cf77', '2026-02-27 08:55:17'),
('a2febbe9-5512-44f0-9574-79c663317653', 'fe1603e3-8309-496e-a2af-a7ea3aa25c68', '38969070-6741-4f2d-b0dd-b76cf4b3956c', '2026-02-27 08:55:17'),
('aa2d62d2-021c-40d9-8526-76e062c05dce', '8d5966bc-9cd1-45ca-95e6-c0b068c1c5a6', '1ebf2b57-44c8-4095-8613-fbc1fce34079', '2026-02-27 08:55:17'),
('ae586358-1eff-4cd8-97f1-620e042961e9', '2dee4683-69e7-4313-81eb-fb6e8dc31c27', 'c7f18aee-f14b-483a-8792-4967de52cf77', '2026-02-27 16:49:46'),
('b1a5b21e-00e6-4f99-9875-ba7014990db1', '798c0967-894a-434c-a792-569b54ea9813', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-27 08:55:17'),
('b2971cb5-6912-48e2-9072-d2438bd8d7c3', '8d98b686-dc21-49f9-8b68-2e60eb9b2e9d', '38969070-6741-4f2d-b0dd-b76cf4b3956c', '2026-02-27 08:55:17'),
('b4dd435b-fe74-47d2-ac07-5c9dabc1eb2a', 'af3bafe1-f60d-471b-b823-d5c0e1d518d5', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-19 15:30:27'),
('b51c742c-4582-42c0-b5cc-f7479eb41ac5', 'c0f59a0e-a31e-4170-b969-1abbac8efbb9', '5cabdddf-53f2-4215-8c47-705eb26fc10b', '2026-02-27 12:40:33'),
('bf1568fd-468e-4d87-a1f9-fad42f1c3e8f', 'f91eda6b-cdd2-4ea4-a6e7-0557d88443ea', 'c7f18aee-f14b-483a-8792-4967de52cf77', '2026-02-27 16:49:46'),
('bfb20bf8-fed3-4068-9a3a-769ae1e2cd47', 'e46d55a6-9030-4642-92fe-777977fde71a', '5cabdddf-53f2-4215-8c47-705eb26fc10b', '2026-02-27 12:40:33'),
('c3a43079-6926-4494-baa2-a59028dad5b4', '7b81d83f-3758-4a03-96d9-ce6107f57731', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-20 08:31:52'),
('c5c7ebd7-5af6-4071-b6b5-5e655a4b3738', 'da5f8343-c973-42b6-810f-632dd1bdbfb1', '5cabdddf-53f2-4215-8c47-705eb26fc10b', '2026-02-27 12:40:33'),
('c63dfb6f-e8ef-476c-9f43-46be0e1c9fb2', 'acf1fd6a-dce8-4f40-a0a7-ca1c838ab91f', 'c7f18aee-f14b-483a-8792-4967de52cf77', '2026-02-27 16:49:46'),
('cb3a2419-510c-4a20-bf2e-a6d782ba667e', 'a54ce329-5b24-4736-8980-8b85c6222232', '38969070-6741-4f2d-b0dd-b76cf4b3956c', '2026-02-27 08:55:17'),
('cf52992b-3dee-4422-825f-f70c28005693', 'f3979c20-8b20-4608-a21f-da4cda494f50', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-27 08:55:17'),
('d0a0b75d-bfcf-45d3-938d-962eb7f75f1b', '148665ab-f352-490d-ad29-fb3cac4514ec', '1ebf2b57-44c8-4095-8613-fbc1fce34079', '2026-02-26 16:08:34'),
('d110c913-019a-44c3-9f47-22a6bc33d6ff', 'f0dc8528-adc2-4a78-89d4-db73eefdcf78', '38969070-6741-4f2d-b0dd-b76cf4b3956c', '2026-02-27 08:55:17'),
('d5af0f67-f651-40a7-8ae7-6a0b1018e839', '90250f21-1fe6-417b-abbb-c9b309a38433', '38969070-6741-4f2d-b0dd-b76cf4b3956c', '2026-02-26 16:37:44'),
('d606a34f-8ff7-4f7c-9477-27f833c2dfb3', '87ff3ea3-6090-4c1f-9cef-1b8a2e8ef9bc', 'c7f18aee-f14b-483a-8792-4967de52cf77', '2026-02-27 08:55:17'),
('d6f26b13-ebc7-4814-8c07-d7a1955e28d7', '3322af7f-564f-4a61-9098-6b6025e453bd', '5cabdddf-53f2-4215-8c47-705eb26fc10b', '2026-02-27 12:40:33'),
('dd316ec9-0e2f-424a-aaed-ce6e4c09a8ee', 'd320bcd3-7784-46e2-9978-831fcbb4d864', 'c7f18aee-f14b-483a-8792-4967de52cf77', '2026-02-23 08:18:58'),
('e36568c4-a460-4821-80c5-768ef3371a7d', '9669dea0-d3e3-4a71-9347-02b1a58a1e94', '1ebf2b57-44c8-4095-8613-fbc1fce34079', '2026-02-27 16:49:46'),
('e3f3ecd1-fa8f-4e93-85eb-3d02c7d9c7f7', '933c4d52-b6a6-4db6-b959-4b61aeb0fb52', 'c7f18aee-f14b-483a-8792-4967de52cf77', '2026-02-27 08:55:17'),
('e4c1efcf-225d-4ad7-99dd-26126358bc80', 'b6a7b1b8-865c-4ff1-8f0b-6b071c410592', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-27 08:55:17'),
('eb59dc11-73f5-4c21-bb88-057bb6809dcc', 'e35469f5-2ee6-4319-ae71-108e65c9369a', '38969070-6741-4f2d-b0dd-b76cf4b3956c', '2026-02-27 08:55:17'),
('f400bfe4-13ff-4a03-9a31-78ba4b0b5afe', 'e6e689d4-7354-491c-a1aa-dd97bf5f0d07', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-27 14:37:16'),
('f82b0707-afbb-411c-8a23-0c2f9930fdb3', 'ecd2dbec-605a-4a63-973d-675a396ce961', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-27 08:55:17'),
('f892bd7c-d50e-4f80-aeb8-cd9ab5e3184b', '81a1b8d5-5fa7-48df-a960-77a7e53db421', '5cabdddf-53f2-4215-8c47-705eb26fc10b', '2026-02-26 10:56:52'),
('fc8915a1-3d23-4999-9ebc-9a2556f22f58', '088fae5d-f7a2-4674-9e98-2e2c8bae85c6', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '2026-02-20 08:31:52');

-- --------------------------------------------------------

--
-- Structure de la table `lead_interactions`
--

CREATE TABLE `lead_interactions` (
  `id` char(36) NOT NULL,
  `lead_id` char(36) NOT NULL,
  `provider_id` char(36) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lead_interactions`
--

INSERT INTO `lead_interactions` (`id`, `lead_id`, `provider_id`, `comment`, `created_at`) VALUES
('4535d975-138f-441f-9e39-9d72ca8e81fe', 'c4319054-c232-4886-95dd-94c8fdf3ea28', '5cabdddf-53f2-4215-8c47-705eb26fc10b', 'disponible - plombier', '2026-02-26 10:53:43'),
('4d450539-9bed-4622-ac6c-81b7af299eb4', 'c4319054-c232-4886-95dd-94c8fdf3ea28', '5cabdddf-53f2-4215-8c47-705eb26fc10b', 'dispooo', '2026-02-26 11:00:59'),
('8dc1a855-59ca-477d-ab1f-c59028ff1fc2', 'e6e689d4-7354-491c-a1aa-dd97bf5f0d07', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', 'c bien', '2026-02-27 16:29:31'),
('fa4a3890-caab-4f8f-8944-eee816cb3818', 'c4319054-c232-4886-95dd-94c8fdf3ea28', '5cabdddf-53f2-4215-8c47-705eb26fc10b', 'test', '2026-02-26 11:01:25');

-- --------------------------------------------------------

--
-- Structure de la table `quotes`
--

CREATE TABLE `quotes` (
  `id` char(36) NOT NULL,
  `provider_id` char(36) NOT NULL,
  `lead_id` char(36) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `items_count` int(11) DEFAULT 1,
  `status` varchar(20) DEFAULT 'attente_client',
  `doc_path` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quotes`
--

INSERT INTO `quotes` (`id`, `provider_id`, `lead_id`, `client_name`, `project_name`, `amount`, `items_count`, `status`, `doc_path`, `created_at`, `updated_at`) VALUES
('0f2b315b-2f64-468e-a7a2-a5a92db000e9', '5cabdddf-53f2-4215-8c47-705eb26fc10b', 'c4319054-c232-4886-95dd-94c8fdf3ea28', 'Mouad Benmekki', 'plombier', 1200.00, 1, 'sent', NULL, '2026-02-26 10:37:09', '2026-02-26 13:28:46'),
('27c805d8-b469-4405-91ef-374831efe490', '5cabdddf-53f2-4215-8c47-705eb26fc10b', '778e99d8-81cf-4616-ba1d-c8644319671d', 'Hicham BEN', 'vvv', 600.00, 1, 'completed', NULL, '2026-02-26 14:41:07', '2026-03-02 13:17:30'),
('47fad8f9-72ba-4a05-a881-40f4d0ef963b', '1ebf2b57-44c8-4095-8613-fbc1fce34079', NULL, 'khalil', 'cfhfjf', 3333.00, 1, 'sent', NULL, '2026-02-20 11:36:17', '2026-02-20 11:36:17'),
('517ad8d8-138a-4bb4-af0d-7f3f5790c56e', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', NULL, 'khalil', 'Nouveau Projet', 9000.00, 1, 'attente_client', NULL, '2026-02-19 14:49:22', '2026-02-19 14:49:22'),
('8806e66c-d094-4521-8e96-5114602affee', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', NULL, 'mouad', 'Nouveau projet', 89000.00, 1, 'rejected', NULL, '2026-02-19 14:49:39', '2026-02-25 09:02:08'),
('8d282524-8284-44dd-89b2-d91c1dd409e2', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '4956d5f9-6833-4cee-b806-df70c845ca2c', 'Client FZ', 'ggg', 2900.00, 1, 'rejected', '/rappel/public/uploads/quotes/d1fbcb16-2983-4975-bd45-7401ca7f6e5b/20260302_160011_1586d3e4c410.png', '2026-03-02 15:00:11', '2026-03-02 15:01:35'),
('98ae3eb2-5e6b-41f9-bd1a-abaf2ef49d92', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', NULL, 'aziz', 'Nouveau Projet', 650000.00, 1, 'draft', NULL, '2026-02-19 14:48:15', '2026-02-25 09:03:39'),
('c36e60ae-131a-485c-b586-8b4f4ead3739', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', '17754028-5544-459a-b240-611de5a8d842', 'hicham test', 'helios perfume', 7000.00, 1, 'accepted', NULL, '2026-02-25 08:59:46', '2026-03-02 10:15:14'),
('f27bb724-ed9f-4945-8649-581c5b0ef3c4', '5cabdddf-53f2-4215-8c47-705eb26fc10b', '778e99d8-81cf-4616-ba1d-c8644319671d', 'Adnane', 'renov', 3000.00, 1, 'completed', NULL, '2026-02-26 13:29:38', '2026-03-02 11:47:36'),
('f2ed6861-d117-4b03-bbaf-0c68c68270b2', 'd1fbcb16-2983-4975-bd45-7401ca7f6e5b', 'e6e689d4-7354-491c-a1aa-dd97bf5f0d07', 'Client Test', 'nv moteur', 1000.00, 1, 'completed', NULL, '2026-02-27 16:38:10', '2026-03-02 14:35:53');

-- --------------------------------------------------------

--
-- Structure de la table `quote_documents`
--

CREATE TABLE `quote_documents` (
  `id` char(36) NOT NULL,
  `quote_id` char(36) NOT NULL,
  `doc_path` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quote_documents`
--

INSERT INTO `quote_documents` (`id`, `quote_id`, `doc_path`, `created_at`) VALUES
('0de5bd40-03e4-4b0f-9e6d-4e03e6ec04cc', '8d282524-8284-44dd-89b2-d91c1dd409e2', '/rappel/public/uploads/quotes/d1fbcb16-2983-4975-bd45-7401ca7f6e5b/20260302_160011_1586d3e4c410.png', '2026-03-02 15:00:11');

-- --------------------------------------------------------

--
-- Structure de la table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `id` char(36) NOT NULL,
  `name` varchar(50) NOT NULL,
  `stripe_price_id` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `max_leads` int(11) DEFAULT 0,
  `currency` varchar(3) DEFAULT 'EUR',
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `subscription_plans`
--

INSERT INTO `subscription_plans` (`id`, `name`, `stripe_price_id`, `price`, `max_leads`, `currency`, `features`, `created_at`) VALUES
('a364b47a88889084', 'Flexibilité', 'prod_U3TR1ZAPhLiEgk', 12.00, 0, 'EUR', '[\"Z\\u00e9ro co\\u00fbt fixe hebdomadaire\",\"Volume illimit\\u00e9\",\"Recharge cr\\u00e9dit instantan\\u00e9e\",\"Acc\\u00e8s dashboard global\"]', '2026-02-27 09:39:32'),
('df01999a8b0e0ac5', 'Accéleration', 'prod_U3TYhOZEUOsxIt', 249.00, 30, 'EUR', '[\"30 leads haute qualit\\u00e9\",\"Ciblage r\\u00e9gional illimit\\u00e9\",\"CRM int\\u00e9gr\\u00e9 avec API\",\"Support VIP 24\\/7\",\"Garantie de remplacement lead\"]', '2026-02-27 09:39:32'),
('e86990868552c231', 'Croissance', 'prod_U3TZrPppDgFDP5', 99.00, 10, 'EUR', '[\"10 leads qualifi\\u00e9s inclus\",\"1 secteur d\'activit\\u00e9\",\"Ciblage d\\u00e9partemental\",\"Preuves de consentement SMS\"]', '2026-02-27 09:39:32');

-- --------------------------------------------------------

--
-- Structure de la table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` char(36) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `job_title` varchar(100) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `siret` varchar(20) DEFAULT NULL,
  `legal_form` varchar(50) DEFAULT NULL,
  `creation_year` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'provider',
  `plan_id` char(36) DEFAULT NULL,
  `lead_credits` int(11) DEFAULT 0,
  `subscription_status` varchar(20) DEFAULT 'inactive',
  `stripe_customer_id` varchar(255) DEFAULT NULL,
  `verification_code` varchar(100) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `sectors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sectors`)),
  `description` text DEFAULT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `certifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`certifications`)),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `email`, `password`, `first_name`, `last_name`, `job_title`, `company_name`, `siret`, `legal_form`, `creation_year`, `address`, `zip`, `city`, `phone`, `role`, `plan_id`, `lead_credits`, `subscription_status`, `stripe_customer_id`, `verification_code`, `is_verified`, `reset_token`, `reset_expires`, `sectors`, `description`, `zone`, `certifications`, `created_at`, `updated_at`) VALUES
('1ebf2b57-44c8-4095-8613-fbc1fce34079', 'hicham@ycndev.com', '$2y$10$cOvx822.vxU713JlIsHB..dSc9ApEDNHlVL/ddksuw/SdGHXXHSCK', 'fhdh', 'fshr', NULL, 'SOCIETE CIVILE YLN', '48922852800027', '6599', 2006, '7 RUE NIEPCE 75014 PARIS', '75014', 'PARIS', '06325412565', 'provider', NULL, 47, 'active', NULL, NULL, 1, NULL, NULL, '[\"garage\",\"renovation\"]', NULL, NULL, NULL, '2026-02-20 11:33:28', '2026-02-27 16:49:46'),
('38969070-6741-4f2d-b0dd-b76cf4b3956c', 'adnane@ycndev.com', '$2y$10$fPyD5hk32D1j473QQDwKcekRcspTwKiTp/DB1uD27Dknya0oCLpUG', 'Adnane', 'AITHAMOU', NULL, 'SECE.STA SAS', '48514532000068', 'SAS', 2005, '71 RUE JEAN JAURES 62575 BLENDECQUES', '62575', 'BLENDECQUES', '0632541100', 'provider', NULL, 50, 'inactive', NULL, NULL, 1, NULL, NULL, '[\"finance\",\"assurance\"]', NULL, NULL, NULL, '2026-02-24 09:08:18', '2026-02-27 16:47:11'),
('4548d08f-8014-4bd7-8620-7a5f7e014a46', 'benabbeshicham1@gmail.com', '$2y$10$.CiTcz09h5qE9mmHy6GWauZeTjBU6F66C9JsE6kUe3HX21vUkvTPG', 'hicham', 'benabbes', NULL, 'ROYAUME DU PARFUM INTERNATIONAL', '40818281400074', '', 0, '46 RUE POISSONNIERE 75002 PARIS', '75002', 'PARIS', '0696435720', 'provider', NULL, 0, 'inactive', NULL, NULL, 1, NULL, NULL, '[\"autre\"]', 'bonjour et bienvenue', 'national', NULL, '2026-02-27 16:17:25', '2026-02-27 16:17:43'),
('5cabdddf-53f2-4215-8c47-705eb26fc10b', 'yassir@ycndev.com', '$2y$10$eF4lQULyM8HQ/aOjJnUucO.m.j0yk1sOcAAk9n5LtEMAnmb6lCNCy', 'Yassir', 'A', NULL, 'HELIOS B', '75317440800033', 'SAS', 2012, 'FAUBOURG DE L ARCHE 1 PLACE SAMUEL DE CHAMPLAIN 92400 COURBEVOIE', '92444', 'COURBEVOIE', '0696435720', 'provider', NULL, 50, 'active', NULL, NULL, 1, NULL, NULL, '[\"assurance\",\"banque\"]', '', '', NULL, '2026-02-25 08:13:02', '2026-03-02 15:29:13'),
('6ae83452-ebd9-4c97-b661-1b3f028dfb2b', 'admin@rappel.com', '$2y$10$GFdnXImG8IXSF/N3C9AH6eFxAa3b0EGj4sBgy.fdHZMU13gAFb2b2', 'Admin', 'Admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'admin', NULL, 25, 'active', NULL, NULL, 1, NULL, NULL, '[]', NULL, NULL, NULL, '2026-02-19 16:15:44', '2026-02-26 16:04:39'),
('c7f18aee-f14b-483a-8792-4967de52cf77', 'mouad@ycndev.com', '$2y$10$Zm4JaiZlJLuuWkmUTWlvwOKcYBeekM5cldbnZivuXB.xAJtyguxDW', 'Mouad', 'BEN', NULL, '', '75317440800025', 'SAS', 2018, '3 B RUE DE L\'ETANG DE LA TOUR 78120 RAMBOUILLET', '78120', 'RAMBOUILLET', '', 'provider', NULL, 47, 'active', NULL, NULL, 1, NULL, NULL, '[\"renovation\",\"energie\"]', NULL, NULL, NULL, '2026-02-19 15:56:01', '2026-02-27 16:49:46'),
('d1fbcb16-2983-4975-bd45-7401ca7f6e5b', 'fatimaezahra@ycndev.com', '$2y$10$nm7P.UOMWUVP9itMCnVgVuQIlzVXZdMXhobw8uMyxDLidZ5McDjhC', 'Fatimaezzahraa', 'FZ', NULL, 'YY', '', '', 200444, 'dcskjhkdc', '12032', 'dcdcd', '', 'provider', NULL, 44, 'active', NULL, NULL, 1, NULL, NULL, '[\"garage\",\"finance\"]', '', '', NULL, '2026-02-19 12:49:09', '2026-03-02 14:24:35'),
('e8cfd9e0-e72f-4d84-8a95-a276c6ba531e', 'fbendriss0@gmail.com', '$2y$10$mMnEZ9sycJaPTgR/qq/3kudToun3Jl.WmBDmeuAQENNWSfHNpNN82', 'Client', 'FZ', NULL, '', NULL, NULL, NULL, '', '', '', '0762214100', 'client', NULL, 0, 'inactive', NULL, NULL, 1, NULL, NULL, '[]', NULL, NULL, NULL, '2026-02-26 10:34:18', '2026-03-02 13:42:23');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `client_partner_notes`
--
ALTER TABLE `client_partner_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cpn_provider` (`provider_id`),
  ADD KEY `idx_cpn_client` (`client_id`),
  ADD KEY `idx_cpn_lead` (`lead_id`),
  ADD KEY `idx_cpn_created` (`created_at`);

--
-- Index pour la table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Index pour la table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lead_user` (`user_id`);

--
-- Index pour la table `lead_assignments`
--
ALTER TABLE `lead_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_id` (`lead_id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Index pour la table `lead_interactions`
--
ALTER TABLE `lead_interactions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `quotes`
--
ALTER TABLE `quotes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_id` (`provider_id`),
  ADD KEY `lead_id` (`lead_id`);

--
-- Index pour la table `quote_documents`
--
ALTER TABLE `quote_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_quote_documents_quote_id` (`quote_id`);

--
-- Index pour la table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `stripe_price_id` (`stripe_price_id`);

--
-- Index pour la table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `siret` (`siret`),
  ADD UNIQUE KEY `stripe_customer_id` (`stripe_customer_id`),
  ADD KEY `idx_user_email` (`email`),
  ADD KEY `idx_user_siret` (`siret`),
  ADD KEY `fk_user_plan` (`plan_id`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `user_profiles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `fk_lead_user` FOREIGN KEY (`user_id`) REFERENCES `user_profiles` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `lead_assignments`
--
ALTER TABLE `lead_assignments`
  ADD CONSTRAINT `lead_assignments_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_assignments_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `user_profiles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `quotes`
--
ALTER TABLE `quotes`
  ADD CONSTRAINT `quotes_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `user_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quotes_ibfk_2` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `fk_user_plan` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
