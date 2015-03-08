<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

defined("ENT_XHTML") or define("ENT_XHTML",32);

mysql_select_db("listored", $con);

$u=mysql_real_escape_string($_SESSION["id"]);
$l=mysql_real_escape_string($_GET["l"]);

$sql="UPDATE users SET currList=" . $l . " WHERE userId=" . $u;

mysql_query($sql);

mysql_close($con);

?>
