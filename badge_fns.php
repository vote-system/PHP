<?php
require_once("vote_fns.php");


//test_badge();
function test_badge()
{	
	$usrname="dingyi";
	$badge = query_badge($usrname);
	echo $badge['friend_badge'];
	echo $badge['usr_vote_badge'];
}

function update_badge($badge_name,$usrname,$action)
{
  $badge = query_badge($usrname);
  if(!$badge){
	  return false;
  }else{
	if($action == ADD_BADGE)
		$badge +=1;
	else
		$badge -=1;

	$query = "update usrinfo set '".$badge_name."' = '".$badge."'
							where usrname = '".$usrname."'";
	$result = vote_db_query($query);
	if(!$result){
		return false;
	}else{
		return true;
	}
  }
  return false;
}

function query_badge($usrname)
{
  //save the usrid
  $query = "select * from usrinfo where usrname='".$usrname."'";
  $badge = vote_get_array($query);
  return $badge;
}

function get_user_badge($usrname)
{	
	$badge = query_badge($usrname);
	$total_badge = $badge['friend_badge'] +  $badge['usr_vote_badge'];
	return $total_badge;
}

?>

