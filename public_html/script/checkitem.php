<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

$o=$_POST["o"];

if ($_POST["c"])
	$check=0;
else
	$check=1;

if (isset($_GET["mobileapp"])) {
	$u=mysql_real_escape_string($_GET["usr"]);
}
else {
	$u=mysql_real_escape_string($_SESSION["usr"]);
}

$sql="SELECT userID FROM users WHERE userName='" . $u . "'";

$result=mysql_query($sql);

$row=mysql_fetch_array($result);

$userId=$row['userID'];
	
$sql="UPDATE `ideas` SET `checked`=" . $check . " WHERE `order`=" . $o . " AND `userId`=" . $userId;

echo '{"status":"' . mysql_query($sql) . '","edited":"' . mysql_insert_id() . '"}';
?>