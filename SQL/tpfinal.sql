CREATE DATABASE `tpfinal`;

USE `tpfinal`;

CREATE TABLE `users` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `authToken` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `birth_year` int(11) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_picture` varchar(255) NOT NULL
);


CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
)

CREATE TABLE `pregunta` (
  `id` int(11) NOT NULL,
  `texto` varchar(255) NOT NULL,
  `id_categoria` int(11) NOT NULL
)

CREATE TABLE `respuesta` (
  `id` int(11) NOT NULL,
  `texto` varchar(255) DEFAULT NULL,
  `id_pregunta` int(11) NOT NULL
)

ALTER TABLE `users`
  ADD PRIMARY KEY (`username`);

create table Pregunta(
                         id INT auto_increment,
                         categoria INT NOT NULL,
                         contenido TEXT NOT NULL,
                         primary key(id)
);

create table Respuesta(
                          id INT auto_increment,
                          idPregunta INT NOT NULL,
                          contenido TEXT NOT NULL,
                          correcta tinyint not null,
                          primary key(id),
                          foreign key(idPregunta) references Pregunta(id)
);

INSERT INTO Pregunta(categoria,contenido) VALUES (1,'de que color era el caballo blanco de san martin?'),(2,'cuantos balones de oro tiene Messi?'),(3,'quien fue el director de la pelicula "el resplandor"?');
INSERT INTO Respuesta(idPregunta,contenido,correcta) VALUES (1,'marron',0),(1,'blanco',1),(1,'verde',0),(1,'negro',0),(2,'3',0),(2,'6',0),(2,'4',0),(2,'8',1),(3,'Stanley Kubrick',1),(3,'Woody Allen',0),(3,'Christopher Nolan',0),(3,'Ari Aster',0);
