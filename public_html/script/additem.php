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

$i=mysql_real_escape_string($_POST["i"]);
$l=mysql_real_escape_string($_SESSION["list"]);
$p=$_POST['p'];

$sql="SELECT userID FROM users WHERE userName='" . $u . "'";

$result=mysql_query($sql);

$row=mysql_fetch_array($result);

$userId=$row['userID'];

$sql="INSERT INTO ideas (userId, listId, idea, parent) VALUES (" . $userId . "," . $l . ", AES_ENCRYPT('" . $i . "','" . $key . "')," . $p .")";

echo '{"status":"' . mysql_query($sql) . '","insertedOrder":"' . mysql_insert_id() . '"';

$sql="SELECT AES_DECRYPT(idea,'" . $key . "') AS idea FROM ideas WHERE `order`=" . mysql_insert_id();

$result=mysql_query($sql);

$row=mysql_fetch_array($result);

echo ',"insertedItem":' . json_encode(urldecode($row['idea'])) . '}';
?>