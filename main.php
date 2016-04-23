<?php
//initialize session
session_start();

include("includes/db.inc.php");
include("includes/common.php");
include("loginObj.php");

$loginObj = new loginObj();

$userName = $_SESSION['userName'];
$passWord = $_SESSION['passWord'];
$userAcess = $loginObj->login($userName,$passWord);
$userNotExist = $loginObj->getRecCount($loginObj->loginNotExist($userName,$passWord));

//Restriction
if($userName == '' && $passWord == ''){
header('Location: index.php');
}

?>
<!DOCTYPE html>
<html>
	<head>
	
		<meta charset="utf-8" />
		<meta name="author" content="www.frebsite.nl" />
		<meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0 user-scalable=yes" />

		<title>Generate CSV</title>

		<link type="text/css" rel="stylesheet" href="includes/slidingmenu/demo/css/demo.css" />
		<link type="text/css" rel="stylesheet" href="includes/slidingmenu/src/css/jquery.mmenu.all.css" />
		
		<script src="includes/jquery/js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="includes/slidingmenu/src/js/jquery.mmenu.min.all.js"></script>
		
		<!-- jQuery, BGIFrame -->
        <script type="text/javascript" src="includes/external/jquery.bgiframe-2.1.1.js"></script>
        <script language="javascript">
        function menu(url)	{
        $("#bodyFrame").attr('src',url);
        }
		</script>
        <!-- jQuery, BGIFrame -->
		
		<script type="text/javascript">
			$(function() {
				$('nav#menu').mmenu();
			});
		</script>
		
		<style type="text/css">
		<!--
		body { 
			background:url(includes/images/bgGenerateCSV2.png) no-repeat center center fixed; 
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
			top:0px;
		}
		
		body, html {
			margin: 0;
			padding: 0;
		}
		
		.bodyFrame{
			height: 1000px;
			width:99.7%;
		}
		-->	
		</style>
	</head>
	<body>
		<div id="page">
			<div class="header">
				<a href="#menu"></a>
				Generate CSV
				
			</div>
			
			<div class="dvContainer">
				<div class="dvBody" id="dvBody">
						<iframe id="bodyFrame" class="bodyFrame"></iframe>
                </div>
			</div>
			
			<nav id="menu">
					<?php
						include("sidebar.php");
					?>
			</nav>
		</div>
	</body>
</html>