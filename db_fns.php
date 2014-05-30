<?php
require_once('vote_fns.php');

if(DB_DEBUG)
	test_db();

function test_db()
{
	$usrname="testdb";
	$email="testdb@test.com";
	$passwd=111111;
/*
	$query = "insert into usrinfo values
             (NULL,sha1('".$password."'), '".$email."',NULL, NULL,'".$usrname."', NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)";
	$ret = vote_db_query($query);

	$query = "select * from usrinfo where usrname='".$usrname."'";
	$name_existed = vote_item_existed_test($query);
	echo "name_existed = " . $name_existed . "\n";

	$fetch_name="dingyi";
	$query = "select * from usrinfo where usrname='".$fetch_name."'";
	$row = vote_get_array($query);
	print_r($row);
	*/
}

function vote_db_connect() 
{
   if(DB_DEBUG)
	  echo "function vote_db_connect\n";
   $result = new mysqli('localhost', 'root', '841023', 'vote');
   //print_r($result);
   if (!$result) {
	 //$msg = "db connect error!";
	 //$log->general($msg);
	 if(DB_DEBUG)
		echo "VOTE_DB_ERROR\n";
	 return VOTE_DB_ERROR;
   } else {
     return $result;
   }
}

function vote_db_closed($result)
{
	//if($result)
	//	$result->free();
}

function vote_db_query($query)
{
  if(DB_DEBUG)
		echo "function vote_db_query\n";
  $conn = vote_db_connect();
  $result = $conn->query($query);
  if (!$result) {
	if(DB_DEBUG)
		echo "in vote_db_query, VOTE_DB_ERROR\n";
	return VOTE_DB_ERROR;
  }
  //vote_db_closed($result);
  return $result;
}

function vote_get_array($query)
{
  $result = vote_db_query($query);
  if($result){
	  $vote_array = $result->fetch_array();
	  //vote_db_closed($result);
	  return $vote_array;
  }
}

function vote_get_assoc($query)
{
  $result = vote_db_query($query);
  if($result){
	  $posts = array();
	  while ($row = $result->fetch_assoc()) {
		$posts[] = $row;
	  }
	  vote_db_closed($result);
	  return $posts;
  }
}

function vote_item_existed_test($query)
{
  // connect to db
  if(DB_DEBUG)
	echo "function vote_item_existed_test\n";
  $conn = vote_db_connect();
  if(!$conn){
	if(DB_DEBUG)
		echo "in vote_item_existed_test, DB_CONNECT_ERROR\n";
	return VOTE_DB_ERROR;
  }
  // check if usrname is unique
  $result = $conn->query($query);
  if (!$result) {
	if(DB_DEBUG)
		echo "in vote_item_existed_test, DB_CONNECT_ERROR\n";
	return VOTE_DB_ERROR;
  }

  vote_db_closed($result);
  if ($result->num_rows>0) {
	return true;  
  }else{
	return false;
  }
	
}


?>
