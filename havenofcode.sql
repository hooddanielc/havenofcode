SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `article` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `github_id` int(32) NOT NULL,
  `title` varchar(52) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(170) COLLATE utf8_unicode_ci NOT NULL,
  `md` text COLLATE utf8_unicode_ci NOT NULL,
  `youtube` varchar(1000) COLLATE utf8_unicode_ci NOT NULL COMMENT 'link to youtube',
  `published` tinyint(1) NOT NULL COMMENT 'published',
  `publish_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=53 ;

CREATE TABLE IF NOT EXISTS `article_comment` (
  `comment_id` int(32) NOT NULL AUTO_INCREMENT,
  `comment_foreign` int(32) DEFAULT NULL,
  `comment_text` varchar(2083) COLLATE utf8_unicode_ci NOT NULL,
  `article_id` int(32) NOT NULL COMMENT 'comment on article',
  `comment_deleted` tinyint(4) NOT NULL,
  `github_id` int(32) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=38 ;

CREATE TABLE IF NOT EXISTS `users` (
  `github_id` int(32) NOT NULL DEFAULT '0' COMMENT 'github user id',
  `user_cache` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`github_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
