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
  `car`      VARCHAR (32),

  FOREIGN KEY (`username`) REFERENCES `users` (`username`)
);

CREATE TABLE `posts_images` (
  `postid` BIGINT UNSIGNED NOT NULL,
  `imgid`  BIGINT UNSIGNED NOT NULL,

  FOREIGN KEY (`postid`) REFERENCES `posts` (`id`),
  FOREIGN KEY (`imgid`)  REFERENCES `images` (`id`)
);

CREATE TABLE `likes` (
  `username` VARCHAR NOT NULL,
  `postid`   BIGINT UNSIGNED NOT NULL,

  PRIMARY KEY (`username`, `postid`),
  FOREIGN KEY (`username`) REFERENCES `users` (`username`),
  FOREIGN KEY (`postid`)   REFERENCES `posts` (`id`)
);

CREATE TABLE `comments` (
  `id`       BIGINT UNSIGNED PRIMARY KEY DEFAULT UUID_SHORT(),
  `created`  DATETIME NOT NULL,
  `postid`   BIGINT UNSIGNED NOT NULL,
  `username` VARCHAR (64) NOT NULL,
  `text`     TEXT NOT NULL,

  FOREIGN KEY (`postid`)   REFERENCES `posts` (`id`),
  FOREIGN KEY (`username`) REFERENCES `users` (`username`)
);

CREATE TABLE `manufacturers` (
  `name`   VARCHAR (32) PRIMARY KEY,
  `origin` VARCHAR (32)
);

CREATE TABLE `cars` (
  `name`         VARCHAR (64) PRIMARY KEY,
  `model_year`   INT,
  `engine_size`  FLOAT,
  `engine_type`  VARCHAR (32),
  `drive_train`  VARCHAR (32),
  `gear_box`     VARCHAR (32),
  `type`         VARCHAR (32),
  `manufacturer` VARCHAR (32),

  FOREIGN KEY (`manufacturer`) REFERENCES `manufacturers` (`name`)
);

ALTER TABLE `posts`
ADD FOREIGN KEY (`car`) REFERENCES `cars` (`name`);
