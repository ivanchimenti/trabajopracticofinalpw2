CREATE DATABASE `tpfinal`;

USE `tpfinal`;

CREATE TABLE `users` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `authToken` varchar(255) NOT NULL
);

ALTER TABLE `users`
  ADD PRIMARY KEY (`username`);