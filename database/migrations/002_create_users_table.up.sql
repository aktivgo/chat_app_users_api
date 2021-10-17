create table if not exists users
(
    id       int(11)      not null primary key auto_increment,
    login    varchar(255) not null ,
    password varchar(255) not null,
    fullName varchar(255) null,
    email varchar(255) null,
    confirmed boolean null default false
);