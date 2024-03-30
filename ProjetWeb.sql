/* Création de la bdd */

DROP DATABASE IF EXISTS ProjetWeb;

CREATE DATABASE ProjetWeb;
USE ProjetWeb;


/* Tables */

DROP TABLE IF EXISTS AccountTypes;
CREATE TABLE AccountTypes(
	ID_Type INT NOT NULL AUTO_INCREMENT,
	Name VARCHAR (50) NOT NULL,
	PRIMARY KEY (ID_Type)
);

DROP TABLE IF EXISTS Countries;
CREATE TABLE Countries(
	ID_Country INT NOT NULL AUTO_INCREMENT,
    Name VARCHAR (50) NOT NULL,
	PRIMARY KEY (ID_Country)
);

DROP TABLE IF EXISTS Cities;
CREATE TABLE Cities(
	ID_City INT NOT NULL AUTO_INCREMENT,
    Name VARCHAR (50) NOT NULL,
    ZIP INT NOT NULL,
    ID_Country INT NOT NULL,
	PRIMARY KEY (ID_City),
    FOREIGN KEY (ID_Country) REFERENCES Countries(ID_Country)
);

DROP TABLE IF EXISTS Addresses;
CREATE TABLE Addresses(
	ID_Address INT NOT NULL AUTO_INCREMENT,
    Number VARCHAR (50) NOT NULL,
    Street VARCHAR (50) NOT NULL,
    Creation_Date DATETIME NOT NULL,
    ID_City INT NOT NULL,
	PRIMARY KEY (ID_Address),
    FOREIGN KEY (ID_City) REFERENCES Cities(ID_City)
);

DROP TABLE IF EXISTS Classes;
CREATE TABLE Classes(
	ID_Class INT NOT NULL AUTO_INCREMENT,
	Name VARCHAR (50) NOT NULL,
    ID_Address INT,
	PRIMARY KEY (ID_Class),
    FOREIGN KEY (ID_Address) REFERENCES Addresses(ID_Address)
);

DROP TABLE IF EXISTS Accounts;
CREATE TABLE Accounts(
	ID_Account INT NOT NULL AUTO_INCREMENT,
	Creation_Date DATETIME NOT NULL,
	Username VARCHAR (50) NOT NULL,
	Password VARCHAR (50) NOT NULL,
    ID_Type INT NOT NULL,
    ID_ClassItBelongsTo INT,
	PRIMARY KEY (ID_Account),
    FOREIGN KEY(ID_Type) REFERENCES AccountTypes(ID_Type),
    FOREIGN KEY(ID_ClassItBelongsTo) REFERENCES Classes(ID_Class)
);

DROP TABLE IF EXISTS Users;
CREATE TABLE Users(
	ID_User INT NOT NULL AUTO_INCREMENT,
	Firstname VARCHAR (50) NOT NULL,
	Lastname VARCHAR (50) NOT NULL,
    ID_LinkedAccount INT NOT NULL,
    PRIMARY KEY (ID_User),
    FOREIGN KEY(ID_LinkedAccount) REFERENCES Accounts(ID_Account)
);

DROP TABLE IF EXISTS Sessions;
CREATE TABLE Sessions(
	ID_Session INT NOT NULL AUTO_INCREMENT,
	Token VARCHAR (50) NOT NULL,
	Creation_Date DATETIME NOT NULL,
	Update_Date DATETIME NOT NULL,
    UserAgent VARCHAR (50) NOT NULL,
    Geolocation VARCHAR (50) NOT NULL,
    ID_Account INT NOT NULL,
	PRIMARY KEY (ID_Session),
    FOREIGN KEY(ID_Account) REFERENCES Accounts(ID_Account)
);

DROP TABLE IF EXISTS Companies;
CREATE TABLE Companies(
	ID_Company INT NOT NULL AUTO_INCREMENT,
	Name VARCHAR (50) NOT NULL,
	Creation_Date DATETIME NOT NULL,
	PRIMARY KEY (ID_Company)
);

