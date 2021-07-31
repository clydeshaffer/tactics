CREATE TABLE Players (
    PlayerID int NOT NULL AUTO_INCREMENT,
    DisplayName varchar(255) NULL,
    LoginName varchar(255) NOT NULL,
    PassHash varchar(255) NOT NULL,
    PRIMARY KEY (PlayerID),
    UNIQUE KEY unique_login (LoginName)
);

CREATE TABLE GameSessions (
    GameSessionID int NOT NULL AUTO_INCREMENT,
    Rounds int NOT NULL DEFAULT 0,
    ActionHours int NOT NULL DEFAULT 24,
    Winner int NULL,
    StartDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FinishDate TIMESTAMP NULL,
    PRIMARY KEY (GameSessionID),
    FOREIGN KEY (Winner) REFERENCES Players(PlayerID)
);

CREATE TABLE Tank (
    TankID int NOT NULL AUTO_INCREMENT,
    Player int NOT NULL,
    Game int NOT NULL,
    X int NOT NULL,
    Y int NOT NULL,
    HP int NOT NULL DEFAULT 3,
    ExtraAP int NOT NULL DEFAULT 0,
    SpentAP int NOT NULL DEFAULT 0,
    PRIMARY KEY (TankID),
    FOREIGN KEY (Player) REFERENCES Players(PlayerID),
    FOREIGN KEY (Game) REFERENCES GameSessions(GameSessionID),
    UNIQUE KEY unique_position(X, Y, Game),
    UNIQUE KEY unique_tank_per_game(Player, Game)
);

CREATE TABLE Message (
    MessageID int NOT NULL AUTO_INCREMENT,
    Sender int NULL,
    Receiver int NULL,
    Game int NULL,
    MessageType int NOT NULL DEFAULT 0,
    Content varchar(255) NOT NULL,
    SendDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (MessageID),
    FOREIGN KEY (Sender) REFERENCES Players(PlayerID),
    FOREIGN KEY (Receiver) REFERENCES Players(PlayerID),
    FOREIGN KEY (Game) REFERENCES GameSessions(GameSessionID)
);