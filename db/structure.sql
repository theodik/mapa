CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `hash_pass` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

--INSERT INTO users (name, hash_pass) VALUES ('test', SHA1(CONCAT('test', '12345')));


