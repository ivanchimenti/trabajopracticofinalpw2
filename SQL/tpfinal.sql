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
                        `role` varchar(1),
                        `cantEntregada` int(11) NOT NULL,
                        `cantRespondida` int(11) NOT NULL,
                        `fecha_ingreso` datetime NOT NULL DEFAULT NOW(),
);

ALTER TABLE `user`
    ADD PRIMARY KEY (`username`);

create table Pregunta(
                         id INT auto_increment,
                         categoria INT NOT NULL,
                         contenido TEXT NOT NULL,
                         cantEntregada INT NOT NULL,
                         cantRespondida INT NOT NULL,
                         porcentajeAcertado INT NOT NULL,
                         estado TINYINT(1) NOT NULL,
                         primary key(id)
);

create table Respuesta(
                          id INT auto_increment,
                          idPregunta INT NOT NULL,
                          contenido TEXT NOT NULL,
                          correcta TINYINT(1) not null,
                          primary key(id),
                          foreign key(idPregunta) references Pregunta(id)
);

INSERT INTO `pregunta` (`id`, `categoria`, `contenido`, `cantEntregada`, `cantRespondida`, `porcentajeAcertado`, `estado`) VALUES
(1, 6, 'de que color era el caballo blanco de san martin?', 50, 50, 60, 1),
(2, 3, 'cuantos balones de oro tiene Messi?', 50, 50, 60, 1),
(3, 4, 'quien fue el director de la pelicula \"el resplandor\"?', 50, 50, 60, 1),
(4, 6, '¿Quién fue el primer presidente de los Estados Unidos?', 50, 50, 60, 1),
(5, 3, '¿En qué año ganó España la Copa del Mundo de fútbol?', 50, 50, 60, 1),
(6, 4, '¿Quién protagonizó la película \"Titanic\"?', 50, 50, 60, 1),
(7, 1, '¿En qué año comenzó la Segunda Guerra Mundial?', 50, 50, 60, 1),
(8, 3, '¿Cuántos jugadores conforman un equipo de fútbol en el campo?', 50, 50, 60, 1),
(9, 4, '¿Cuál es el nombre del parque temático de Disney en París?', 50, 50, 60, 1),
(10, 6, '¿En qué año cayó el Muro de Berlín?', 50, 50, 60, 1),
(11, 3, '¿Cuál es el deporte más popular en el mundo?', 50, 50, 60, 1),
(12, 1, '¿Quién escribió la saga de libros \"Harry Potter\"?', 50, 50, 60, 1),
(13, 2, '¿A que temperatura hierve el agua?', 50, 50, 60, 1),
(14, 5, 'Por área, ¿Cuál es el país más pequeño del planeta?', 50, 50, 60, 1);

INSERT INTO `respuesta` (`id`, `idPregunta`, `contenido`, `correcta`) VALUES
(1, 1, 'marron', 0),
(2, 1, 'blanco', 1),
(3, 1, 'verde', 0),
(4, 1, 'negro', 0),
(5, 2, '3', 0),
(6, 2, '6', 0),
(7, 2, '4', 0),
(8, 2, '8', 1),
(9, 3, 'Stanley Kubrick', 1),
(10, 3, 'Woody Allen', 0),
(11, 3, 'Christopher Nolan', 0),
(12, 3, 'Ari Aster', 0),
(13, 4, 'George Washington', 1),
(14, 4, 'Abraham Lincoln', 0),
(15, 4, 'Thomas Jefferson', 0),
(16, 6, 'John Adams', 0),
(17, 5, '2006', 0),
(18, 5, '2010', 1),
(19, 5, '2014', 0),
(20, 5, '2018', 0),
(21, 6, 'Leonardo DiCaprio', 1),
(22, 6, 'Brad Pitt', 0),
(23, 6, 'Tom Cruise', 0),
(24, 6, 'Johnny Depp', 0),
(25, 7, '1939', 1),
(26, 7, '1941', 0),
(27, 7, '1945', 0),
(28, 7, '1936', 0),
(29, 8, '9', 0),
(30, 8, '10', 0),
(31, 8, '11', 1),
(32, 8, '12', 0),
(33, 9, 'Disneyland París', 0),
(34, 9, 'Disney World Europa', 0),
(35, 9, 'Parc Disneyland', 0),
(36, 9, 'Disneyland París Resort', 1),
(37, 10, '1989', 1),
(38, 10, '1990', 0),
(39, 10, '1987', 0),
(40, 10, '1991', 0),
(41, 11, 'Fútbol', 1),
(42, 11, 'Béisbol', 0),
(43, 11, 'Baloncesto', 0),
(44, 11, 'Tenis', 0),
(45, 12, 'J.K. Rowling', 1),
(46, 12, 'J.R.R. Tolkien', 0),
(47, 12, 'George R.R. Martin', 0),
(48, 12, 'Stephen King', 0),
(53, 13, '100°C', 1),
(54, 13, '0°C', 0),
(55, 13, '212°C', 0),
(56, 13, '60°C', 0),
(57, 14, 'Vaticano', 1),
(58, 14, 'Mónaco', 0),
(59, 14, 'Granada', 0),
(60, 14, 'Malta', 0);

CREATE TABLE pregunta_respondida (
                                     id INT PRIMARY KEY auto_increment,
                                     id_usuario varchar(255),
                                     id_pregunta INT,
                                     acierto tinyint(1),
                                     FOREIGN KEY (id_usuario) REFERENCES user(username),
                                     FOREIGN KEY (id_pregunta) REFERENCES pregunta(id)
);

create table partida(
                        id INT primary key auto_increment,
                        username VARCHAR(255),
                        ult_pregunta INT NOT NULL,
                        fecha datetime NOT NULL,
                        puntuacion INT NOT NULL,
                        CONSTRAINT FK_PartidaUser FOREIGN KEY (username) REFERENCES user(username)
);

CREATE TABLE `sugerencia` (
  `id` INT primary key auto_increment,
  `contenido` varchar(255) NOT NULL,
  `estado` tinyint(1) NOT NULL,
  `username` varchar(255) NOT NULL,
  FOREIGN KEY (username) REFERENCES user(username)
);

CREATE TABLE `reporte` (
  `id` int(11) PRIMARY KEY auto_increment,
  `username` varchar(255) NOT NULL,
  `id_pregunta` int(11) NOT NULL,
  FOREIGN KEY (username) REFERENCES user(username),
  FOREIGN KEY (id_pregunta) REFERENCES pregunta(id)
)
