CREATE DATABASE IF NOT EXISTS `mchariots`;
USE `mchariots`;

CREATE TABLE IF NOT EXISTS `users` (
  `username` VARCHAR (64) PRIMARY KEY,
  `hash`     VARCHAR (255) NOT NULL,
  `email`    VARCHAR (64) UNIQUE,
  `fullname` VARCHAR (128),
  `dob`      DATE NOT NULL,
  `gender`   ENUM('male', 'female')
);
