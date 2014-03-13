<?php
 
//macro for all the source code.

//TRUE and FAIL has the same value with system defined.
//define('FAIL', 0);
//define('TRUE', 1);

//macro for database
define('DB_CONNECT_ERROR', 0);
define('DB_CONNECT_SUCCESS', 1);
define('DB_QUERY_ERROR', 2);
define('DB_INSERT_ERROR', 3);
define('DB_DELETE_ERROR', 4);
define('DB_ITEM_FOUND', 5);
define('DB_ITEM_NOT_FOUND', 6);

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
define('COOKIE_LOGIN_SUCCESS', 1);
define('COOKIE_SAVE_SUCCESS', 2);
define('COOKIE_NOT_SAVED', 3);

//macro to check whether name have been used
define('NAME_BEEN_USED', 1);
define('NAME_NOT_USED', 0);
define('NAME_CHECK_ERROR', -1);

// We can include this file in all our files
// this way, every file will contain all our functions and exceptions
require_once('data_valid_fns.php'); 
require_once('db_fns.php');
require_once('user_auth_fns.php');
require_once('log.php');
?>
