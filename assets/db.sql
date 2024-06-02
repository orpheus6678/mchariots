CREATE DATABASE `mchariots`;
USE `mchariots`;

CREATE TABLE `users` (
  `username` VARCHAR (64)  PRIMARY KEY,
  `hash`     VARCHAR (256) NOT NULL,
  `email`    VARCHAR (64)  UNIQUE,
  `fullname` VARCHAR (128),
  `dob`      DATE NOT NULL,
  `gender`   ENUM ('male', 'female'),
  `joindate` DATE NOT NULL,
  `pfp`      BIGINT
);

CREATE TABLE `images` (
  `id`   BIGINT UNSIGNED PRIMARY KEY DEFAULT UUID_SHORT(), 
  `path` VARCHAR (256) NOT NULL
)

ALTER TABLE `users` ADD FOREIGN KEY (`pfp`) REFERENCES `images` (`id`);

CREATE TABLE `posts` (
  `id`       BIGINT UNSIGNED PRIMARY KEY DEFAULT UUID_SHORT(),
  `text`     TEXT,
  `created`  DATETIME NOT NULL,
  `username` VARCHAR (64) NOT NULL,
  `car`      BIGINT,

  FOREIGN KEY (`username`) REFERENCES `users` (`username`)
);

CREATE TABLE `posts_images` (
  `postid` BIGINT UNSIGNED NOT NULL,
  `imgid`  BIGINT UNSIGNED NOT NULL,

  FOREIGN KEY (`postid`) REFERENCES `posts` (`id`),
  FOREIGN KEY (`imgid`)  REFERENCES `images` (`id`)
);
