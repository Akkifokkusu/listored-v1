<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

defined("ENT_XHTML") or define("ENT_XHTML",32);

mysql_select_db("listored", $con);

$u=mysql_real_escape_string($_SESSION["id"]);
$t=urldecode(htmlspecialchars(mysql_real_escape_string($_GET["t"]), (ENT_QUOTES | ENT_XHTML)));

$sql="INSERT INTO lists (name, users) VALUES ('" . $t . "','," . $u . ",')";

mysql_query($sql);

echo mysql_insert_id();

mysql_close($con);

?>
