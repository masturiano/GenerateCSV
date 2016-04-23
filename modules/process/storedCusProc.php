<?php
//include "../../../adodb/adodb.inc.php";
include("../../includes/db.inc.php");
include("../../includes/common.php");

//include("generateCsvObj.php");

$cimsServer = array(
"Name" =>"CIMS Server",
"Host" =>"192.168.200.231",
"User" =>"sa",
"Pass" =>"sa",
"Database" =>"ExclusivesHO");

switch($_POST['action']){

	case 'storedProc':
		
		$cimsServerCon = mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass']);
		$cimsServerDb = mssql_select_db($cimsServer['Database']);	
		
		$gmt = time() + (8 * 60 * 60);
		$todayTime = date("His",$gmt);
		$datefileM = date("m",$gmt);
		$datefileD = date("d",$gmt);
		$datefileY = date("Y",$gmt);
		$fileExt = ".CSV";
		
		if($cimsServerCon){
		
			$curServerDate = "
			select GETDATE() as CurrentDateTime
			";
			$assCurServerDate = mssql_fetch_assoc(mssql_query($curServerDate));
			$serverDate = $assCurServerDate['CurrentDateTime'];
			
			$serverCurrentDate = date("dmY", strtotime("$serverDate", time()));
			$serverCurrentDateMinOne = date("dmY", strtotime("$serverDate -1 day", time()));
		
			//6/24/1987
			$tranDate = date("m/d/Y", strtotime("$serverDate", time()));
			
			
			$qryTruncViewArcCustomer2 = "
			truncate table TBL_VIEW_ARC_Customer2
			";
			
			$qryInsertViewArcCustomer2 = "
			insert into TBL_VIEW_ARC_Customer2
			select * from VIEW_ARC_Customer_FINAL
			";
			
			$qryTruncViewArcCustomer = "
			truncate table TBL_VIEW_ARC_Customer
			";
			
			$qryInsertViewArcCustomer = "
			insert into TBL_VIEW_ARC_Customer
			select * from TBL_VIEW_ARC_Customer2
			";
			
			/*
			if(mssql_query($qryTruncViewArcCustomer2)){
				echo "$('.divLogs-generateCimLoyPoi-error').append('Message: truncate table TBL_VIEW_ARC_Customer2 done'+'<br/>').css('color','white');";
				if(mssql_query($qryInsertViewArcCustomer2)){
					echo "$('.divLogs-generateCimLoyPoi-error').append('Message: insert into TBL_VIEW_ARC_Customer2 done'+'<br/>').css('color','white');";
					if(mssql_query($qryTruncViewArcCustomer)){
						echo "$('.divLogs-generateCimLoyPoi-error').append('Message: truncate table TBL_VIEW_ARC_Customer done'+'<br/>').css('color','white');";
						if(mssql_query($qryInsertViewArcCustomer)){
							echo "$('.divLogs-generateCimLoyPoi-error').append('Message: insert into TBL_VIEW_ARC_Customer done'+'<br/>').css('color','white');";
							return true;
						}
					}
				}
			}else{
				echo "$('.divLogs-generateCimLoyPoi-error').append('Message: Save ARC Last failed'+'<br/>').css('color','red');";
				return true;
			}
			*/
			
			if($truncViewArcCustomer2 = mssql_query($qryTruncViewArcCustomer2)){
				echo "$('.divLogs-saveArcLastCsv').append('Message: truncate table TBL_VIEW_ARC_Customer2 done'+'<br/>').css('color','white');";
				if($insertViewArcCustomer2 = mssql_query($qryInsertViewArcCustomer2)){
					echo "$('.divLogs-saveArcLastCsv').append('Message: insert into TBL_VIEW_ARC_Customer2 done'+'<br/>').css('color','white');";
					if($truncViewArcCustomer = mssql_query($qryTruncViewArcCustomer)){
						echo "$('.divLogs-saveArcLastCsv').append('Message: truncate table TBL_VIEW_ARC_Customer done'+'<br/>').css('color','white');";
						if($insertViewArcCustomer = mssql_query($qryInsertViewArcCustomer)){
							echo "$('.divLogs-saveArcLastCsv').append('Message: insert into TBL_VIEW_ARC_Customer done'+'<br/>').css('color','white');";
							echo "$('.divLogs-saveArcLastCsv').append('Message: Save ARC Last CSV sucess'+'<br/>').css('color','white');";
							return true;
						}
					}
				}
			}else{
				echo "$('.divLogs-saveArcLastCsv-error').append('Message: Save ARC Last failed'+'<br/>').css('color','red');";
				return true;
			}
			
		
		}
		
		exit();
	break;
	
}
?>
		
