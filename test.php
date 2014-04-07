<?php
require_once("friend_fns.php");
require_once("vote_fns.php");

test_friend();
function test_friend()
{
	add_friend_response("zhaobo","dingyi",AGREE_ADD_FRIEND);
	//add_friend_response("zhaobo","dingyi",REFUSE_ADD_FRIEND);
}

?>