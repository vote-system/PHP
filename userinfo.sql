create database cwork;
use cwork;

create table user  (
  username varchar(16) primary key,
  passwd char(40) not null,
  email varchar(100) not null
);

grant insert, update, delete,select
on cwork.*
to bo@localhost identified by 'password';
