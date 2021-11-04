create table if not exists users
(
    id       int(11)      not null primary key auto_increment,
    fullName varchar(255) null,
    login    varchar(255) not null,
    password varchar(255) not null
);