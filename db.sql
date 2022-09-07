-- -- quema todo...
SET FOREIGN_KEY_CHECKS = 0;
drop table if exists Comments;
drop table if exists Taglinks;
drop view if exists  Linksinfo;
drop table if exists Links;
drop table if exists States;
drop table if exists Topics;
drop table if exists Subcategories;
drop table if exists Categories;
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
       info     VARCHAR(300),
       mail     VARCHAR(300),
       utypeId  INT NOT NULL DEFAULT 1,
       FOREIGN KEY (utypeId) REFERENCES Usertypes(id)
);

INSERT INTO Users (nombre,clave,info,mail) VALUES ('cam','426164810d40cdfb319fd4606f477190ebbd36d5','Lorem ipsum dolor sit amet, consectetuer adipiscing elit.  Donec hendrerit tempor tellus.  Donec pretium posuere tellus.  Proin quam nisl, tincidunt et, mattis eget, convallis nec, purus.  Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.  Nulla posuere.','ned@bob.ss');
INSERT INTO Users (nombre,clave,info,mail) VALUES ('ned','426164810d40cdfb319fd4606f477190ebbd36d5','Nullam eu ante vel est convallis dignissim.  Fusce suscipit, wisi nec facilisis facilisis, est dui fermentum leo, quis tempor ligula erat quis odio.  Nunc porta vulputate tellus.  Nunc rutrum turpis sed pede.  Sed bibendum.  Aliquam posuere.  Nunc aliquet, augue nec adipiscing interdum.','ned@bob.ss');


CREATE VIEW Userinfo AS
SELECT Users.id, Users.nombre, Users.info, Users.mail, Usertypes.nombre AS 'tipo'
FROM Users RIGHT JOIN Usertypes ON Users.utypeId = Usertypes.id
;

CREATE TABLE Categories(
       id       INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
       nombre   VARCHAR(50) NOT NULL,
       orden 	INT
);

-- INSERT INTO Categories (nombre) VALUES ('Animales');
-- INSERT INTO Categories (nombre) VALUES ('Materia');

CREATE TABLE Subcategories(
       id       INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
       nombre   VARCHAR(50) NOT NULL,
       categId  INT NOT NULL,
       FOREIGN KEY (categId) REFERENCES Categories(id),
       orden    INT
);
-- INSERT INTO Subcategories (nombre,categId) VALUES ('Mamiferos',1);
-- INSERT INTO Subcategories (nombre,categId) VALUES ('Ovíparos',1);
-- INSERT INTO Subcategories (nombre,categId) VALUES ('Solido',2);
-- INSERT INTO Subcategories (nombre,categId) VALUES ('Gaseoso',2);
-- INSERT INTO Subcategories (nombre,categId) VALUES ('Liquido',2);

Create TABLE Topics(
       id        INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
       nombre    VARCHAR(100) NOT NULL,
       subcatId  INT NOT NULL,
       FOREIGN  KEY (subcatId) REFERENCES Subcategories(id),
       orden 	 INT
);
-- INSERT INTO Topics (nombre,subcatId) VALUES ('Perros',1);
-- INSERT INTO Topics (nombre,subcatId) VALUES ('Gatos',1);
-- INSERT INTO Topics (nombre,subcatId) VALUES ('Aves',2);
-- INSERT INTO Topics (nombre,subcatId) VALUES ('Peces',2);

-- INSERT INTO Topics (nombre,subcatId) VALUES ('Rocas',3);
-- INSERT INTO Topics (nombre,subcatId) VALUES ('Madera',3);
-- INSERT INTO Topics (nombre,subcatId) VALUES ('Plastico',3);
-- INSERT INTO Topics (nombre,subcatId) VALUES ('Helio',4);
-- INSERT INTO Topics (nombre,subcatId) VALUES ('Agua',5);
-- INSERT INTO Topics (nombre,subcatId) VALUES ('Café',5);

CREATE TABLE States(
       id	 INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
       nombre	 VARCHAR(12) NOT NULL
);

INSERT INTO States (nombre) VALUES ('visible');
INSERT INTO States (nombre) VALUES ('eliminado');
INSERT INTO States (nombre) VALUES ('borrador');

CREATE TABLE Links (
       id       INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
       info     VARCHAR(500) NOT NULL,
       url      VARCHAR(200) NOT NULL,
       urlextra VARCHAR(200),
       creado   TIMESTAMP NOT NULL,
       autorId  INT NOT NULL,
       FOREIGN KEY (autorId) REFERENCES Users(id),     
       topicId  INT NOT NULL,
       FOREIGN KEY (topicId) REFERENCES Topics(id),
       stateId  INT NOT NULL DEFAULT 1,
       FOREIGN KEY (stateId) REFERENCES States(id)
);

CREATE TABLE Tagslinks(
       tag VARCHAR(100) NOT NULL,
       linkId INT NOT NULL,
       FOREIGN KEY (linkId) REFERENCES Links(id)
);

CREATE VIEW Linksinfo AS
SELECT Links.id, Links.info, Links.url, Links.urlextra, Links.creado,
       Users.id AS 'usrid', Users.nombre AS 'user',
       Topics.id AS 'topicid', Topics.nombre AS 'topic',
       Subcategories.id AS 'subcatid', Subcategories.nombre AS 'subcat',
       Categories.id AS 'catid', Categories.nombre As 'cat'
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
       estado	BOOLEAN DEFAULT TRUE,
       autorId	INT NOT NULL,
       FOREIGN KEY (autorId) REFERENCES Users(id),
       linkId   INT NOT NULL,
       FOREIGN KEY (linkId) REFERENCES Links(id)
);
