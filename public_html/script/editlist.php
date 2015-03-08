<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

defined("ENT_XHTML") or define("ENT_XHTML",32);

mysql_select_db("listored", $con);

$l=mysql_real_escape_string($_SESSION["list"]);
$t=urldecode(htmlspecialchars(mysql_real_escape_string($_GET["t"]), (ENT_QUOTES | ENT_XHTML)));

$sql="UPDATE lists SET name='" . $t . "' WHERE listId=" . $l;

if (mysql_query($sql)) {
	$_SESSION["listName"]=$t;
	echo $t;
}
else
	echo "0";

mysql_close($con);

?>
