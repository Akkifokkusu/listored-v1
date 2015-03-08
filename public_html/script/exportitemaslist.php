<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

mysql_select_db("listored", $con);

$u=mysql_real_escape_string($_SESSION["id"]);
$o=$_GET["o"];

$query1="SELECT AES_DECRYPT(ideas.idea,'" . $key . "') AS idea FROM ideas WHERE `order`=" . $o;
$row=mysql_fetch_array(mysql_query($query1));
$ideaName=$row["idea"];

$query2="INSERT INTO lists (name, users) VALUES ('" . $ideaName . "','," . $u . ",')";
mysql_query($query2);
$newListId=mysql_insert_id();

function changeListId ($order) {
	$sql="SELECT `order`, parent FROM ideas WHERE parent=" . $order;
	$sqlResult=mysql_query($sql);
	while ($row=mysql_fetch_array($sqlResult)) {
		global $o;
		if ($row["parent"]==$o) {
			$changeParentQuery="UPDATE ideas SET parent=0 where `order`=" . $row["order"];
			mysql_query($changeParentQuery);
		}
		global $newListId;
		$changeListIdQuery="UPDATE ideas SET listId=" . $newListId . " WHERE `order`=" . $row["order"];
		mysql_query($changeListIdQuery);
		changeListId($row["order"]);
	}
};

changeListId($o);

mysql_query("DELETE FROM ideas WHERE `order`=" . $o);

echo $newListId;

mysql_close($con);

?>