DROP TABLE IF EXISTS Company_Notes;
CREATE TABLE Company_Notes(
	ID_Note INT NOT NULL AUTO_INCREMENT,
    Note INT NOT NULL,
    ID_Account INT NOT NULL,
    ID_Company INT NOT NULL,
	PRIMARY KEY (ID_Note),
    FOREIGN KEY (ID_Account) REFERENCES Accounts(ID_Account),
    FOREIGN KEY (ID_Company) REFERENCES Companies(ID_Company) 
);

DROP TABLE IF EXISTS Activities;
CREATE TABLE Activities(
	ID_Activity INT NOT NULL AUTO_INCREMENT,
	Name VARCHAR (50) NOT NULL,
	PRIMARY KEY (ID_Activity)
);

DROP TABLE IF EXISTS Locations;
CREATE TABLE Locations(
	ID_CompanyLocation INT NOT NULL AUTO_INCREMENT,
    Creation_Date DATETIME NOT NULL,
    Description VARCHAR (50) NOT NULL,
    IsHeadquarters BOOL NOT NULL,
    ID_Company INT NOT NULL,
	PRIMARY KEY (ID_CompanyLocation),
    FOREIGN KEY (ID_Company) REFERENCES Companies(ID_Company)
);

DROP TABLE IF EXISTS Durations;
CREATE TABLE Durations(
	ID_Duration INT NOT NULL AUTO_INCREMENT,
    Duration INT NOT NULL,
	PRIMARY KEY (ID_Duration)
);

DROP TABLE IF EXISTS Offers;
CREATE TABLE Offers(
	ID_Offer INT NOT NULL AUTO_INCREMENT,
    Pay VARCHAR (50) NOT NULL,
    Start_Date DATE NOT NULL,
    Places INT NOT NULL,
    Description VARCHAR (50) NOT NULL,
    End_Date DATE NOT NULL,
    Creation_Date DATETIME NOT NULL,
    ID_Duration INT NOT NULL,
    ID_CompanyLocation INT NOT NULL,
    ID_Address INT NOT NULL,
	PRIMARY KEY (ID_Offer),
    FOREIGN KEY (ID_Duration) REFERENCES Durations(ID_Duration),
    FOREIGN KEY (ID_CompanyLocation) REFERENCES Locations(ID_CompanyLocation),
    FOREIGN KEY (ID_Address) REFERENCES Addresses(ID_Address) 
);

DROP TABLE IF EXISTS Study_Levels;
CREATE TABLE Study_Levels(
	ID_Study_Level INT NOT NULL AUTO_INCREMENT,
    Name VARCHAR (50) NOT NULL,
	PRIMARY KEY (ID_Study_Level)
);

DROP TABLE IF EXISTS Skills;
CREATE TABLE Skills(
	ID_Skill INT NOT NULL AUTO_INCREMENT,
    Name VARCHAR (50) NOT NULL,
	PRIMARY KEY (ID_Skill)
);

DROP TABLE IF EXISTS MotivationLetters;
CREATE TABLE MotivationLetters(
	ID_MotivationLetter INT NOT NULL AUTO_INCREMENT,
    FileName VARCHAR (50) NOT NULL,
	PRIMARY KEY (ID_MotivationLetter)
);

DROP TABLE IF EXISTS CVs;
CREATE TABLE CVs(
	ID_CV INT NOT NULL AUTO_INCREMENT,
    FileName VARCHAR (50) NOT NULL,
	PRIMARY KEY (ID_CV)
);

DROP TABLE IF EXISTS Applications;
CREATE TABLE Applications(
	ID_Application INT NOT NULL AUTO_INCREMENT,
	Creation_Date DATETIME NOT NULL,
    ID_Offer INT NOT NULL,
    ID_Account INT NOT NULL,
    ID_MotivationLetter INT NOT NULL,
    ID_CV INT NOT NULL,
	PRIMARY KEY (ID_Application),
    FOREIGN KEY (ID_Offer) REFERENCES Offers(ID_Offer),
    FOREIGN KEY (ID_Account) REFERENCES Accounts(ID_Account),
    FOREIGN KEY (ID_MotivationLetter) REFERENCES MotivationLetters(ID_MotivationLetter),
    FOREIGN KEY (ID_CV) REFERENCES CVs(ID_CV)
);


