CREATE DATABASE `tpfinal`;

USE `tpfinal`;

CREATE TABLE `user` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `authToken` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `birth_year` int(11) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `latitude` int(11) NOT NULL,
  `longitude` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_picture` varchar(255) NOT NULL,
  `role` varchar(1)
);

ALTER TABLE `user`
  ADD PRIMARY KEY (`username`);

create table Pregunta(
                         id INT auto_increment,
                         categoria INT NOT NULL,
                         contenido TEXT NOT NULL,
                         primary key(id),
                         estado TINYINT(1)
);

create table Respuesta(
                          id INT auto_increment,
                          idPregunta INT NOT NULL,
                          contenido TEXT NOT NULL,
                          correcta TINYINT(1) not null,
                          primary key(id),
                          foreign key(idPregunta) references Pregunta(id)
);

INSERT INTO Pregunta(categoria,contenido) VALUES (1,'de que color era el caballo blanco de san martin?'),(2,'cuantos balones de oro tiene Messi?'),(3,'quien fue el director de la pelicula "el resplandor"?');
INSERT INTO Respuesta(idPregunta,contenido,correcta) VALUES (1,'marron',0),(1,'blanco',1),(1,'verde',0),(1,'negro',0),(2,'3',0),(2,'6',0),(2,'4',0),(2,'8',1),(3,'Stanley Kubrick',1),(3,'Woody Allen',0),(3,'Christopher Nolan',0),(3,'Ari Aster',0);

INSERT INTO pregunta (categoria, contenido) VALUES (1, '¿Quién fue el primer presidente de los Estados Unidos?'),(2, '¿En qué año ganó España la Copa del Mundo de fútbol?'),(3, '¿Quién protagonizó la película "Titanic"?');
INSERT INTO respuesta (idPregunta, contenido, correcta) VALUES (4, 'George Washington', 1),(4, 'Abraham Lincoln', 0),(4, 'Thomas Jefferson', 0),(6, 'John Adams', 0),(5, '2006', 0),(5, '2010', 1),(5, '2014', 0),(5, '2018', 0),(6, 'Leonardo DiCaprio', 1),(6, 'Brad Pitt', 0),(6, 'Tom Cruise', 0),(6, 'Johnny Depp', 0);

INSERT INTO pregunta (categoria, contenido) VALUES (1, '¿En qué año comenzó la Segunda Guerra Mundial?'),(2, '¿Cuántos jugadores conforman un equipo de fútbol en el campo?'),(3, '¿Cuál es el nombre del parque temático de Disney en París?');
INSERT INTO respuesta (idPregunta, contenido, correcta) VALUES (7, '1939', 1),(7, '1941', 0),(7, '1945', 0),(7, '1936', 0),(8, '9', 0),(8, '10', 0),(8, '11', 1),(8, '12', 0),(9, 'Disneyland París', 0),(9, 'Disney World Europa', 0),(9, 'Parc Disneyland', 0),(9, 'Disneyland París Resort', 1);

INSERT INTO pregunta (categoria, contenido) VALUES (1, '¿En qué año cayó el Muro de Berlín?'),(2, '¿Cuál es el deporte más popular en el mundo?'),(3, '¿Quién escribió la saga de libros "Harry Potter"?');
INSERT INTO respuesta (idPregunta, contenido, correcta) VALUES (10, '1989', 1),(10, '1990', 0),(10, '1987', 0), (10, '1991', 0),(11, 'Fútbol', 1), (11, 'Béisbol', 0),(11, 'Baloncesto', 0),(11, 'Tenis', 0),(12, 'J.K. Rowling', 1),(12, 'J.R.R. Tolkien', 0),(12, 'George R.R. Martin', 0),(12, 'Stephen King', 0);

CREATE TABLE pregunta_respondida (
                                     id INT PRIMARY KEY auto_increment,
                                     id_usuario varchar(255),
                                     id_pregunta INT,
                                     FOREIGN KEY (id_usuario) REFERENCES user(username),
                                     FOREIGN KEY (id_pregunta) REFERENCES pregunta(id)
);