-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2020. Aug 28. 14:43
-- Kiszolgáló verziója: 10.1.37-MariaDB
-- PHP verzió: 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `videouploader`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- A tábla adatainak kiíratása `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1598611055),
('m200826_085728_create_video_table', 1598611059),
('m200828_103451_init_video_table', 1598611077);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `video`
--

CREATE TABLE `video` (
  `id` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `quality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- A tábla adatainak kiíratása `video`
--

INSERT INTO `video` (`id`, `extension`, `status`, `quality`) VALUES
('JjfGDio4mnp', 'mp4', 0, NULL),
('pCq08PgrZTK', 'mp4', 1, '{\"360\":true,\"720\":true}');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- A tábla indexei `video`
--
ALTER TABLE `video`
  ADD UNIQUE KEY `id` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
