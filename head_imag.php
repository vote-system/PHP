<?php
require_once('vote_fns.php');

$date = new DateTime();
$timestamp = $date->getTimestamp();
 
$username=$_POST['username'];
$upload_dir = "/vote/upload/$username";

if(!is_dir($upload_dir))
{
	$oldumask = umask(0);
	$res = mkdir($upload_dir, 0777);
	if(!$res)
	{
		$msg = "mkdir error for $upload_dir\n";
		echo "mkdir error for $upload_dir\n";
		//error_log($msg,3,"/alidate/log");
	}
	umask($oldumask);
}

  $i = 0;
  while (($_FILES['userfile']['name'][$i]) &&
         ($_FILES['userfile']['name'][$i] !='none')) {

    if ($_FILES['userfile']['size'][$i]==0) {
      //echo "<p>Problem: ".$_FILES['userfile']['name'][$i].
      //     " is zero length";
      $i++;
      continue;
    }

    if ($_FILES['userfile']['size'][$i]>$max_size) {
      //echo "<p>Problem: ".$_FILES['userfile']['name'][$i]." is over "
      //      .$max_size." bytes";
      $i++;
      continue;
    }

    // we would like to check that the uploaded image is an image
    // if getimagesize() can work out its size, it probably is.
    if(($i>1) && (!getimagesize($_FILES['userfile']['tmp_name'][$i]))) {
      echo "<p>Problem: ".$_FILES['userfile']['name'][$i].
           " is corrupt, or not a gif, jpeg or png.</p>";
      $i++;
      continue;
    }

    // file 0 (the text message) and file 1 (the html message) are special cases
    if($i==0) {
      $destination = "archive/".$list."/".$mailid."/text.txt";
    } else if($i == 1) {
      $destination = "archive/".$list."/".$mailid."/index.html";
    } else  {
      $destination = "archive/".$list."/".$mailid."/"
                     .$_FILES['userfile']['name'][$i];
      $query = "insert into images values ('".$mailid."',
                       '".$_FILES['userfile']['name'][$i]."',
                       '".$_FILES['userfile']['type'][$i]."')";

      $result = $conn->query($query);
    }

    if (!is_uploaded_file($_FILES['userfile']['tmp_name'][$i])) {
      // possible file upload attack detected
      echo "<p>Something funny happening with "
           .$_FILES['userfile']['name'].", not uploading.";
      do_html_footer();
      exit;
    }

    move_uploaded_file($_FILES['userfile']['tmp_name'][$i],
                       $destination);

    $i++;
  }


if($head_imag_url)
{
	//$item_name='head_imag_url';
	//update_item($item_name,$head_imag_url);	
	//return;

	$res = $conn->query("update user_detail
							set gender = '".$gender."'
							where username = '".$username."'");
	if (!$res) {
		echo "db_update error\n";
		$msg = "Function update_usr_info,db update failed";
		//$auth_log->general($msg);
		$usrinfo_resp['usrinfo_code'] = DB_UPDATE_ERROR; //user name and passwd not correct!
		header('Content-Type: application/json');
		echo json_encode($usrinfo_resp);
	 }
	 else{
		$usrinfo_resp['usrinfo_code'] = INFO_UPDATE_SUCCESS; //user name and passwd not correct!
		header('Content-Type: application/json');
		echo json_encode($usrinfo_resp);
	 }
}

?>