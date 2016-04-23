<?php
//include "../../../adodb/adodb.inc.php";
include("../../includes/db.inc.php");
include("../../includes/common.php");

//include("generateCsvObj.php");

$stsServer = array(
"Name" =>"STS Server",
"Host" =>"192.168.200.228",
"User" =>"wil",
"Pass" =>"pw@123456",
"Database" =>"pg_sts");

/*
$stsServer = array(
"Name" =>"STS Server",
"Host" =>"192.168.200.174",
"User" =>"sa",
"Pass" =>"pw@123",
"Database" =>"pg_sts");
*/

$cashierListServer = array(
"Name" =>"Cashier List Server",
"Host" =>"192.168.200.232",
"User" =>"sa",
"Pass" =>"sa",
"Database" =>"CashierList");

$cimsServer = array(
"Name" =>"CIMS Server",
"Host" =>"192.168.200.231",
"User" =>"sa",
"Pass" =>"sa",
"Database" =>"ExclusivesHO");

//$generateCsvObj = new generateCsvObj();

switch($_POST['action']){

	case 'generateCimCusFin':
		
		$cimsServerCon = mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass']);
		$cimsServerDb = mssql_select_db($cimsServer['Database']);	
	
		$file_desti = "../../exported_file/"; // File destination
		$file_desti_arc_server = "\\\\192.168.200.133\pg_src_data\\"; // ARC Server
		
		$gmt = time() + (8 * 60 * 60);
		$todayTime = date("His",$gmt);
		$datefileM = date("m",$gmt);
		$datefileD = date("d",$gmt);
		$datefileY = date("Y",$gmt);
		$fileExt = ".CSV";
		
		// File format: REBATES_TYP_DDMMYYYY.csv
		//$filename = "REBATES_TYP_".$datefileD.$datefileM.$datefileY.$fileExt;
		
		if($cimsServerCon){
		
			$curServerDate = "
			select GETDATE() as CurrentDateTime
			";
			$assCurServerDate = mssql_fetch_assoc(mssql_query($curServerDate));
			$serverDate = $assCurServerDate['CurrentDateTime'];
			
			$serverCurrentDate = date("dmY", strtotime("$serverDate", time()));
			$serverCurrentDateMinOne = date("dmY", strtotime("$serverDate -1 day", time()));
			//$dateFromLY = date("Y-m-d", strtotime("$dtFrom -1 year", time()));
			
			$filename = "CUSTOMER_".$serverCurrentDateMinOne.$fileExt;
			
			//6/24/1987
			$tranDate = date("m/d/Y", strtotime("$serverDate  -1 day", time()));
			
			$sqlExecSaveARCLastCSV = "
			Exec SaveARCLastCSV
			";
			
			//if(mssql_query($sqlExecSaveARCLastCSV)){
			
				$sqlCimCusFin = "
				select
				CUSTOMER_CODE, COMPANY_NAME, CUSTOMER_GROUP, 
				GENDER_CODE, HOUSE_HOLD, INCOME, 
				MARITAL_STATUS, LOYALTY_STATUS, BIRTH_DATE, 
				LOYALTY_CARD_NO, LOYALTY_CARD_ISSUE_DATE, FIRST_NAME, 
				LAST_NAME, ADDRESS1, ADDRESS2, LOCATION1, 
				LOCATION2, LOCATION3, PHONE1, PHONE2, 
				ACCEPT_CHEQUES, CARD_NO1, CARD_NO2, 
				CARD1_ISSUE_DATE, CARD1_EXPIRE_DATE, CARD2_ISSUE_DATE, 
				CARD2_EXPIRY_DATE, ACTIVE, 
				ARC_DATE
				FROM  VIEW_ARC_Customer_FINAL
				";
				$arrSqlCimCusFin = mssql_query($sqlCimCusFin);

				if (file_exists($file_desti.$filename)) {
					unlink($file_desti.$filename);
				}
				$strlength = strlen($arrSqlCimCusFin); //gets the length of our $content string.
				$xcontentx = "";
				if($strlength > 0){
				
					$xcontentx .= trim("CUSTOMER_CODE,COMPANY_NAME,CUSTOMER_GROUP,GENDER_CODE,HOUSE_HOLD,INCOME,MARITAL_STATUS,LOYALTY_STATUS,BIRTH_DATE,LOYALTY_CARD_NO, LOYALTY_CARD_ISSUE_DATE, FIRST_NAME,LAST_NAME, ADDRESS1, ADDRESS2, LOCATION1,LOCATION2, LOCATION3, PHONE1, PHONE2,ACCEPT_CHEQUES, CARD_NO1, CARD_NO2,CARD1_ISSUE_DATE, CARD1_EXPIRE_DATE, CARD2_ISSUE_DATE,CARD2_EXPIRY_DATE, ACTIVE,ARC_DATE");
					$xcontentx .= "\r\n";
				
					while($myrow=mssql_fetch_array($arrSqlCimCusFin)){
					
						$xcontentx .= trim($myrow['CUSTOMER_CODE']).','; #1
						$xcontentx .= '"'.trim(str_replace('"','""',$myrow['COMPANY_NAME'])).'",'; #2
						$xcontentx .= '"'.trim($myrow['CUSTOMER_GROUP']).'",'; #3
						$xcontentx .= trim($myrow['GENDER_CODE']).","; #4
						$xcontentx .= '"'.trim($myrow['HOUSE_HOLD']).'",'; #5
						$xcontentx .= trim($myrow['INCOME']).","; #6
						$xcontentx .= '"'.trim($myrow['MARITAL_STATUS']).'",'; #7
						$xcontentx .= '"'.trim($myrow['LOYALTY_STATUS']).'",'; #8
						$xcontentx .= trim(date("Y-m-d h:i:s",strtotime($myrow['BIRTH_DATE']))).","; #9
						$xcontentx .= '"'.trim($myrow['LOYALTY_CARD_NO']).'",'; #10
						$xcontentx .= trim(date("Y-m-d h:i:s",strtotime($myrow['LOYALTY_CARD_ISSUE_DATE']))).","; #11
						$xcontentx .= '"'.trim(str_replace('"','""',$myrow['FIRST_NAME'])).'",'; #12
						$xcontentx .= '"'.trim(str_replace('"','""',$myrow['LAST_NAME'])).'",'; #13
						$xcontentx .= '"'.trim(str_replace('"','""',$myrow['ADDRESS1'])).'",'; #14
						$xcontentx .= '"'.trim(str_replace('"','""',$myrow['ADDRESS2'])).'",'; #15
						$xcontentx .= '"'.trim(str_replace('"','""',$myrow['LOCATION1'])).'",'; #16
						$xcontentx .= '"'.trim(str_replace('"','""',$myrow['LOCATION2'])).'",'; #17
						$xcontentx .= '"'.trim(str_replace('"','""',$myrow['LOCATION3'])).'",'; #18
						$xcontentx .= '"'.trim($myrow['PHONE1']).'",'; #19
						$xcontentx .= '"'.trim($myrow['PHONE2']).'",'; #20
						$xcontentx .= '"'.trim($myrow['ACCEPT_CHEQUES']).'",'; #21
						$xcontentx .= '"'.trim($myrow['CARD_NO1']).'",'; #22
						$xcontentx .= '"'.trim($myrow['CARD_NO2']).'",'; #23
						$xcontentx .= '"'.trim(date("m/d/Y",strtotime($myrow['CARD1_ISSUE_DATE']))).'",'; #24
						$xcontentx .= '"'.trim(date("m/d/Y",strtotime($myrow['CARD1_EXPIRE_DATE']))).'",'; #25
						$xcontentx .= '"'.trim(date("m/d/Y",strtotime($myrow['CARD2_ISSUE_DATE']))).'",'; #26
						$xcontentx .= trim(date("Y-m-d h:i:s",strtotime($myrow['CARD2_EXPIRY_DATE']))).","; #27
						$xcontentx .= trim($myrow['ACTIVE']).","; #28
						$xcontentx .= trim(date("Y-m-d h:i:s",strtotime($myrow['ARC_DATE']))); #29
						$xcontentx .= "\r\n";
					}
					$create = fopen($file_desti.$filename, "x"); //uses fopen to create our file.
					fwrite($create, $xcontentx);
					fclose($create);
					
					if (file_exists($file_desti_arc_server.$filename)) {
					echo "$('.divLogs-generateCimCusFin-exist').append('Message: Customer Final CSV Exist at ARC(192.168.200.133) server'+'<br/>').css('color','red');";
					}else{
						$create2 = fopen($file_desti_arc_server.$filename, "x"); //uses fopen to create our file.
						fwrite($create2, $xcontentx);
						fclose($create2);
					}
					echo "$('.divLogs-generateCimCusFin').append('Message: Customer Final CSV Created'+'<br/>').css('color','white');";
					//$transFileToFTPMMS = ftp_put($conn1, $filename, $directory.$filename, FTP_BINARY);
				}
				else{
					echo "$('.divLogs-generateCimCusFin-error').append('Message: Customer Final CSV Creation Failed'+'<br/>').css('color','red');";
					return true;
				}
			
			//}
			
		}
		else{
			echo "$('.divLogs-generateCimCusFin-error').append('Message: Failed to connect to Cims Server'+'<br/>').css('color','red');";
			exit();
			return true;
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
		
		    generateCimCusFin(){
				$.ajax({
					url: 'generateCusCsv.php',
					type: 'POST',
					data: 'action=generateCimCusFin',
					beforeSend: function() {
						$('#btnGenerateCsv').val('Running...');
					},
					success: function(data){
						$('#btnGenerateCsv').attr('disabled','disabled');
						$(".btn:hover").css({
							"text-decoration": "none",
							"background": "#3498db"
						});
						eval(data)
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
							<input type="button" name="btnGenerateCsv" class="btn btn-info" onClick="generateCimCusFin();" id="btnGenerateCsv" value="Generate CSV">
						</center>
						
						<br>
						
						<div class="divLogs">
							<div class="divLogs-stsServer">
							<?
							// check open
							if (!mssql_connect($stsServer['Host'],$stsServer['User'],$stsServer['Pass'])) {
								echo "<font color='red'><b>Connection status:</b>" .$stsServer ['Name'] . " connect failed.</font><br>";
							}
							else {
								echo "<font color='lightblue'><b>Connection status:</b> " . $stsServer['Name'] . " connected.</font><br>";
							}
							?>
							</div>
							<div class="divLogs-cashierListServer">
							<?
							// check open
							if (!mssql_connect($cashierListServer['Host'],$cashierListServer['User'],$cashierListServer['Pass'])) {
								echo "<font color='red'><b>Connection status:</b>" .$cashierListServer ['Name'] . " connect failed.</font><br>";
							}
							else {
								echo "<font color='lightblue'><b>Connection status:</b> " . $cashierListServer['Name'] . " connected.</font><br>";
							}
							?>
							</div>
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
							<div class="divLogs-generateStsRebTyp">
							</div>
							<div class="divLogs-generateStsRebTyp-error">
							</div>
							<div class="divLogs-generateStsRebTyp-exist">
							</div>
							
							<div class="divLogs-generateStsReaReb">
							</div>
							<div class="divLogs-generateStsReaReb-error">
							</div>
							<div class="divLogs-generateStsReaReb-exist">
							</div>
							
							<div class="divLogs-generateCasLis">
							</div>
							<div class="divLogs-generateCasLis-error">
							</div>
							<div class="divLogs-generateCasLis-exist">
							</div>
							
							<div class="divLogs-generateCimLoyPoi">
							</div>
							<div class="divLogs-generateCimLoyPoi-error">
							</div>
							<div class="divLogs-generateCimLoyPoi-exist">
							</div>
							
							<div class="divLogs-generateCimCusFin">
							</div>
							<div class="divLogs-generateCimCusFin-error">
							</div>
							<div class="divLogs-generateCimCusFin-exist">
							</div>
							
						</div>
						
					</div>
					
				</form>	
  
			</div>
			
        </body>
		
</html>