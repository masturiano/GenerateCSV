<?php
//include "../../../adodb/adodb.inc.php";
include("../../includes/db.inc.php");
include("../../includes/common.php");

//include("generateCsvObj.php");

$stsServer = array(
"Name" =>"Downloads",
"Host" =>"192.168.200.226",
"User" =>"sa",
"Pass" =>"",
"Database" =>"STS");

$cashierListServer = array(
"Name" =>"Downloads",
"Host" =>"192.168.200.103",
"User" =>"sa",
"Pass" =>"sa",
"Database" =>"CashierList");

switch($_POST['action']){

	case 'generateStsRebTyp':
	
		$stsServerCon = mssql_connect($stsServer['Host'],$stsServer['User'],$stsServer['Pass']);
		$stsServerDb = mssql_select_db($stsServer['Database']);	
	
		$file_desti = "../../exported_file/"; // File destination
		
		$gmt = time() + (8 * 60 * 60);
		$todayTime = date("His",$gmt);
		$datefileM = date("m",$gmt);
		$datefileD = date("d",$gmt);
		$datefileY = date("Y",$gmt);
		$fileExt = ".CSV";
		
		// File format: REBATES_TYP_DDMMYYYY.csv
		//$filename = "REBATES_TYP_".$datefileD.$datefileM.$datefileY.$fileExt;
		
		if($stsServerCon){
		
			$curServerDate = "
			select GETDATE() as CurrentDateTime
			";
			$assCurServerDate = mssql_fetch_assoc(mssql_query($curServerDate));
			$serverDate = $assCurServerDate['CurrentDateTime'];
			
			$serverCurrentDate = date("dmY", strtotime("$serverDate -1 day", time()));
			$serverCurrentDateMinOne = date("dmY", strtotime("$serverDate -1 day", time()));
			//$dateFromLY = date("Y-m-d", strtotime("$dtFrom -1 year", time()));
			
			$filename = "REBATES_TYP_".$serverCurrentDateMinOne.$fileExt;
			
			$sqlStsRebTyp = "
			select REBATE_TYPE_CODE,REBATE_TYPE_DESC,ACTIVE,REBATE_REASON_CODE,REBATE_REASON_DESC from BI_VIEW_REBATE_TYPE
			";
			$arrStsRebTyp = mssql_query($sqlStsRebTyp);

			if (file_exists($file_desti.$filename)) {
				unlink($file_desti.$filename);
			}
			$strlength = strlen($arrStsRebTyp); //gets the length of our $content string.
			$xcontentx = "";
			if($strlength > 0){
				while($myrow=mssql_fetch_array($arrStsRebTyp)){
				
					$xcontentx .= trim($myrow['REBATE_TYPE_CODE']).",";
					$xcontentx .= trim($myrow['REBATE_TYPE_DESC']).",";
					$xcontentx .= trim($myrow['ACTIVE']).",";
					$xcontentx .= trim($myrow['REBATE_REASON_CODE']).",";
					$xcontentx .= trim($myrow['REBATE_REASON_DESC']);
					$xcontentx .= "\r\n";
				}
				$create = fopen($file_desti.$filename, "x"); //uses fopen to create our file.
				fwrite($create, $xcontentx);
				fclose($create);
				echo "$('.divLogs').append('Message: Rebate Type CSV Created'+'<br/>').css('color','white');";
				//$transFileToFTPMMS = ftp_put($conn1, $filename, $directory.$filename, FTP_BINARY);
			}
			else{
				echo "$('.divLogs').append('Message: Rebate Type CSV Creation Failed'+'<br/>').css('color','red');";
				return false;
			}
		}
		else{
			echo "$('.divLogs').append('Message: Failed to connect to Sts Server'+'<br/>').css('color','red');";
			exit();
		}	
		
		exit();
	break;
	
	
	
	case 'generateStsReaReb':
	
		$stsServerCon = mssql_connect($stsServer['Host'],$stsServer['User'],$stsServer['Pass']);
		$stsServerDb = mssql_select_db($stsServer['Database']);	
	
		$file_desti = "../../exported_file/"; // File destination
		
		$gmt = time() + (8 * 60 * 60);
		$todayTime = date("His",$gmt);
		$datefileM = date("m",$gmt);
		$datefileD = date("d",$gmt);
		$datefileY = date("Y",$gmt);
		$fileExt = ".CSV";
		
		// File format: REBATES_TYP_DDMMYYYY.csv
		//$filename = "REBATES_TYP_".$datefileD.$datefileM.$datefileY.$fileExt;
		
		if($stsServerCon){
		
			$curServerDate = "
			select GETDATE() as CurrentDateTime
			";
			$assCurServerDate = mssql_fetch_assoc(mssql_query($curServerDate));
			$serverDate = $assCurServerDate['CurrentDateTime'];
			
			$serverCurrentDate = date("dmY", strtotime("$serverDate", time()));
			$serverCurrentDateMinOne = date("dmY", strtotime("$serverDate -1 day", time()));
			//$dateFromLY = date("Y-m-d", strtotime("$dtFrom -1 year", time()));
			
			$filename = "REBATES_TYP_".$serverCurrentDate.$fileExt;
			
			//6/24/1987
			$tranDate = date("m/d/Y", strtotime("$serverDate  -1 day", time()));
			
			$sqlExecCreateRebateTransForArc = "
			Exec CreateRebateTransForArc '$tranDate'
			";
			
			if(mssql_query($sqlExecCreateRebateTransForArc)){
			
				$sqlStsReaReb = "
				select
				DATE,STORE_CODE,VENDOR_CODE,DEPARTMENT_CODE,SUBDEPARTMENT_CODE,
				CLASS_CODE,SUBCLASS_CODE,BUYER_CODE,PRODUCT_CODE,REBATE_CODE,
				REALISED_REBATE_VAL
				from BI_VIEW_REALISED_REBATE
				";
				$arrStsReaReb = mssql_query($sqlStsReaReb);

				if (file_exists($file_desti.$filename)) {
					unlink($file_desti.$filename);
				}
				$strlength = strlen($arrStsReaReb); //gets the length of our $content string.
				$xcontentx = "";
				if($strlength > 0){
					while($myrow=mssql_fetch_array($arrStsReaReb)){
					
						$xcontentx .= trim($myrow['DATE']).",";
						$xcontentx .= trim($myrow['STORE_CODE']).",";
						$xcontentx .= trim($myrow['VENDOR_CODE']).",";
						$xcontentx .= trim($myrow['DEPARTMENT_CODE']).",";
						$xcontentx .= trim($myrow['SUBDEPARTMENT_CODE']);
						$xcontentx .= trim($myrow['CLASS_CODE']).",";
						$xcontentx .= trim($myrow['SUBCLASS_CODE']).",";
						$xcontentx .= trim($myrow['BUYER_CODE']).",";
						$xcontentx .= trim($myrow['PRODUCT_CODE']).",";
						$xcontentx .= trim($myrow['REBATE_CODE']);
						$xcontentx .= trim($myrow['REALISED_REBATE_VAL']);
						$xcontentx .= "\r\n";
					}
					$create = fopen($file_desti.$filename, "x"); //uses fopen to create our file.
					fwrite($create, $xcontentx);
					fclose($create);
					echo "$('.divLogs').append('Message: Realised Rebate CSV Created'+'<br/>').css('color','white');";
					//$transFileToFTPMMS = ftp_put($conn1, $filename, $directory.$filename, FTP_BINARY);
				}
				else{
					echo "$('.divLogs').append('Message: Realised Rebate CSV Creation Failed'+'<br/>').css('color','red');";
					return false;
				}
			
			}
			
		}
		else{
			echo "$('.divLogs').append('Message: Failed to connect to Sts Server'+'<br/>').css('color','red');";
			exit();
		}	
	
		exit();
	break;
	
	case 'generateCasLis':
		
		$cashierListServerCon = mssql_connect($cashierListServer['Host'],$cashierListServer['User'],$cashierListServer['Pass']);
		$cashierListServerDb = mssql_select_db($cashierListServer['Database']);	
	
		$file_desti = "../../exported_file/"; // File destination
		
		$gmt = time() + (8 * 60 * 60);
		$todayTime = date("His",$gmt);
		$datefileM = date("m",$gmt);
		$datefileD = date("d",$gmt);
		$datefileY = date("Y",$gmt);
		$fileExt = ".CSV";
		
		// File format: REBATES_TYP_DDMMYYYY.csv
		//$filename = "REBATES_TYP_".$datefileD.$datefileM.$datefileY.$fileExt;
		
		if($cashierListServerCon){
		
			$curServerDate = "
			select GETDATE() as CurrentDateTime
			";
			$assCurServerDate = mssql_fetch_assoc(mssql_query($curServerDate));
			$serverDate = $assCurServerDate['CurrentDateTime'];
			
			$serverCurrentDate = date("dmY", strtotime("$serverDate", time()));
			$serverCurrentDateMinOne = date("dmY", strtotime("$serverDate -1 day", time()));
			//$dateFromLY = date("Y-m-d", strtotime("$dtFrom -1 year", time()));
			
			$filename = "EMPLOYEE_".$serverCurrentDate.$fileExt;
			
			$sqlCasLisEmp = "
			select
			EMPLOYEE_CODE,EMPLOYEE_TYPE,EMPLOYEE_STATUS,FIRST_NAME,LAST_NAME,ACTIVE,TIME_SHIFT_NO,COMMISSION_LVL,MAX_DISC_PERCENT,STORE_CODE
			from VIEW_EMPLOYEE 
			";
			
			$arrSqlCasLisEmp = mssql_query($sqlCasLisEmp);

			if (file_exists($file_desti.$filename)) {
				unlink($file_desti.$filename);
			}
			$strlength = strlen($arrSqlCasLisEmp); //gets the length of our $content string.
			$xcontentx = "";
			if($strlength > 0){
				while($myrow=mssql_fetch_array($arrSqlCasLisEmp)){
				
					$xcontentx .= trim($myrow['EMPLOYEE_CODE']).",";
					$xcontentx .= trim($myrow['EMPLOYEE_TYPE']).",";
					$xcontentx .= trim($myrow['EMPLOYEE_STATUS']).",";
					$xcontentx .= trim($myrow['FIRST_NAME']).",";
					$xcontentx .= trim($myrow['LAST_NAME']);
					$xcontentx .= trim($myrow['ACTIVE']).",";
					$xcontentx .= trim($myrow['TIME_SHIFT_NO']).",";
					$xcontentx .= trim($myrow['COMMISSION_LVL']).",";
					$xcontentx .= trim($myrow['MAX_DISC_PERCENT']).",";
					$xcontentx .= trim($myrow['STORE_CODE']);
					$xcontentx .= "\r\n";
				}
				$create = fopen($file_desti.$filename, "x"); //uses fopen to create our file.
				fwrite($create, $xcontentx);
				fclose($create);
				echo "$('.divLogs').append('Message: Cashier List Employee CSV Created'+'<br/>').css('color','white');";
				//$transFileToFTPMMS = ftp_put($conn1, $filename, $directory.$filename, FTP_BINARY);
			}
			else{
				echo "$('.divLogs').append('Message: Cashier List Employee CSV Creation Failed'+'<br/>').css('color','red');";
				return false;
			}
			
		}
		else{
			echo "$('.divLogs').append('Message: Failed to connect to Cashier List Server'+'<br/>').css('color','red');";
			exit();
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
		
			function generateCsv(){
				$.ajax({
					url: 'generateCsv.php',
					type: 'POST',
					data: 'action=generateStsRebTyp',
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
						generateReaRebCsv();
					}		
				})	
			}
			
			function generateReaRebCsv(){
				$.ajax({
					url: 'generateCsv.php',
					type: 'POST',
					data: 'action=generateStsReaReb',
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
						generateCasLis();
					}		
				})	
			}
			
			function generateCasLis(){
				$.ajax({
					url: 'generateCsv.php',
					type: 'POST',
					data: 'action=generateCasLis',
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
		width: 90%;
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
							<input type="button" name="btnGenerateCsv" class="btn btn-info" onClick="generateCsv();" id="btnGenerateCsv" value="Generate CSV">
						</center>
						
						<br>
						
						<div class="divLogs">
						</div>
						
					</div>
					
				</form>	
  
			</div>
			
        </body>
		
</html>