-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Окт 02 2012 г., 18:05
-- Версия сервера: 5.5.24-log
-- Версия PHP: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `hilife`
--

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Дата регистрации',
  `username` varchar(255) NOT NULL DEFAULT '' COMMENT 'Имя пользователья',
  `pass` varchar(32) NOT NULL DEFAULT '' COMMENT 'Хешированный пароль',
  `type` enum('postav','client','admin') NOT NULL DEFAULT 'client' COMMENT 'Класс пользователя',
  `email` varchar(200) NOT NULL COMMENT 'Емайл',
  `phone` varchar(12) NOT NULL COMMENT 'Номер телефона',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Таблица всех зарегистрированных' AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `added`, `username`, `pass`, `type`, `email`, `phone`) VALUES
(2, '2012-09-04 00:00:00', 'МАРАТ!', '123456', 'client', 'shaihetdinov2009@yandex.ru', '0'),
(3, '2012-09-20 00:00:00', 'dsdadas', '123', 'client', '', '0'),
(4, '2012-10-02 15:26:36', 'Марат Шайхетдинов', 'f93eca346455f5eb99e423272a8212f9', 'client', 'marat-shahutdino@mail.ru', '79274858601');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
