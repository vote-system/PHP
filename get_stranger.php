<?php
require_once("vote_fns.php");

$usrname = $_POST['usrname'];
header('Content-Type: application/json');

if($usrname)
{
	//1.clear the friend_badge in usrinfo
	$query = "update usrinfo set friend_badge = 0
							where usrname = '".$usrname."'";
	vote_db_query($query);

	//2.return the stranger info to usr
    ret_stranger_info($usrname);
}

function ret_stranger_info($usrname)
{
  $conn = db_connect();
  if(!$conn)
  {
	//$msg = "Function do_update_friend_db,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }
   
  $query = "select * from usrinfo where usrname = '".$usrname."'";
  $result = $conn->query($query);
  if (!$result) {
    //$msg = "Function register,db insert failed";
	//$auth_log->general($msg);
	return DB_INSERT_ERROR;
  }
  $num_results = $result->num_rows;
  for ($i=0; $i <$num_results; $i++)
  {
	  $row = $result->fetch_assoc();
	  $stranger['usrname'] = $row['usrname'];
	  $stranger['signature'] = $row['signature'];
	  $stranger['screen_name'] = $row['screen_name'];
	  $stranger['gender'] = $row['gender'];
	  $stranger['original_head_imag_url'] = $row['original_head_imag_url'];
	  $stranger['medium_head_imag_url'] = $row['medium_head_imag_url'];
	  echo json_encode($stranger); 
  }
	
}

?>