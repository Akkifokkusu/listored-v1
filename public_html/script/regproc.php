<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

function checkEmail($email) {
	if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email)) {
		return false;
	}
	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	for ($i = 0; $i < sizeof($local_array); $i++) {
		if
		(!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'↪*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/",$local_array[$i])) {
			return false;
		}
	}
	if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) {
		$domain_array = explode(".", $email_array[1]);
		if (sizeof($domain_array) < 2) {
			return false;
		}
		for ($i = 0; $i < sizeof($domain_array); $i++) {
			if
			(!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|↪([A-Za-z0-9]+))$/",$domain_array[$i])) {
				return false;
			}
		}
	}
	return true;
}

$err=0;

if (strlen($_POST['username'])<4 || strlen($_POST['username'])>32)
	$err+=16;

if (strlen($_POST['password'])<4)
	$err+=8;

if (preg_match("/[^a-z0-9\-\_\.]+/i",$_POST['username']))
	$err+=4;

if (!checkEmail($_POST['email']))
	$err+=2;

if ($err>0) {
	echo $err;
	exit;
}

$username=mysql_real_escape_string($_POST["username"]);
$password=mysql_real_escape_string(md5($_POST["password"]));
$email=mysql_real_escape_string($_POST["email"]);

$query1="INSERT INTO users (userName,password,email,currList) VALUES ('" . $username . "','" . $password . "','" . $email . "',-1)";
$query1Success = mysql_query($query1);
$userId=mysql_insert_id();
$query2="INSERT INTO lists (name, users) VALUES ('" . $username . "','," . $userId . ",')";
$query2Success = mysql_query($query2);
$query3="UPDATE users SET currList=" . mysql_insert_id() . " WHERE userId=" . $userId;
$query3Success = mysql_query($query3);
if ($query1Success && $query2Success && $query3Success) {
	echo "1";
}
else
	echo "0";

mysql_close($con);
?>
