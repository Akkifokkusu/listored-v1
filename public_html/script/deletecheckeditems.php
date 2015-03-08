<?php
session_name("listored");
session_start();

@require('../../listoredconnect.php');

mysql_select_db("listored", $con);

$deleteCounter=0;
$deleteSuccess=0;

function deleteIdeaTree ($o) {
	global $deleteCounter, $deleteSuccess;
	$deleteCounter++;
	$query="SELECT ideas.order FROM `ideas` WHERE `parent`=" . $o;
	$queryResult=mysql_query($query);
	if (mysql_num_rows($queryResult) > 0) {
		while($row=mysql_fetch_array($queryResult)) {
			$query="SELECT ideas.order FROM `ideas` WHERE `parent`=" . $row['order'];
			$queryResult=mysql_query($query);
			if (mysql_num_rows($queryResult)>0) {
				while ($row=mysql_fetch_array($queryResult)) {
					deleteIdeaTree($row['order']);
				}
			}
			$deleteQuery="DELETE FROM `ideas` WHERE `order`=" . $row['order'];
			$deleteResult=mysql_query($deleteQuery);
			$deleteSuccess+=$deleteResult;
		}
	}
	$deleteQuery="DELETE FROM `ideas` WHERE `order`=" . $o;
	$deleteResult=mysql_query($deleteQuery);
	$deleteSuccess+=$deleteResult;
}

function deleteCheckedIdeas ($o) {
	$sql="SELECT ideas.order, ideas.checked FROM `ideas` WHERE `checked`=1 AND `parent`=" . $o;
	$result = mysql_query($sql);
	while($row=mysql_fetch_array($result)) {
		if (checkChildren($row['order'])) {
			deleteIdeaTree($row['order']);
		}
	}
}

function checkChildren($o) {
	$full = "SELECT ideas.order, ideas.checked FROM `ideas` WHERE `parent`=" . $o;
	$result = mysql_query($full);
	if (mysql_num_rows($result) > 0) {
		while ($row=mysql_fetch_array($result)) {
			if ($row['checked'] == 1) {
				if (checkChildren($row['order']) == false) {
					return false;
				}
			}
			else {
				return false;
			}
		}
	}
	else {
	}
	return true;
}



$o=$_GET["o"];

deleteCheckedIdeas($o);

if ($deleteCounter > 0) {
	if (($deleteSuccess/$deleteCounter)>=1) {
		echo 1;
	}
	else {
		echo 0;
	}
}
else {
	echo -1;
}
?>
