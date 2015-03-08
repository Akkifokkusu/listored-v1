<?php
session_name("listored");
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" src="script/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="script/ideas.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>IdeaBag</title>
</head>
<body>
	<h1>IdeaBag</h1>
<h2>Changelog</h2>
<div id="listored">Now loading<img src="res/load.gif" alt="A small spinning black circle." title="Loading" style="margin-left: 10px;" /></div>
<p><a href="index.php">Back</a></p>
<script type="text/javascript">getChangelog();</script>
<div style="clear: both;"><h5>IdeaBag (<a href="changelog.php" id="version"></a>) &copy;2012 Igor Dubinskiy</h5></div>
</body>
</html>