<html>

	<head>
		
		<script src="../../includes/jquery/js/jquery-1.6.2.min.js"></script>
        <script src="../../includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script src="../../includes/bootbox/bootbox.js"></script>
        
        <script src="../../includes/toastmessage/src/main/javascript/jquery.toastmessage.js"></script>
        <link rel="stylesheet" type="text/css" href="../../includes/toastmessage/src/main/resources/css/jquery.toastmessage.css" />
        
        <link href="../../includes/showLoading/css/showLoading.css" rel="stylesheet" media="screen" /> 
        <script type="text/javascript" src="../../includes/showLoading/js/jquery.showLoading.js"></script>
		
		<script type="text/javascript">
		
			function storedProc(){
				$.ajax({
					url: 'storedCusProc.php',
					type: 'POST',
					data: 'action=storedProc',
					beforeSend: function() {
						$('#btnGenerateCsv').val('Running...');
					},
					success: function(data){
						$('#btnGenerateCsv').attr('disabled','disabled');
						$(".btn:hover").css({
							"text-decoration": "none",
							"background": "#3498db"
						});
						eval(data);
						$('#btnGenerateCsv').val('Done!');      
					}		
				})	
			}
		
		</script>
	
		<style type="text/css">
		<!--

		.btn {
		background: #3498db;
		background-image: -webkit-linear-gradient(top, #3498db, #2980b9);
		background-image: -moz-linear-gradient(top, #3498db, #2980b9);
		background-image: -ms-linear-gradient(top, #3498db, #2980b9);
		background-image: -o-linear-gradient(top, #3498db, #2980b9);
		background-image: linear-gradient(to bottom, #3498db, #2980b9);
		-webkit-border-radius: 60;
		-moz-border-radius: 60;
		border-radius: 60px;
		font-family: Courier New;
		color: #ffffff;
		font-size: 38px;
		padding: 10px 20px 10px 20px;
		text-decoration: none;
		}

		.btn:hover {
		background: #3cb0fd;
		text-decoration: none;
		}
		
		.divLogs{
		height: 50%;
		width: 70%;
		overflow:auto;
		border: 1px solid #ddd;
		border-radius: 10px;
		background-color:black;
		margin-left: auto;
		margin-right: auto;
		}
		
		#dispLogs{
		color: white;
		font-size: 14px;
		}
		-->
		</style>   

    </head>
	
    	<body>
		
			<div id="activity_pane">
			
			<br />
            
				<form class="form-horizontal">
				
					<div class="dvContainer">
						<center>
							<input type="button" name="btnGenerateCsv" class="btn btn-info" onClick="storedProc();" id="btnGenerateCsv" value="Stored Procedure">
						</center>
						
						<br>
						
						<div class="divLogs">
							<div class="divLogs-cims">
							<?
							// check open
							if (!mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass'])) {
								echo "<font color='red'><b>Connection status:</b>" .$cimsServer ['Name'] . " connect failed.</font><br>";
							}
							else {
								echo "<font color='lightblue'><b>Connection status:</b> " . $cimsServer['Name'] . " connected.</font><br>";
							}
							?>
							</div>
							<div class="divLogs-saveArcLastCsv">
							</div>
							<div class="divLogs-saveArcLastCsv-error">
							</div>
							<div class="divLogs-CreateCustomerPointsForArc">
							</div>
							<div class="divLogs-CreateCustomerPointsForArc-error">
							</div>
							
						</div>
						
					</div>
					
				</form>	
  
			</div>
			
        </body>
		
</html>