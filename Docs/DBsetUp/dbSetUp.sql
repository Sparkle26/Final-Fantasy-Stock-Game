CREATE DATABASE IF NOT EXISTS stocksDB;
use stocksDB;

CREATE TABLE IF NOT EXISTS user (
    userID int AUTO_INCREMENT,
    username VARCHAR(45),
    password VARCHAR(12),
    wins INT,
    losses INT,
    leagueID INT,
    stocks VARCHAR(10),

    PRIMARY KEY (userID),
    CONSTRAINT fk_users_to_league FOREIGN KEY (leagueID) REFERNCES league(leagueID)
);

CREATE TABLE IF NOT EXISTS league ();

CREATE TABLE IF NOT EXISTS holdings ();

CREATE TABLE IF NOT EXISTS matchups ();

CREATE TABLE IF NOT EXISTS sector ();