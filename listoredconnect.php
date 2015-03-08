<?php
$con = mysql_connect('DB_HOST', 'DB_USER', 'DB_PASS');
if (!$con) {
	die('Could not connect: ' . mysql_error());
}

mysql_select_db("listored", $con);

$key="SECRET_KEY";
?>
