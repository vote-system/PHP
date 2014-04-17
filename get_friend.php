<?php
require_once("friend_fns.php");

$usrname=$_GET['usrname'];

if($usrname)
{
  //echo "function handle_get_fri_list \n";
  $conn = db_connect();
  if(!$conn)
  {
	return DB_CONNECT_ERROR;
  }
  $usrid = usrname_to_usrid($usrname);
	
  if(FRIEND_DEBUG)
  {
	echo "usrid = " . $usrid;
  }
  
  $result = $conn->query("select * from friend where usrid='".$usrid."'");
  if (!$result) {
     //$msg = "Function register,db query failed";
     //$auth_log->general($msg);
	 return DB_QUERY_ERROR;
  }

  while ($row = $result->fetch_assoc()) 
  {
     $friend_id = $row['friend_id'];
	 if(FRIEND_DEBUG)
	 {
		echo "friend_id = " . $friend_id;
	 }
	 get_usrdetail($friendid);
  }

  /* free result set */
  $result->free();
}


?>