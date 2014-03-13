create database vote;
use vote;

create table user  (
  username varchar(16) primary key,
  passwd char(40) not null,
  email varchar(100) not null,
  cookie char(40)
);

grant insert, update, delete,select
on vote.*
to vote_admin identified by 'password';
