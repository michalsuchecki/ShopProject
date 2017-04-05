-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 01 Cze 2012, 12:01
-- Wersja serwera: 5.5.20
-- Wersja PHP: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `sklep`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `galeria`
--

CREATE TABLE IF NOT EXISTS `galeria` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `produkt_id` int(10) unsigned NOT NULL,
  `plik` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `galeria_FKIndex1` (`produkt_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=42 ;

--
-- Zrzut danych tabeli `galeria`
--

INSERT INTO `galeria` (`id`, `produkt_id`, `plik`) VALUES
(19, 100016, '100016_0.jpg'),
(20, 100016, '100016_1.jpg'),
(21, 100017, '100017_0.jpg'),
(22, 100017, '100017_1.jpg'),
(34, 100023, '100023_0.jpg'),
(35, 100023, '100023_1.jpg'),
(36, 100023, '100023_2.jpg'),
(37, 100023, '100023_3.jpg'),
(38, 100024, '100024_0.jpg'),
(39, 100024, '100024_1.jpg'),
(40, 100024, '100024_2.jpg'),
(41, 100024, '100024_3.jpg');

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `kategoria`
--

CREATE TABLE IF NOT EXISTS `kategoria` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kategoria_id` int(10) unsigned DEFAULT NULL,
  `nazwa` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kategoria_FKIndex1` (`kategoria_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=84 ;

--
-- Zrzut danych tabeli `kategoria`
--

INSERT INTO `kategoria` (`id`, `kategoria_id`, `nazwa`) VALUES
(30, NULL, 'Komputery'),
(31, 30, 'Procesory'),
(32, 30, 'Płyty główne'),
(33, 30, 'Pamięć RAM'),
(34, 30, 'Obudowy'),
(35, 30, 'Zasilacze'),
(36, 30, 'Karty Graficzne'),
(37, 30, 'Karty Muzyczne'),
(38, NULL, 'Komptutery przenośne'),
(39, 38, 'Laptopy'),
(40, 38, 'Netbooki'),
(41, 38, 'Tablety'),
(42, 38, 'Akcesoria'),
(43, NULL, 'Oprogramowanie'),
(44, 43, 'Systemy opreacyjne'),
(45, 43, 'Programy biurowe'),
(47, 43, 'Programy antywirusowe'),
(48, 43, 'Gry'),
(52, 32, 'ASUS'),
(53, 32, 'ABIT'),
(54, 32, 'GIGABYTE'),
(69, 31, 'AMD'),
(70, 31, 'INTEL'),
(82, NULL, 'Smoki');

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `komentarze`
--

CREATE TABLE IF NOT EXISTS `komentarze` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `produkt_id` int(10) unsigned NOT NULL,
  `uzytkownicy_id` int(10) unsigned NOT NULL,
  `tytul` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `czas` date NOT NULL,
  `ocena` tinyint(3) unsigned NOT NULL,
  `tresc` text COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `komentarze_FKIndex1` (`uzytkownicy_id`),
  KEY `komentarze_FKIndex2` (`produkt_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `pozycja`
--

CREATE TABLE IF NOT EXISTS `pozycja` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `produkt_id` int(10) unsigned NOT NULL,
  `zamowienie_id` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `ilosc` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pozycja_FKIndex1` (`zamowienie_id`),
  KEY `pozycja_FKIndex2` (`produkt_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=18 ;

--
-- Zrzut danych tabeli `pozycja`
--

INSERT INTO `pozycja` (`id`, `produkt_id`, `zamowienie_id`, `ilosc`) VALUES
(16, 100011, '0000022012060100', 1),
(17, 100018, '0000022012060100', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `produkt`
--

CREATE TABLE IF NOT EXISTS `produkt` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kategoria_id` int(10) unsigned NOT NULL,
  `nazwa` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `opis` text COLLATE utf8_polish_ci NOT NULL,
  `krotki_opis` text COLLATE utf8_polish_ci,
  `cena` float NOT NULL DEFAULT '0',
  `poprzednia_cena` float DEFAULT NULL,
  `promocja` tinyint(1) DEFAULT NULL,
  `kod` varchar(10) COLLATE utf8_polish_ci NOT NULL,
  `ocena` float DEFAULT NULL,
  `dodano` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produkt_FKIndex1` (`kategoria_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=100025 ;

--
-- Zrzut danych tabeli `produkt`
--

INSERT INTO `produkt` (`id`, `kategoria_id`, `nazwa`, `opis`, `krotki_opis`, `cena`, `poprzednia_cena`, `promocja`, `kod`, `ocena`, `dodano`) VALUES
(100016, 69, 'Testowy procesor', 'sadfasfasf\r\n\r\nasfdsaf\r\nsfa\r\nsadf\r\n\r\nsdfasfasf', 'bal blafashfaf\r\n\r\nsaf\r\nsf\r\nsf\r\nsf\r\nf\r\ngdagfdgdag', 999, 0, 0, 'AMDFX5800', 0, '2012-05-30'),
(100017, 70, 'Intel Core i7 920', 'Testowy opis procesora #2', 'Testowy opis procesora #1', 1299.99, 0, 0, 'ICOREI7920', 0, '2012-05-30'),
(100023, 82, 'Zenith Dragon', 'It''s our 7'' tall, inflatable zenith dragon! This is a custom toy project thought up by Helsing (FA: Helsing), one of our customers and they are willing to open up pre-orders to the public. So, that''s what we''re doing!<br/><br/>\r\n\r\nJust check out all the features:<br/><br/>\r\n\r\n* 7'' tall<br/>\r\n* 3 chambers (1 body & legs, 1 per wing)<br/>\r\n* 0.4mm vinyl (same as on all our toys!), except for<br/>\r\n* Thinner (0.2-0.3) vinyl used on spikes, wing membranes<br/>\r\n* Hips will taper into the body for improved rideability<br/>\r\n', '* 7'' tall<br/>\r\n* 3 chambers (1 body & legs, 1 per wing)<br/>\r\n* 0.4mm vinyl (same as on all our toys!), except for<br/>\r\n* Thinner (0.2-0.3) vinyl used on spikes, wing membranes<br/>\r\n* Hips will taper into the body for improved rideability<br/>', 1999, 0, 0, 'PUZENITH', 0, '2012-06-01'),
(100024, 82, 'Alexia Dragon', 'ROAR! It''s a 8'' tall, inflatable standing dragon! This is a custom toy project thought up and designed by Alexia and they are willing to open up pre-orders to the public. So, mind his step, he''s HUGE!<br/><br/>\r\n\r\n* 8'' tall<br/>\r\n* 2 chambers (1 body, 1 hair)<br/>\r\n* 0.4mm vinyl (same as our husky!)<br/>\r\n* Overlapping seams for strength<br/>', '* 8'' tall<br/>\r\n* 2 chambers (1 body, 1 hair)<br/>\r\n* 0.4mm vinyl (same as our husky!)<br/>\r\n* Overlapping seams for strength<br/>', 1299.54, 0, 0, 'PUALEXIA', 0, '2012-06-01');

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `uzytkownicy`
--

CREATE TABLE IF NOT EXISTS `uzytkownicy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wojewodztwo_id` int(10) unsigned NOT NULL,
  `email` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `haslo` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `token` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `kod_aktywacyjny` varchar(32) COLLATE utf8_polish_ci DEFAULT NULL,
  `aktywny` tinyint(1) NOT NULL DEFAULT '0',
  `uprawnienia` tinyint(1) NOT NULL DEFAULT '0',
  `imie` varchar(20) COLLATE utf8_polish_ci NOT NULL,
  `nazwisko` varchar(20) COLLATE utf8_polish_ci NOT NULL,
  `ulica` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `dom` varchar(5) COLLATE utf8_polish_ci NOT NULL,
  `lokal` smallint(4) unsigned DEFAULT NULL,
  `kod` tinyint(2) unsigned NOT NULL,
  `kod2` smallint(3) unsigned NOT NULL,
  `miasto` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uzytkownicy_FKIndex1` (`wojewodztwo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=7 ;

--
-- Zrzut danych tabeli `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`id`, `wojewodztwo_id`, `email`, `haslo`, `token`, `kod_aktywacyjny`, `aktywny`, `uprawnienia`, `imie`, `nazwisko`, `ulica`, `dom`, `lokal`, `kod`, `kod2`, `miasto`) VALUES
(1, 12, 'sklepwsti@gmail.com', 'a8146488ef1c60a4d0464c5fed36a809', '192243e7fbebb02d6df46b6520529854', '1a6cf55e011c0199f3ec21ddccd74077', 1, 1, 'Administrator', 'Suchecki', 'Tysiąclecia', '86', 165, 40, 871, 'Katowice'),
(5, 12, 'haruka_pl@o2.pl', '221bd8bca57d87878fd46b6627f4a2cf', '221bd8bca57d87878fd46b6627f4a2cf', 'e729dde5b4fd293f5300fa0470fd1376', 0, 0, 'Michał', 'Suchecki', 'Tysiąclecia', '86', 165, 40, 871, 'Katowice'),
(6, 12, 'harukapl@gmail.com', '9e58b60c3365f00b5495b16ee3896fc6', '69ca6905955f1bbd092549cd2c37b6d3', '3a121db067df06402e364148d261c3aa', 1, 0, 'Michał', 'Suchecki', 'Tysiąclecia', '86', 165, 40, 871, 'Katowice');

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `wojewodztwo`
--

CREATE TABLE IF NOT EXISTS `wojewodztwo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nazwa` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=17 ;

--
-- Zrzut danych tabeli `wojewodztwo`
--

INSERT INTO `wojewodztwo` (`id`, `nazwa`) VALUES
(1, 'Dolnośląskie'),
(2, 'Kujawsko-pomorskie'),
(3, 'Lubelskie'),
(4, 'Lubuskie'),
(5, 'Łódzkie'),
(6, 'Małopolskie'),
(7, 'Mazowieckie'),
(8, 'Opolskie'),
(9, 'Podlaskie'),
(10, 'Podkarpackie'),
(11, 'Pomorskie'),
(12, 'Śląskie'),
(13, 'Świętokrzyskie'),
(14, 'Warmińsko-mazurskie'),
(15, 'Wielkopolskie'),
(16, 'Zachodnio-pomorskie');

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `zamowienie`
--

CREATE TABLE IF NOT EXISTS `zamowienie` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uzytkownicy_id` int(10) unsigned NOT NULL,
  `zam_nr` varchar(16) COLLATE utf8_polish_ci DEFAULT NULL,
  `zam_data` date NOT NULL,
  `zam_status` smallint(6) NOT NULL,
  `cena` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zamowienie_FKIndex1` (`uzytkownicy_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=10 ;

--
-- Zrzut danych tabeli `zamowienie`
--

INSERT INTO `zamowienie` (`id`, `uzytkownicy_id`, `zam_nr`, `zam_data`, `zam_status`, `cena`) VALUES
(9, 2, '0000022012060100', '2012-06-01', 1, 2898);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
