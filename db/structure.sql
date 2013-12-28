CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `hash_pass` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

INSERT INTO user (name, hash_pass) VALUES ('admin', SHA1('admin:12345'));

CREATE TABLE IF NOT EXISTS `server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `url` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
