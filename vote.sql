create database vote;
use vote;

create table user  (
  username varchar(16) primary key,
  passwd char(40) not null,
  email varchar(100) not null,
  cookie char(40)
);

create table user_detail  (
  username varchar(16) primary key,
  gender char(1),
  signature varchar(100),
  screen_name varchar(40),
  screen_name_pinyin varchar(40),
  head_imag_url varchar(40),
  medium_imag_url varchar(40),
  tiny_imag_url varchar(40),
  info_timestamp int unsigned,
  image_timestamp int unsigned
);

grant insert, update, delete, select
on vote.*
to vote identified by 'password';
