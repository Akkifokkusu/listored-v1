<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

mysql_select_db("listored", $con);

$l=mysql_real_escape_string($_SESSION["list"]);

do {
	$row=mysql_fetch_assoc(mysql_query("SELECT updated FROM lists WHERE listId='" . $l . "'"));
	$updated=strtotime($row["updated"]);
	sleep(1);
}
while ($_SESSION["updated"]>$updated);

mysql_close($con);

echo 1;

?>
