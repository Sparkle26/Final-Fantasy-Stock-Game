CREATE DATABASE IF NOT EXISTS stocksDB;
use stocksDB;

CREATE TABLE IF NOT EXISTS user (
    userID int AUTO_INCREMENT,
    username VARCHAR(45),
    user_password VARCHAR(12),
    wins INT,
    losses INT,
    leagueID INT,
    stocks VARCHAR(10),

    PRIMARY KEY (userID),
    CONSTRAINT fk_users_to_league FOREIGN KEY (leagueID) REFERENCES league(leagueID),
    CONSTRAINT fk_users_to_holdings FOREIGN KEY (stocks) REFERENCES holdings(ticker)
);

CREATE TABLE IF NOT EXISTS league (
    leagueID int AUTO_INCREMENT,
    user_in_league VARCHAR(45),
    league_name VARCHAR(45),
    duration INT
    
    PRIMARY KEY (leagueID)
);

CREATE TABLE IF NOT EXISTS holdings (
    ticker VARCHAR(10),
    st_name VARCHAR(45),
    start_price DECIMAL(10),
    end_price DECIMAL(10),
    indicies VARCHAR(45),
    index INT,

    PRIMARY KEY (ticket),
    CONSTRAINT fk_holdings_to_sector FOREIGN KEY (index) REFERENCES sector(index)
);

CREATE TABLE IF NOT EXISTS matchups (
    Week_num INT,
    leagueID INT,
    user_1_id INT,
    user_2_id INT,
    winner VARCHAR(45),
    loser VARCHAR(45),

    PRIMARY KEY (Week_num),
    CONSTRAINT fk_match_to_league FOREIGN KEY (leagueID) REFERENCES league(leagueID),
    CONSTRAINT fk_match_to_users FOREIGN KEY (user_1_id) REFERENCES users(userID),
    CONSTRAINT fk_match_to_users FOREIGN KEY (user_2_id) REFERENCES users(userID)
);

CREATE TABLE IF NOT EXISTS sector (
    index INT AUTO_INCREMENT,
    sector_name VARCHAR(45),

    PRIMARY KEY (index)
);