/* Tables de relations */

DROP TABLE IF EXISTS Is_responsible_for;
CREATE TABLE Is_responsible_for(
   ID_Classs INT,
   ID_Account INT,
   PRIMARY KEY(ID_Classs, ID_Account),
   FOREIGN KEY(ID_Classs) REFERENCES Classes(ID_Class),
   FOREIGN KEY(ID_Account) REFERENCES Accounts(ID_Account)
);

DROP TABLE IF EXISTS Operates_in;
CREATE TABLE Operates_in(
   ID_Company INT,
   ID_Activity INT,
   PRIMARY KEY(ID_Company, ID_Activity),
   FOREIGN KEY(ID_Company) REFERENCES Companies(ID_Company),
   FOREIGN KEY(ID_Activity) REFERENCES Activities(ID_Activity)
);

DROP TABLE IF EXISTS Live_in;
CREATE TABLE Live_in(
   ID_City INT,
   ID_Account INT,
   PRIMARY KEY(ID_City, ID_Account),
   FOREIGN KEY(ID_City) REFERENCES Cities(ID_City),
   FOREIGN KEY(ID_Account) REFERENCES Accounts(ID_Account)
);

DROP TABLE IF EXISTS Is_located;
CREATE TABLE Is_located(
   ID_Address INT,
   ID_CompanyLocation INT,
   PRIMARY KEY(ID_Address, ID_CompanyLocation),
   FOREIGN KEY(ID_Address) REFERENCES Addresses(ID_Address),
   FOREIGN KEY(ID_CompanyLocation) REFERENCES Locations(ID_CompanyLocation)
);

DROP TABLE IF EXISTS Has;
CREATE TABLE Has(
   ID_Skill INT,
   ID_Account INT,
   PRIMARY KEY(ID_Skill, ID_Account),
   FOREIGN KEY(ID_Skill) REFERENCES Skills(ID_Skill),
   FOREIGN KEY(ID_Account) REFERENCES Accounts(ID_Account)
);

DROP TABLE IF EXISTS Requires;
CREATE TABLE Requires(
   ID_Offer INT,
   ID_Skill INT,
   PRIMARY KEY(ID_Offer, ID_Skill),
   FOREIGN KEY(ID_Offer) REFERENCES Offers(ID_Offer),
   FOREIGN KEY(ID_Skill) REFERENCES Skills(ID_Skill)
);

DROP TABLE IF EXISTS Is_dedicated_to;
CREATE TABLE Is_dedicated_to(
   ID_Offer INT,
   ID_Study_Level INT,
   PRIMARY KEY(ID_Offer, ID_Study_Level),
   FOREIGN KEY(ID_Offer) REFERENCES Offers(ID_Offer),
   FOREIGN KEY(ID_Study_Level) REFERENCES Study_Levels(ID_Study_Level)
);

DROP TABLE IF EXISTS Is_of_interest_to;
CREATE TABLE Is_of_interest_to(
   ID_Offer INT,
   ID_Account INT,
   PRIMARY KEY(ID_Offer, ID_Account),
   FOREIGN KEY(ID_Offer) REFERENCES Offers(ID_Offer),
   FOREIGN KEY(ID_Account) REFERENCES Accounts(ID_Account)
);


/* Création d'utilisateur */

DROP USER IF EXISTS 'Administrateur'@'localhost';
CREATE USER 'Administrateur'@'localhost' IDENTIFIED BY 'AdminPassword' ;
GRANT ALL ON ProjetWeb.* TO 'Administrateur'@'localhost' WITH GRANT OPTION ;

FLUSH PRIVILEGES;

