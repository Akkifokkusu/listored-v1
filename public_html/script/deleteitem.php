<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

$deleteCounter=0;
$deleteSuccess=0;

function deleteIdeaTree ($sqlResult) {
	global $deleteCounter, $deleteSuccess;
	$deleteCounter++;
	while($row=mysql_fetch_array($sqlResult)) {
		$query="SELECT ideas.order FROM `ideas` WHERE `parent`=" . $row['order'];
		$queryResult=mysql_query($query);
		if (mysql_num_rows($queryResult)>0)
			deleteIdeaTree($queryResult);
		$deleteQuery="DELETE FROM `ideas` WHERE `order`=" . $row['order'];
		$deleteResult=mysql_query($deleteQuery);
		$deleteSuccess+=$deleteResult;
	}
}

$o=$_POST["o"];

if (isset($_GET["mobileapp"])) {
	$u=mysql_real_escape_string($_GET["usr"]);
}
else {
	$u=mysql_real_escape_string($_SESSION["usr"]);
}

$sql="SELECT userID FROM users WHERE userName='" . $u . "'";

$result=mysql_query($sql);

$row=mysql_fetch_array($result);

$userId=$row['userID'];

$sql="SELECT ideas.order FROM `ideas` WHERE `order`=" . $o . " AND `userId`=" . $userId;

$result=mysql_query($sql);

deleteIdeaTree($result);

if (($deleteSuccess/$deleteCounter)>=1) {
	echo '{"status":"1"}';
}
else
	echo '{"status":"0"}';
?>