<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

if (!$_POST['username'] || !$_POST['password']) {
	echo "2";
	exit;
}

$username=mysql_real_escape_string($_POST["username"]);
$password=mysql_real_escape_string(md5($_POST["password"]));
$remember=$_POST["remember"];

$row=mysql_fetch_assoc(mysql_query("SELECT userId,userName,currList FROM users WHERE userName='" . $username . "' AND password='" . $password . "'"));
if ($row["userId"]>=0) {
	$sql = mysql_query("SELECT name FROM lists WHERE listId=" . $row["currList"]);
	if ($sql) {
		$list=mysql_fetch_assoc($sql);
		$_SESSION["usr"]=$row["userName"];
		$_SESSION["id"]=$row["userId"];
		$_SESSION["list"]=$row["currList"];
		$_SESSION["listName"]=$list["name"];

		if ($remember=="true") {
			setcookie('listoredRemember',true,(time()+60*60*24*30),'/');
			setcookie('listoredUsr',$row["userName"],(time()+60*60*24*30),'/');
			setcookie('listoredId',$row["userId"],(time()+60*60*24*30),'/');
			setcookie('listoredList',$row["currList"],(time()+60*60*24*30),'/');
			setcookie('listoredListName',$list["name"],(time()+60*60*24*30),'/');
			setcookie('listoredExpires',(time()+60*60*24*30),(time()+60*60*24*30),'/');
		}
		echo "1";
	}
	else {
		echo "0";
	}
}
else {
	echo "0";
}
mysql_close($con);
?>