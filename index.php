<?php
//Initialize session
session_start();

include("includes/db.inc.php");
include("includes/common.php");
include("loginObj.php");

$loginObj = new loginObj();

switch($_POST['action']){
	case "btnLogin":
		if(empty($_POST['userName'])) { 
            echo "alert('Please fill in username!')";
			return false;
        }else if(empty($_POST['passWord'])){
			echo "alert('Please fill in password!')";
			return false;
		}else{
			$userNotExist = $loginObj->getRecCount($loginObj->loginNotExist($_POST['userName'],$_POST['passWord']));
			if($userNotExist==0){
				echo "alert('Invalid User Access!')";
			}else{
			$userAcess = $loginObj->login($_POST['userName'],$_POST['passWord']);
				echo '$("#login").val("Redirecting..");';
				echo "window.location.href = 'main.php'";
				$_SESSION['userName'] = $userAcess['userName'];
				$_SESSION['passWord'] = $userAcess['userPass'];
			}
		}
		exit();
	break;
}
?>

<html>
	<head>
    
    	<!-- jQuery, Bootstrap -->
    	<link rel="stylesheet" href="includes/bootstrap/css/bootstrap.css"/>
        <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="includes/bootstrap/css/bootstrap-responsive.css"/>
        <link rel="stylesheet" href="includes/bootstrap/css/bootstrap-responsive.min.css"/>
        <!-- jQuery, Bootstrap -->
        
        <script src="includes/jquery/js/jquery-1.6.2.min.js"></script>
        <script src="includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script src="includes/bootbox/bootbox.js"></script>
   
        <style type="text/css">
		<!--
        body { 
			background:url(includes/images/bgGenerateCSV.png) no-repeat center center fixed; 
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
			top:0px;
		}
		.dvLogin{
			top:300px;
			width:100%;
			height:230px;
			border-color:#000;
			border-style:solid;
			border-width:0px;
			position:relative;
			background-color: rgba(100, 100, 0, 0.2);
			filter: progid:DXImageTransform.Microsoft.Gradient(GradientType=0, StartColorStr='#7F00FF00', EndColorStr='#7F00FF00');
		}
		.puregoldLogo{
			margin-right: auto;
			height:20px;
			width:150px;
			background-color:transparent;

		}
		.creditCard{
			height:20px;
			width:60px;
			background-color:transparent;
		}
		.dvContainer{
			margin-left:25%;
			margin-right: auto;
		}
		
		input,
		textarea,
		select {
			padding: 3px;
			font: 900 1em Verdana, Sans-serif;
			font-size:11px;
			color: #333;
			background:#eee;
			border: 1px solid #ccc;
			margin:0 0 0px 0;
			width:700px%;
		}
		input:focus,
		textarea:focus,
		select:focus {
			background: #fff;
			border: 1px solid #999;
		}
		
		input,
		textarea {
		  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
		  -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
		  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
		  -webkit-transition: border linear 0.2s, box-shadow linear 0.2s;
		  -moz-transition: border linear 0.2s, box-shadow linear 0.2s;
		  -ms-transition: border linear 0.2s, box-shadow linear 0.2s;
		  -o-transition: border linear 0.2s, box-shadow linear 0.2s;
		  transition: border linear 0.2s, box-shadow linear 0.2s;
		}
		input:focus,
		textarea:focus {
		  border-color: rgba(82, 168, 236, 0.8);
		  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
		  -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
		  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
		  outline: 0;
		  outline: thin dotted \9;
		  /* IE6-9 */
		}
		-->	
		</style>   
        
        <script type="text/javascript">
			function btnLogin(){
				$.ajax({
					url: 'index.php',
					type: 'POST',
					data: 'action=btnLogin&'+$("#frmLogin").serialize(),
					success: function(data){
						eval(data);
					}	
				});
			}
		</script> 

    </head>
	 	<body>

			<div class="dvLogin">
                    <fieldset>
                        <div id="legend">
                        <legend class="">&nbsp;<b><font color="#00CCFF">Login</font></b>
                        </legend>
                        </div>
                        
                        <div class="topHeader">
                            <!-- <img src="includes/images/PUREGOLD.png" class="puregoldLogo"> -->
                            <!-- <img src="includes/images/creditCard.png" class="creditCard"> -->
                            <!-- <i>Generate CSV</i> -->
                        </div>
                        
                        <br>
                    
                 <form class="form-horizontal" id="frmLogin" name="frmLogin">
                        <div class="dvContainer">
                            <div class="control-group">
                                <!-- Username -->
                                <label class="control-label" for="username"><b><font color="#00CCFF">Username :</font></b></label>
                                <div class="controls">
                                    <div class="input-prepend"><span class="add-on"><i class="icon-user"></i></span>
                                    <input type="text" id="userName" name="userName" style="height:30px;" class="input-xlarge"></div>
                                    
                            </div>
                            
                            <div class="control-group">
                                <!-- Password -->
                                <label class="control-label" for="password"><b><font color="#00CCFF">Password :</font></b></label>
                                <div class="controls">
                                    <div class="input-prepend"><span class="add-on"><i class="icon-lock"></i></span>
                                    <input type="password" id="passWord" name="passWord" style="height:30px;" class="input-xlarge"></div>
                            </div>
                     
                     		<br />
                            
                            <div class="control-group">
                            	<!-- Button -->
                                <div class="controls">
                                	<input type="button" name="login" class="btn btn-info" onClick="btnLogin();" value="LOGIN">
                                </div>
                            </div>
                         </div>
                    </fieldset>
                </form>	
        	</div>
  
        </body>
</html>

<script language="javascript">
history.forward();
</script>