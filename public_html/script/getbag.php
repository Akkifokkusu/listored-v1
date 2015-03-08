<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

defined("ENT_XHTML") or define("ENT_XHTML",32);

function buildIdeaList ($sqlResult, $isBase, $parent) {

	global $key;
	global $ex;
	global $l;

	$numRows = mysql_num_rows($sqlResult);
	$rowCounter = 0;
	while($row=mysql_fetch_array($sqlResult)) {
		$query="SELECT AES_DECRYPT(ideas.idea,'" . $key . "') AS idea,ideas.order,ideas.checked FROM ideas WHERE parent=" . $row['order'] . " ORDER BY ideas.order ASC";
		$queryResult=mysql_query($query);
		$numChildren=mysql_num_rows($queryResult);

		echo '{"order":"' . $row['order'] . '","item":' . json_encode(urldecode($row['idea'])) . ',"checked":' . $row['checked'];

		if ($numChildren>0) {
			echo ',"children":[';
			buildIdeaList($queryResult, 0, $row['order']);
			echo ']';
		}

		echo "}";
		if (++$rowCounter < $numRows) {
			echo ",";
		}
}
}

if(isset($_COOKIE["listoredEx"])) {
	if (strpos(trim($_COOKIE["listoredEx"], "|"), "|") !== false) {
		$ex = explode("|", urldecode($_COOKIE["listoredEx"]));
	}
	else
		$ex[0] = trim($_COOKIE["listoredEx"], "|");
}
else
	$ex[0] = "";

echo '{"listData": [';

$l=mysql_real_escape_string($_SESSION["list"]);

mysql_select_db("listored", $con);

$sql="SELECT AES_DECRYPT(ideas.idea,'" . $key . "') AS idea,ideas.order,ideas.checked FROM ideas INNER JOIN lists ON ideas.listId=lists.listId WHERE parent=0 AND ideas.listId=" . $l . " ORDER BY ideas.order ASC";

$result=mysql_query($sql);

buildIdeaList($result, 1, 0);

echo "]}";

mysql_close($con);
?>
