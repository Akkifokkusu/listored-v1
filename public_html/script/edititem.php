<?php

session_name("listored");
session_start();

@require('../../listoredconnect.php');

if (isset($_GET["mobileapp"])) {
	$u=mysql_real_escape_string($_GET["usr"]);
}
else {
	$u=mysql_real_escape_string($_SESSION["usr"]);
}

$t=mysql_real_escape_string($_POST["t"]);
$o=$_POST['o'];

$sql="SELECT userID FROM users WHERE userName='" . $u . "'";

$result=mysql_query($sql);

$row=mysql_fetch_array($result);

$userId=$row['userID'];

$sql="UPDATE `ideas` SET `idea`=AES_ENCRYPT('" . $t . "','" . $key . "') WHERE `order`=" . $o . " AND `userId`=" . $userId;

echo '{"status":"' . mysql_query($sql) . '"';

$sql="SELECT AES_DECRYPT(idea,'" . $key . "') AS idea FROM ideas WHERE `order`=" . $o;

$result=mysql_query($sql);

$row=mysql_fetch_array($result);

echo ',"editedOrder":"' . $o . '","editedItem":' . json_encode(urldecode($row['idea'])) . '}';
?>