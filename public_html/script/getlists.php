<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

defined("ENT_XHTML") or define("ENT_XHTML",32);

mysql_select_db("listored", $con);

$u=mysql_real_escape_string($_SESSION["id"]);
$l=mysql_real_escape_string($_SESSION["list"]);

$lists=mysql_query("SELECT listId, name FROM lists WHERE users LIKE '%," . $u . ",%'");

while ($row=mysql_fetch_array($lists)) {
	echo "<option";
	if ($row["listId"] == $l)
		echo " selected=\"selected\"";
	echo " value=\"" . $row["listId"] . "\" >" . urldecode(htmlspecialchars($row["name"], (ENT_QUOTES | ENT_XHTML))) . "</option>";
}

mysql_close($con);

?>