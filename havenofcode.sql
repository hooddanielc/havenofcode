SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE TABLE IF NOT EXISTS `article` (
  `article_id` int(32) NOT NULL AUTO_INCREMENT,
  `github_id` int(32) NOT NULL,
  `article_title` varchar(52) COLLATE utf8_unicode_ci NOT NULL,
  `article_description` varchar(170) COLLATE utf8_unicode_ci NOT NULL,
  `article_md` text COLLATE utf8_unicode_ci NOT NULL,
  `article_youtube` varchar(1000) COLLATE utf8_unicode_ci NOT NULL COMMENT 'link to youtube',
  `article_repo` varchar(256) COLLATE utf8_unicode_ci NOT NULL COMMENT 'github repo id',
  PRIMARY KEY (`article_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `article` (`article_id`, `github_id`, `article_title`, `article_description`, `article_md`, `article_youtube`, `article_repo`) VALUES
(1, 1351734, 'Welcome to Haven of Code', 'This is the first article ever written for haven of code! By Daniel Hood', 'Haven of Code\r\n=============\r\n\r\nI hope you enjoy this!', '', '');

CREATE TABLE IF NOT EXISTS `article_comment` (
  `comment_id` int(32) NOT NULL,
  `comment_foreign` int(32) DEFAULT NULL,
  `comment_text` varchar(2083) COLLATE utf8_unicode_ci NOT NULL,
  `article_id` int(32) NOT NULL COMMENT 'comment on article',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `article_comment` (`comment_id`, `comment_foreign`, `comment_text`, `article_id`) VALUES
(1, NULL, 'This is AWESOME ^.^', 1);

CREATE TABLE IF NOT EXISTS `users` (
  `github_id` int(32) NOT NULL DEFAULT '0' COMMENT 'github user id',
  PRIMARY KEY (`github_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users` (`github_id`) VALUES
(1351734);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;