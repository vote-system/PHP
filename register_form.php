<html>
<head>
     <script src="register.js"></script>
     <title><?php echo $title;?></title>
     <link rel="stylesheet" href="styles/register.css" media="screen">
</head>
<body>
<form name="reg" id="reg" method="post" action="" onSubmit="return check_data()" autocomplete="off">
<table>
   <tr>
     <td>Preferred username <br />(max 16 chars):</td>
     <td valign="top"><input type="text" name="usrname" id="usrname" onChange="usercheck('check')" onBlur="usercheck('check')"
         size="16" maxlength="16"/></td></tr>		 
	<tr>
      <td input type="hidden" name="emailexist" id="emailexist"   value="0"/><input type="hidden" name="usrnameexist" id="usrnameexist"   value="0"/><input type="hidden" name="yanzhengexsit" id="yanzhengexsit"   value="0"/></td>
    </tr>	 
   <tr>
     <td>Password <br />(between 6 and 16 chars):</td>
     <td valign="top"><input type="password" name="passwd" id="passwd"
         size="16" maxlength="16"/></td></tr>
   <tr>
     <td>Confirm password:</td>
     <td><input type="password" name="passwd2" id="passwd2"
          size="16" maxlength="16"/></td></tr>
   <tr>
     <td>Email address:</td>
     <td><input type="text" name="email" id="email" size="30" maxlength="100"/></td></tr>
   <tr>
     <td colspan=2 align="center">
     <input type="submit" value="Register"></td></tr>
</table></form>
</body>

</html>
