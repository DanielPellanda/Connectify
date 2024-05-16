-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema WebProj
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `WebProj` DEFAULT CHARACTER SET utf8 ;
USE `WebProj` ;

-- -----------------------------------------------------
-- Table `WebProj`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebProj`.`user` (
  `userid` INT NOT NULL AUTO_INCREMENT,
  `password` VARCHAR(512) NOT NULL,
  `username` VARCHAR(45) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `firstname` TINYTEXT NOT NULL,
  `surname` TINYTEXT NOT NULL,
  `biography` TINYTEXT DEFAULT "",
  `profilePicture` VARCHAR(100) DEFAULT "",
  PRIMARY KEY (`userid`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `WebProj`.`follow`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebProj`.`follow` (
  `followerid` INT NOT NULL,
  `followedid` INT NOT NULL,
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`followerid`, `followedid`),
  FOREIGN KEY (`followerid`) REFERENCES `WebProj`.`user` (`userid`) ON DELETE CASCADE,
  FOREIGN KEY (`followedid`) REFERENCES `WebProj`.`user` (`userid`) ON DELETE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `WebProj`.`post`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebProj`.`post` (
  `postid` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL,
  `text` MEDIUMTEXT NOT NULL,
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `postpreview` TINYTEXT NOT NULL,
  `postimg` VARCHAR(100) NOT NULL,
  `user` INT NOT NULL,
  `is_public` INT DEFAULT 0,
  PRIMARY KEY (`postid`),
  INDEX `fk_post_user_idx` (`user` ASC),
  CONSTRAINT `fk_post_user`
    FOREIGN KEY (`user`)
    REFERENCES `WebProj`.`user` (`userid`)
    ON DELETE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `WebProj`.`like`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebProj`.`like` (
  `userid` INT NOT NULL,
  `postid` INT NOT NULL,
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userid`, `postid`),
  FOREIGN KEY (`userid`) REFERENCES `WebProj`.`user` (`userid`) ON DELETE CASCADE,
  FOREIGN KEY (`postid`) REFERENCES `WebProj`.`post` (`postid`) ON DELETE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `WebProj`.`comment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebProj`.`comment` (
  `commentid` INT NOT NULL AUTO_INCREMENT,
  `userid` INT NOT NULL,
  `postid` INT NOT NULL,
  `text` TINYTEXT NOT NULL,
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`commentid`),
  FOREIGN KEY (`userid`) REFERENCES `WebProj`.`user` (`userid`) ON DELETE CASCADE,
  FOREIGN KEY (`postid`) REFERENCES `WebProj`.`post` (`postid`) ON DELETE CASCADE ) 
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `WebProj`.`notification`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebProj`.`notification` (
  `notificationid` INT NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(20) NOT NULL CHECK (`type` IN ('Like', 'Follow', 'Comment', 'Post')), -- Notification Type: 'Like', 'Follow', 'Comment', 'Post'
  `receiverid` INT NOT NULL,
  `senderid` INT NOT NULL,
  `postid` INT,
  `commentid` INT,
  `visualized` INT DEFAULT FALSE,
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(`notificationid`),
  FOREIGN KEY (`receiverid`) REFERENCES `WebProj`.`user` (`userid`) ON DELETE CASCADE,
  FOREIGN KEY (`senderid`) REFERENCES `WebProj`.`user` (`userid`) ON DELETE CASCADE,
  FOREIGN KEY (`postid`) REFERENCES `WebProj`.`post` (`postid`) ON DELETE CASCADE,
  FOREIGN KEY (`commentid`) REFERENCES `WebProj`.`comment` (`commentid`) ON DELETE CASCADE)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
