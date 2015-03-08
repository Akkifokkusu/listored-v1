<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

defined("ENT_XHTML") or define("ENT_XHTML",32);

mysql_select_db("listored", $con);

$u=mysql_real_escape_string($_SESSION["id"]);
$l=mysql_real_escape_string($_SESSION["list"]);

$query1="DELETE FROM ideas WHERE listId=" . $l;
mysql_query($query1);

$query2="DELETE FROM lists WHERE listId=" . $l;
mysql_query($query2);

$query3="SELECT listId FROM lists where users='," . $u . ",'";
$row=mysql_fetch_array(mysql_query($query3));

echo $row["listId"];

mysql_close($con);

?>
