SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `article` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `github_id` int(32) NOT NULL,
  `title` varchar(52) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(170) COLLATE utf8_unicode_ci NOT NULL,
  `md` text COLLATE utf8_unicode_ci NOT NULL,
  `youtube` varchar(1000) COLLATE utf8_unicode_ci NOT NULL COMMENT 'link to youtube',
  `repo` varchar(256) COLLATE utf8_unicode_ci NOT NULL COMMENT 'github repo id',
  `published` tinyint(1) NOT NULL COMMENT 'published',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

INSERT INTO `article` (`id`, `github_id`, `title`, `description`, `md`, `youtube`, `repo`, `published`) VALUES
(1, 1351734, 'Article Title', 'Article Description', 'not very much md here...', '', '', 1);

CREATE TABLE IF NOT EXISTS `article_comment` (
  `comment_id` int(32) NOT NULL AUTO_INCREMENT,
  `comment_foreign` int(32) DEFAULT NULL,
  `comment_text` varchar(2083) COLLATE utf8_unicode_ci NOT NULL,
  `article_id` int(32) NOT NULL COMMENT 'comment on article',
  `comment_deleted` tinyint(4) NOT NULL,
  `github_id` int(32) NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

INSERT INTO `article_comment` (`comment_id`, `comment_foreign`, `comment_text`, `article_id`, `comment_deleted`, `github_id`) VALUES
(1, NULL, 'Nice article', 1, 0, 1351734),
(2, 1, 'I second that.', 1, 0, 1351734),
(3, NULL, 'Very abusive comment', 1, 1, 1351734);

CREATE TABLE IF NOT EXISTS `users` (
  `github_id` int(32) NOT NULL DEFAULT '0' COMMENT 'github user id',
  PRIMARY KEY (`github_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users` (`github_id`) VALUES
(1351734);
