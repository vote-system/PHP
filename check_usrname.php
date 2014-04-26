<?php
require_once("vote_fns.php");
$usrname=$_POST['usrname'];

$uniq_res = username_unique($usrname);

if($uniq_res == DB_ITEM_FOUND)
{				
	$reg_resp['name_used'] = NAME_BEEN_USED; 
	echo json_encode($reg_resp);
	return;	
}
else if($uniq_res == DB_ITEM_NOT_FOUND)
{
	$reg_resp['name_used'] = NAME_NOT_USED; 
	echo json_encode($reg_resp);
	return;
}
else
{
	$reg_resp['name_used'] = NAME_CHECK_ERROR;
	echo json_encode($reg_resp);
	return;
}

?>