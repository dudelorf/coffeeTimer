CREATE DATABASE coffeerecipes;

USE coffeerecipes;

CREATE TABLE savedrecipes
(
	methodname CHAR(30),
	defaultvolume TINYINT(2),
	brewratio FLOAT(3,1),
	grindsize CHAR(25),
	phasememos VARCHAR(200),
	phaseratios VARCHAR(200),
	phasetimes VARCHAR(200),
	dilutionratio FLOAT(3,1)
);