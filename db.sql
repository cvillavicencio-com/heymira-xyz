-- -- quema todo...
SET FOREIGN_KEY_CHECKS = 0;
drop table if exists Comments;
drop view if exists Usertopics;
drop table if exists Tagslinks;
drop view if exists  Linksinfo;
drop table if exists Links;
drop table if exists States;
drop table if exists Topics;
drop table if exists Subcategories;
drop table if exists Categories;
drop table if exists Catsets;
drop view if exists Userinfo;
drop table if exists Users;
drop table if exists Usertypes;
SET FOREIGN_KEY_CHECKS = 1;
-- -- ...y comienza de nuevo.


CREATE TABLE Usertypes(
       id       INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
       nombre   VARCHAR(15) NOT NULL      
);

INSERT INTO Usertypes (nombre) VALUES ('colaborador');
INSERT INTO Usertypes (nombre) VALUES ('moderador');
INSERT INTO Usertypes (nombre) VALUES ('administrador');

CREATE TABLE Users(
       id       INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
       nombre   VARCHAR(100) NOT NULL,
       clave    VARCHAR(200) NOT NULL,
       token	VARCHAR(200),
       info     VARCHAR(1000),
       mail     VARCHAR(300),
       tema 	INT DEFAULT 1,
       setfav	INT DEFAULT 1,
       utypeId  INT NOT NULL DEFAULT 1,
       FOREIGN KEY (utypeId) REFERENCES Usertypes(id)
);



CREATE VIEW Userinfo AS
SELECT Users.id, Users.nombre, Users.info, Users.mail, Usertypes.nombre AS 'tipo'
FROM Users RIGHT JOIN Usertypes ON Users.utypeId = Usertypes.id
;

CREATE TABLE Catsets(
       id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
       nombre VARCHAR(50)
);

CREATE TABLE Categories(
       id       INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
       nombre   VARCHAR(50) NOT NULL,
       catsetId INT NOT NULL,
       FOREIGN KEY (catsetId) REFERENCES Catsets(id)
);


CREATE TABLE Subcategories(
       id       INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
       nombre   VARCHAR(50) NOT NULL,
       categId  INT NOT NULL,
       FOREIGN KEY (categId) REFERENCES Categories(id),
       orden    INT
);

Create TABLE Topics(
       id        INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
       nombre    VARCHAR(100) NOT NULL,
       subcatId  INT NOT NULL,
       FOREIGN  KEY (subcatId) REFERENCES Subcategories(id),
       orden 	 INT
);

CREATE TABLE States(
       id	 INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
       nombre	 VARCHAR(12) NOT NULL
);


CREATE TABLE Links (
       id       INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
       titulo	VARCHAR(300) NOT NULL,
       info     VARCHAR(1500) NOT NULL,
       url      VARCHAR(200) NOT NULL,
       urlextra VARCHAR(200),
       creado   TIMESTAMP NOT NULL,
       autorId  INT NOT NULL,
       FOREIGN KEY (autorId) REFERENCES Users(id),
       topicId  INT NOT NULL,
       FOREIGN KEY (topicId) REFERENCES Topics(id),
       catsetId INT NOT NULL,
       FOREIGN KEY (catsetId) REFERENCES Catsets(id),
       stateId  INT NOT NULL DEFAULT 1,
       FOREIGN KEY (stateId) REFERENCES States(id)
);

CREATE TABLE Tagslinks(
       tag VARCHAR(100) NOT NULL,
       linkId INT NOT NULL,
       FOREIGN KEY (linkId) REFERENCES Links(id)
);

CREATE VIEW Linksinfo AS
SELECT Links.id AS 'id',
       Links.titulo,
       Links.info,
       Links.url,
       Links.urlextra,
       Links.creado,
       Links.stateId as 'stateid',
       Users.id AS 'usrid', Users.nombre AS 'user',
       Topics.id AS 'topicid', Topics.nombre AS 'topic',
       Subcategories.id AS 'subcatid', Subcategories.nombre AS 'subcat',
       Categories.id AS 'catid', Categories.nombre As 'cat',
       Links.catsetId AS 'catset'
FROM Links
       INNER JOIN Users ON Links.autorId = Users.id
       INNER JOIN Topics ON Links.topicId = Topics.id
       INNER JOIN Subcategories ON Topics.subcatId = Subcategories.id
       INNER JOIN Categories ON Subcategories.categId = Categories.id
;

CREATE VIEW Usertopics AS
SELECT DISTINCT Topics.id AS 'topicid',
       		Topics.nombre AS 'topic',
       		Users.id AS 'userid',
		(SELECT DISTINCT
			COUNT(*)
		FROM Links
		WHERE Links.autorId=Users.id
		AND Links.topicId=Topics.id) AS 'total'
FROM Links
     INNER JOIN Topics ON Topics.id = Links.topicId
     INNER JOIN Users ON Links.autorId = Users.id
;


CREATE TABLE Comments(
       id       INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
       texto	VARCHAR(250) NOT NULL,
       estado	INT NOT NULL DEFAULT 1,
       FOREIGN KEY (estado) REFERENCES States(id),
       autorId	INT NOT NULL,
       FOREIGN KEY (autorId) REFERENCES Users(id),
       linkId   INT NOT NULL,
       FOREIGN KEY (linkId) REFERENCES Links(id),
       fecha    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- poblation

INSERT INTO Usertypes (nombre) VALUES ('colaborador');
INSERT INTO Usertypes (nombre) VALUES ('moderador');
INSERT INTO Usertypes (nombre) VALUES ('administrador');

INSERT INTO States (nombre) VALUES ('visible');
INSERT INTO States (nombre) VALUES ('eliminado');
INSERT INTO States (nombre) VALUES ('borrador');
