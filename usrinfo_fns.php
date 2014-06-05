<?php
require_once('db_fns.php');
require_once('vote_fns.php');

function update_friend_badge($usrname,$action)
{
    $usrinfo = query_badge($usrname);
    $friend_badge = $usrinfo['friend_badge'];
    //echo $friend_badge;
	if($friend_badge < 0)
		return;

	if($action == ADD_BADGE){
		$friend_badge +=1;
	}else{
		$friend_badge -=1;
	}
	//echo $usrname;
	$query = "update usrinfo set friend_badge = '".$friend_badge."'
							where usrname = '".$usrname."'";
	$result = vote_db_query($query);
	//echo $result;
	if(!$result){
		return false;
	}else{
		return true;
	}
}

function query_badge($usrname)
{
  //save the usrid
  $query = "select * from usrinfo where usrname='".$usrname."'";
  $usrinfo = vote_get_array($query);
  return $usrinfo;
}

function get_user_badge($usrname)
{	
	$badge = query_badge($usrname);
	$total_badge = $badge['friend_badge'] +  $badge['usr_vote_badge'];
	return $total_badge;
}


function get_usrdetail($friendid)
{
  $query = "select * from usrinfo where usrid='".$friendid."'";
  $rows = vote_get_assoc($query);
  foreach($rows as $row) 
  {
	 //only return part of items in table usrinfo
     $friend_info = array_slice($row,5,15);
	 $friend_array["friends_array"] = $friend_info;
	 //not sure whether need to put the following line out of the while loop
	 echo json_encode($friend_info);
  }
}

function usrname_to_usrid($usrname)
{
  $query = "select * from usrinfo where usrname='".$usrname."'";
  $row = vote_get_array($query);
  return $row['usrid']; 
}

function usrid_to_usrname($usrid)
{
  $query = "select * from usrinfo where usrid='".$usrid."'";
  $row = vote_get_array($query);
  return $row['usrname']; 
}

function query_usr_info($usrname)
{
  $query = "select * from usrinfo where usrname='".$usrname."'";
  $usrinfo = vote_get_array($query);
  return $usrinfo;
}

function search_token_from_db($usrname)
{
	$query = "select * from usrinfo where usrname='".$usrname."'";
	$usrinfo = vote_get_array($query);
	$token = $usrinfo["device_token"];
	return $token;
}


?>