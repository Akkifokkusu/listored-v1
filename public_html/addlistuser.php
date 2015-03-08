<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

defined("ENT_XHTML") or define("ENT_XHTML",32);

mysql_select_db("listored", $con);

$t=mysql_real_escape_string($_GET["t"]);
$l=mysql_real_escape_string($_SESSION["list"]);

$query1="SELECT users FROM lists WHERE listId=" . $l;
$row=mysql_fetch_array(mysql_query($query1));
$currUsers=$row['users'];

$query2="SELECT userId FROM users WHERE username='" . $t . "'";
$query2Result=mysql_query($query2);
if (mysql_num_rows($query2Result)) {
	$row=mysql_fetch_array($query2Result);
	$newUserId="," . $row['userId'] . ",";

	if (!strpos($currUsers, $newUserId)) {
		$newUserString=$currUsers . $newUserId;
		$query3="UPDATE lists SET users='" . $newUserString . "' WHERE listId=" . $l;

		if (!mysql_query($query3)) {
			echo "3";
		}
		else {
			echo "2";
		}
	}
	else {
		echo "1";
	}
}
else {
	echo "0";
}

mysql_close($con);

?>
