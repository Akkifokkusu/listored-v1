<?php
session_name("listored");
session_start();

if (isset($_GET["logout"])) {
	if (isset($_COOKIE["listoredRemember"])) {
		setcookie("listoredRemember","",time()-60*60*24*30,'/');
		setcookie("listoredUsr","",time()-60*60*24*30,'/');
		setcookie("listoredId","",time()-60*60*24*30,'/');
		setcookie('listoredList',"",(time()-60*60*24*30),'/');
		setcookie('listoredListName',"",(time()-60*60*24*30),'/');
		setcookie('listoredEx',"",(time()-60*60*24*30),'/');
		setcookie('listoredExpires',"",(time()-60*60*24*30),'/');
	}
	$_SESSION=array();
	session_destroy();
	header("Location: index.php");
	exit;
}

if (!isset($_GET["list"])) {
	if (isset($_COOKIE["listoredRemember"])) {
		if ($_COOKIE["listoredRemember"]) {
			header ("location: list/" . $_COOKIE["listoredList"]);
		}
	}
	else if (isset($_SESSION["list"])) {
		header ("location: list/" . $_SESSION["list"]);
	}
}

if (isset($_COOKIE["listoredRemember"])) {
	if ($_COOKIE["listoredRemember"]) {
		$_SESSION["usr"]=$_COOKIE["listoredUsr"];
		$_SESSION["id"]=$_COOKIE["listoredId"];
		$_SESSION["list"]=$_COOKIE["listoredList"];
		$_SESSION["listName"]=$_COOKIE["listoredListName"];
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="script/jquery.autosize.js"></script>
<link href="http://fonts.googleapis.com/css?family=Mako|Contrail+One" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="script/style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="script/print.css" media="print" />
<link rel="stylesheet" type="text/css" href="script/mobile.css" media="handheld, only screen and (max-width: 480px), only screen and (max-device-width: 480px)" />
<script type="text/javascript" src="script/login.js"></script>
<script type="text/javascript" src="script/ideas.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php $ua = strtolower($_SERVER['HTTP_USER_AGENT']); if (stripos($ua,'android')): ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />
    <?php elseif (stripos($ua,'like mac')): ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />
    <?php elseif (stripos($ua,'windows phone')): ?>
    <meta name="viewport" content="width=device-width" />
    <?php endif; ?>
    <?php if (date("n")=="10"): ?>
    	<link rel="stylesheet" type="text/css" href="script/october.css" media="screen" />
    <?php endif; ?>
    <script type="text/javascript">
<?php
if (isset($_GET["list"])):
@require('../listoredconnect.php');
mysql_select_db("listored", $con);
$sql="SELECT name, users FROM lists WHERE listId=" . $_GET["list"];;
$row=mysql_fetch_array(mysql_query($sql));
$listUsers = $row["users"];
if (stripos($listUsers,"," . $_SESSION["id"] . ",")===FALSE) {
	echo "</script></head><body style=\"margin: 0;\">";
	echo "<div id=\"floatingmenu\" style=\"top: 0; left: 0; background-color: gray; color: white; padding: 8px; position: relative;\">";
	echo "<span style=\"font-family: 'Contrail One'; font-size: 2em; font-weight: bold;\">IdeaBag</span>";
	echo "<span id=\"topmenuitems\" style=\"position: absolute; height: 21px; top: 50%; margin-top: -10px;\">Log Out</span></div>";
	echo "<div style=\"margin-left: 8px;\">Sorry, you do not have access to this list.<br /><a href=\"index.php\">Click here to go to one of your lists.</a></div>";
	echo "<div style=\"margin-left: 8px; clear: both;\"><h5>IdeaBag (<a href=\"changelog.php\" id=\"version\"></a>) &copy;2012 Igor Dubinskiy</h5></div>";
	exit("</body></html>");
}
$_SESSION["list"] = $_GET["list"];
$_SESSION["listName"]=urldecode($row["name"]);
mysql_close($con);
?>
var listRequested = true;
var listId = <?php echo $_GET["list"]; ?>;
<?php else: ?>
var listRequested = false;
<?php endif; ?>
</script>
<title>
	LiSTored
	<?php echo ((isset($_SESSION['listName'])) ? (" - " . urldecode($_SESSION['listName'])) : ""); ?>
</title>
  </head>
<body style="margin: 0 auto; background-color: palegreen; overflow: scroll;">
<div id="floatingmenu" style="top: 0; left: 0; background-color: dodgerblue; color: white; padding: 10px; position: relative; border-top-left-radius: 20px; border-top-right-radius: 20px;">
	<?php if (date("n")=="10"): ?>
    		<img id="pinkribbon" src="http://bighugelabs.com/img/nbcam/ribbon_50.png" alt="A pink breast cancer ribbon." title="National Breast Cancer Awareness Month" />
    	<?php endif; ?>
	<span style="font-family: 'Contrail One'; font-size: 2em; font-weight: bold;">LiSTored</span>
	<span id="topmenuitems" style="position: absolute; height: 21px; top: 50%; margin-top: -10px; right: 10px;"></span></div>
<div id="content" style="padding-left: 10px; padding-bottom: 10px; background-color: white; position: relative;">
<?php include_once("script/analyticstracking.php") ?>
    <?php if (!isset($_SESSION['id'])): ?>
    <div id="intro" style="clear: both; max-width: 550px;">
    	Welcome to LiSTored, the webapp for making any kind of list you can imagine: shopping lists, to-do lists, note lists, and anything else that could use a little organization. LiSTored is in beta, but we're adding features and squashing bugs all the time. Log in or sign up below, and start making lists today!
    </div>
    <div id="link" style="clear: both;">
      <a href="javascript:changeLink('reg')">Not registered yet? Click here to register!</a>
    </div>
    <div id="login">
      <form id="loginForm" style="float: left;" action="">
        <p>
          <label for="login-username">Username:</label> <input type="text" class="textbox" name="username" id="login-username" size="32" required /><br />
           <label for="login-password">Password:</label> <input type="password" class="textbox" name="password" id="login-password" size="32" required /><br />
           <label><input type="checkbox" name="rememberMe" id="rememberMe" />&nbsp;Remember me</label><br />
           <input type="submit" name="submit" value="Login" />
        </p>
      </form>
    </div>
    <div id="register">
      <form id="regForm" style="display: none; float: left;" action="">
        <p>
          <label for="reg-username">Username:</label> <input type="text" class="textbox" name="username" id="reg-username" size="32" /><br />
           <label for="reg-password">Password:</label> <input type="password" class="textbox" name="password" id="reg-password" size="32" /><br />
           <label for="email">Email:</label> <input type="text" class="textbox" name="email" id="email" size="32" /><br />
           <input type="submit" name="submit" value="Register" />
        </p>
      </form>
    </div>
    <div id="status" style="float: left;">
    </div>
    <?php else: $list=$_SESSION["listName"]; ?>
    <div style="padding: 10px 0; clear: both; position: relative; height: 22px;" id="listadd">
      <select id="lists" onchange="changeList(this.value);" style="float: left;">
      </select>
      <input type="image" onclick="addListClicked()" title="Add New List" alt="A green plus sign." src="res/add.png" id="addList" class="imagebutton" style="float: left;" />
      <input type="image" onclick="cancelListClicked()" title="Cancel Adding List" alt="A green minus sign." src="res/cancel.png" id="cancelList" class="imagebutton" style="display: none; float: left;" />
      <input type="text" class="textbox" size="30" onkeydown="return (event.keyCode!=13);" onkeyup="if (event.keyCode==13 && this.value!='') addList(this);" id="newlistbox" style="display: none; float: left; clear: right;" />
    </div>
    <div style="font-size: 1.5em; font-weight: bold; clear: both; position: relative; height: 31px; margin: 0;">
      <span style="float: left; position: relative; clear: both;">List:&nbsp;</span><span id="listName" style="float: left; position: relative;"><?php echo $list; ?></span>
      <input type="image" style="float: left; position: relative; top: 5px;" onclick="showHideListMenu()" title="Open Menu" alt="An elipsis." src="res/menu.png" id="listmenu" class="imagebutton" />
      <input type="text" size="30" onkeydown="return (event.keyCode!=13);" onkeyup="if (event.keyCode==13 && this.value!='') editList(this);" style="width: 35px; display: none; font-weight: 700; font-size: 20px; float: left;" id="listEdit" />
      <input type="text" size="20" onkeydown="return (event.keyCode!=13);" onkeyup="if (event.keyCode==13 && this.value!='' && block==false) addListUser(this);" style="display: none; float: left; position: relative; top: 3px; left: 10px; margin-right: 10px;" id="adduserbox" />
      <span class="list" style="position: relative; overflow: hidden; float: left; height: 21px;">
      	<span id="listMenuButtons" class="menubuttons" style="position: absolute; left: -96px; z-index: 1; white-space: nowrap; opacity: 100;">
      		<input type="image" style="display: inline; float: left; top: 5px; position: relative;" onclick="listEditClicked();" title="Edit List Name" alt="A pencil." src="res/edt.png" id="editlist" class="imagebutton" />
      		<input type="image" onclick="noListEditClicked();" style="display: none; float: left; position: relative; top: 5px;" title="Cancel Editing List Name" alt="A pencil with a no symbol over it." src="res/noedt.png" id="noeditlist" class="imagebutton hidden" />
      		<input id="deleteList" class="imagebutton" type="image" style="display: inline;" onclick="deleteList();" title="Delete List" alt="A red X." src="res/del.png" />
      		<input id="deletecheckedList" class="imagebutton" type="image" style="display: inline;" onclick="deleteCheckedItems(0);" title="Delete Checked Items In List" alt="A green check mark with a red X over it." src="res/delchk.png" />
      		<input id="addListUser" class="imagebutton" type="image" style="display: inline; top: 5px;" onclick="addUserClicked();" title="Add User To list" alt="" src="res/addusr.png" />
      		<input id="cancelAddListUser" class="imagebutton" type="image" style="display: none; top: 5px;" onclick="cancelUserClicked();" title="Cancel Adding User To list" alt="" src="res/noaddusr.png" />
      	</span>
      </span>
    </div>
    <div id="listored">
      <div style="margin: 1em 0em;">Now loading<img src="res/load.gif" alt="A small spinning black circle." title="Loading" style="margin-left: 10px;" /></div>
    </div>
<script type="text/javascript">
$(document).ready(function () {
	$("#topmenuitems").html('<a href="?logout">Sign Out</a>');
	$("#content").css("min-height", (($(window).height() - $("#floatingmenu").outerHeight() - $("#footer").outerHeight())) + "px");
	getLists();
	getBag();
});
</script>
    <?php endif; ?>
    </div>
    <div id="footer" style="background-color: white; padding-left: 10px; padding-bottom: 10px; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
      <span style="font-weight: bold; font-size: 0.8em;">
        LiSTored (<span id="version"></span>) &copy;2011-2015 Igor Dubinskiy
      </span>
    </div>

  </body>
</html>
