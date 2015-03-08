<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

defined("ENT_XHTML") or define("ENT_XHTML",32);

function buildIdeaList ($sqlResult) {
	global $key;
	echo "<ul>";
	while($row=mysql_fetch_array($sqlResult)) {
		$parent=$row['parent'];
		$query="SELECT AES_DECRYPT(ideas.idea,'" . $key . "') AS idea,ideas.order,ideas.parent FROM ideas WHERE parent=" . $row['order'];
		$queryResult=mysql_query($query);
		$numChildren=mysql_num_rows($queryResult);
		echo "<li>";
		if ($parent==0)
			echo "<span style=\"font-weight: bold;\">";
		echo urldecode(htmlspecialchars($row['idea'], (ENT_QUOTES | ENT_XHTML)));
		if ($parent==0)
		echo "</span>";
		if ($queryResult)
			buildIdeaList($queryResult);
		echo "</li></ul>";
	}
}

mysql_select_db("listored", $con);

$sql="SELECT AES_DECRYPT(ideas.idea,'" . $key . "') AS idea,ideas.order,ideas.parent FROM ideas INNER JOIN lists ON ideas.listId=lists.listId WHERE parent=0 AND lists.listId=4 ORDER BY ideas.order DESC";

$result=mysql_query($sql);

buildIdeaList($result);

mysql_close($con);
?>
