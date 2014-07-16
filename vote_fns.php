<?php

//ini_set('display_errors','yes');
//report all errors
//error_reporting(E_ALL | E_STRICT);
error_reporting(E_ALL);
//report runtime error
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
//report no error
//error_reporting(0);

//TRUE and FAIL has the same value with system defined.
//define('FAIL', 0);
//define('TRUE', 1);

define("USER_NOT_ACTIVE",0);
define("USER_ACTIVE",1);

//macro to control debug log
define('REG_DEBUG', 0);
define('LOGIN_DEBUG', 0);
define('USR_AUTH_DEBUG', 0);
define('FRIEND_DEBUG',0);
define('DB_DEBUG',0);
define('UPDATE_HEAD_IMAGE_DEBUG',1);

//macro for vote_db
define('VOTE_DB_ERROR', 0);

//macro for database
define('DB_CONNECT_ERROR', 0);
define('DB_CONNECT_SUCCESS', 1);
define('DB_QUERY_ERROR', 2);
define('DB_INSERT_ERROR', 3);
define('DB_UPDATE_ERROR', 4);
define('DB_DELETE_ERROR', 5);
define('DB_ITEM_FOUND', 6);
define('DB_ITEM_NOT_FOUND', 7);
define('DB_ERROR', 8);

//macro for regist
define('REGISTER_SUCCESS', 1);
define('REGISTER_ERROR', 2);
define('REGISTER_USERNAME_INUSED', 3);
define('REGISTER_USERNAME_NOTUSED', 4);
define('EMAIL_INVALID_ERROR', 5);
define('PASSWD_LENGTH_ERROR', 6);
define('REG_UNKNOWN_ERROR', 7);

//macro for login
define('LOGIN_ERROR', 0);
define('LOGIN_SUCCESS', 1);

define('LOGOUT_ERROR', 0);
define('LOGOUT_SUCCESS', 1);

define('COOKIE_LOGIN_ERROR', 0);
define('COOKIE_LOGIN_SUCCESS', 1);
define('COOKIE_SAVE_SUCCESS', 2);
define('COOKIE_NOT_SAVED', 3);

//macro to check whether name have been used
define('NAME_BEEN_USED', 1);
define('NAME_NOT_USED', 0);
define('NAME_CHECK_ERROR', -1);

//macro for changing passwd
define('CHANGE_PASSWD_SUCCESS', 0);
define('OLD_PASSWD_NOT_EXISTED', 1);
define('CONFIRM_NOT_CORRECT', 2);

//macro for update_usr_info
define('USER_NAME_NOT_FILL', 0);
define('INFO_UPDATE_SUCCESS', 1);

define("FILE_NAME_NULL", 1);
define("FILE_SIZE_NULL", 2);
define("FILE_SIZE_OVER", 3);
define("UPLOAD_CORRUPT", 4);
define("MV_FILE_FAIL", 5);
define("RESIZE_IMAGE_FAIL", 6);
define("UPDATE_IMAGE_SUCC", 7);
define("UPDATE_IMAGE_FAIL", 8);
define("FILE_DIMISION_NOT_SUPPORT", 9);

//macro for friend

//define("ADD_FRIEND_RESPONSE",3);
//define("DELETE_FRIEND_RESPONSE",4);

define("ADD_FRIEND_SUCCESS",8);
define("DELETE_FRIEND_SUCCESS",9);
define("GET_FRIEND_LIST",10);

//macro for stranger table status
define("ADD_FRIEND_NOT_SEND",0);
define("ADD_FRIEND_SEND",1);
define("DELETE_FRIEND_REQUEST",3);
define("REFUSE_FRIEND_REQUEST",5);
define("IGNORE_FRIEND_REQUEST",6);
define("IGNORE_FRIEND_RESPONSE",7);

//macro for push notification message
define("ADD_FRIEND_REQUEST",2);
define("AGREE_ADD_FRIEND",4);
define("VOTE_NOTIFICATION",5);

//macro for badge
define("ADD_BADGE",1);
define("SUBTRCT_BADGE",2);

define("USR_INFO_TIME_STAMP",1);
define("HEAD_IMAG_TIME_STAMP",2);


define("ORIGINAL_HEAD_IMAG_URL","http://115.28.228.41/vote/upload/default/default.png");
define("MEDIUM_HEAD_IMAG_URL","http://115.28.228.41/vote/upload/default/medium-default.png");
define("THUMBNAILS_HEAD_IMAG_URL","http://115.28.228.41/vote/upload/default/thumbnails-default.png");

define("SET_UP_VOTE_SUCC",0);
define("SET_UP_VOTE_FAIL",1);
define("VOTE_EXISTED",2);

define("UPDATE_SUCCESS",0);
define("UPDATE_FAIL",1);

define("SUBMIT_VOTE_SUCC",0);
define("SUBMIT_VOTE_ERROR",1);

?>

