<?php
//include "../../../adodb/adodb.inc.php";
include("../../includes/db.inc.php");
include("../../includes/common.php");

//include("generateCsvObj.php");

$stsServer = array(
"Name" =>"STS Server",
"Host" =>"192.168.200.226",
"User" =>"sa",
"Pass" =>"",
"Database" =>"STS");

$cashierListServer = array(
"Name" =>"Cashier List Server",
"Host" =>"192.168.200.103",
"User" =>"sa",
"Pass" =>"sa",
"Database" =>"CashierList");

$cimsServer = array(
"Name" =>"CIMS Server",
"Host" =>"192.168.200.231",
"User" =>"sa",
"Pass" =>"sa",
"Database" =>"ExclusivesHO");

$server1 = array(
"Name" =>"192.168.200.200",
"Host" =>"192.168.200.200",
"User" =>"jdwade@puregold.local",
"Pass" =>"info@2013",
"Path" =>"/fromho/Others/POLLOG_CONVERSION_MASTERFILE_UPDATES/"
);

// connect to ftp server1
$conn1 = ftp_connect($server1['Host']);

// open a session to an external ftp site
$login1 = ftp_login ($conn1, $server1['User'], $server1['Pass']);

ftp_pasv($conn1, true);

//$generateCsvObj = new generateCsvObj();

switch($_POST['action']){
	
	case 'generateCardAlt':
		
		$cimsServerCon = mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass']);
		$cimsServerDb = mssql_select_db($cimsServer['Database']);	
		
		$file_desti = "../../exported_file/"; // File destination
		//$file_desti_arc_server = "\\\\192.168.200.133\pg_src_data\\"; // ARC Server
		
		$gmt = time() + (8 * 60 * 60);
		$todayTime = date("His",$gmt);
		$datefileM = date("m",$gmt);
		$datefileD = date("d",$gmt);
		$datefileY = date("Y",$gmt);
		$fileExt = ".TXT";
		
		// File format: REBATES_TYP_DDMMYYYY.csv
		//$filename = "REBATES_TYP_".$datefileD.$datefileM.$datefileY.$fileExt;
		
		if($cimsServerCon){
		
			$curServerDate = "
			select GETDATE() as CurrentDateTime
			";
			$assCurServerDate = mssql_fetch_assoc(mssql_query($curServerDate));
			$serverDate = $assCurServerDate['CurrentDateTime'];
			
			$serverCurrentDate = date("Ymd", strtotime("$serverDate", time()));
			$serverCurrentDateMinOne = date("Ymd", strtotime("$serverDate -1 day", time()));
			//$dateFromLY = date("Y-m-d", strtotime("$dtFrom -1 year", time()));
			
			$filename = "Card_Alt".$serverCurrentDate.$fileExt;
			
			//6/24/1987
			$tranDate = date("m/d/Y", strtotime("$serverDate", time()));
		
			//if(mssql_query($createCustomerPointsForArc)){
			
				$sqlCardAlt = "
				select CHRCardNumber,DECAlternatenumber from VIEW_CARD_ALT 
				";
				$arrSqlCardAlt = mssql_query($sqlCardAlt);

				if (file_exists($file_desti.$filename)) {
					unlink($file_desti.$filename);
				}
				$strlength = strlen($arrSqlCardAlt); //gets the length of our $content string.
				$xcontentx = "";
				if($strlength > 0){
				
					$xcontentx .= trim('"CHRCardNumber","DECAlternatenumber"');
					$xcontentx .= "\r\n";
				
					while($myrow=mssql_fetch_array($arrSqlCardAlt)){
					
						$xcontentx .= '"'.trim($myrow['CHRCardNumber']).'",';
						$xcontentx .= trim($myrow['DECAlternatenumber']);
						$xcontentx .= "\r\n";
					}
					$create = fopen($file_desti.$filename, "x"); //uses fopen to create our file.
					fwrite($create, $xcontentx);
					fclose($create);
					ftp_put($conn1, $server1['Path'].$filename, $file_desti.$filename."", FTP_BINARY);  // upload the file
					echo "$('.divLogs-cardAlt').append('Message: Card Alt TXT Created'+'<br/>').css('color','white');";
					//$transFileToFTPMMS = ftp_put($conn1, $filename, $directory.$filename, FTP_BINARY);
				}
				else{
					echo "$('.divLogs-cardAlt-error').append('Message: Card Alt TXT Creation Failed'+'<br/>').css('color','red');";
					return true;
				}
			
			//}
		}
		else{
			echo "$('.divLogs-cardAlt-error').append('Message: Failed to connect to Cims Server'+'<br/>').css('color','red');";
			exit();
			return true;
		}	
		
		exit();
	break;
	
	case 'generateEjCor':
		
		$cimsServerCon = mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass']);
		$cimsServerDb = mssql_select_db($cimsServer['Database']);	
		
		$file_desti = "../../exported_file/"; // File destination
		//$file_desti_arc_server = "\\\\192.168.200.133\pg_src_data\\"; // ARC Server
		
		$gmt = time() + (8 * 60 * 60);
		$todayTime = date("His",$gmt);
		$datefileM = date("m",$gmt);
		$datefileD = date("d",$gmt);
		$datefileY = date("Y",$gmt);
		$fileExt = ".TXT";
		
		// File format: REBATES_TYP_DDMMYYYY.csv
		//$filename = "REBATES_TYP_".$datefileD.$datefileM.$datefileY.$fileExt;
		
		if($cimsServerCon){
		
			$curServerDate = "
			select GETDATE() as CurrentDateTime
			";
			$assCurServerDate = mssql_fetch_assoc(mssql_query($curServerDate));
			$serverDate = $assCurServerDate['CurrentDateTime'];
			
			$serverCurrentDate = date("Ymd", strtotime("$serverDate", time()));
			$serverCurrentDateMinOne = date("Ymd", strtotime("$serverDate -1 day", time()));
			//$dateFromLY = date("Y-m-d", strtotime("$dtFrom -1 year", time()));
			
			$filename = "EJCORRECTION_AMAX".$serverCurrentDate.$fileExt;
			
			//6/24/1987
			$tranDate = date("m/d/Y", strtotime("$serverDate", time()));
		
			//if(mssql_query($createCustomerPointsForArc)){
			
				$sqlEjCor = "
				select CHRCardNumber,DECAlternatenumber,cMobileNumber from ViewEJCorrection_AMAX
				";
				$arrSqlEjCor = mssql_query($sqlEjCor);

				if (file_exists($file_desti.$filename)) {
					unlink($file_desti.$filename);
				}
				$strlength = strlen($arrSqlEjCor); //gets the length of our $content string.
				$xcontentx = "";
				if($strlength > 0){
				
					$xcontentx .= trim('"CHRCardNumber","DECAlternatenumber","cMobileNumber"');
					$xcontentx .= "\r\n";
				
					while($myrow=mssql_fetch_array($arrSqlEjCor)){
					
						$xcontentx .= '"'.trim($myrow['CHRCardNumber']).'",';
						$xcontentx .= trim($myrow['DECAlternatenumber']).",";
						$xcontentx .= '"'.trim($myrow['cMobileNumber']).'",';
						$xcontentx .= "\r\n";
					}
					$create = fopen($file_desti.$filename, "x"); //uses fopen to create our file.
					fwrite($create, $xcontentx);
					fclose($create);
					ftp_put($conn1, $server1['Path'].$filename, $file_desti.$filename."", FTP_BINARY);  // upload the file
					echo "$('.divLogs-ejCor').append('Message: EJ Correction TXT Created'+'<br/>').css('color','white');";
					//$transFileToFTPMMS = ftp_put($conn1, $filename, $directory.$filename, FTP_BINARY);
				}
				else{
					echo "$('.divLogs-ejCor-error').append('Message: EJ Correction TXT Creation Failed'+'<br/>').css('color','red');";
					return true;
				}
			
			//}
		}
		else{
			echo "$('.divLogs-ejCor-error').append('Message: Failed to connect to Cims Server'+'<br/>').css('color','red');";
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
		
			function generateCsv(){
				$.ajax({
					url: 'pollogConv.php',
					type: 'POST',
					data: 'action=generateCardAlt',
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
						generateEjCor();
					}		
				})	
			}
			
			function generateEjCor(){
				$.ajax({
					url: 'pollogConv.php',
					type: 'POST',
					data: 'action=generateEjCor',
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
							<input type="button" name="btnGenerateCsv" class="btn btn-info" onClick="generateCsv();" id="btnGenerateCsv" value="Generate Txt">
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
							<div class="divLogs-ftpcon">
							<?
							// check open
							if ((!$conn1) || (!$login1)) {
								echo "<font color='red'><b>Connection status:</b> FTP server " . $server1['Name'] . " connect failed.</font><br>";
							}
							else {
								echo "<font color='lightblue'><b>Connection status:</b> FTP server " . $server1['Name'] . " connected.</font><br>";
							}
							?>
							</div>
							<div class="divLogs-cardAlt">
							</div>
							<div class="divLogs-cardAlt-error">
							</div>
							<div class="divLogs-ejCor">
							</div>
							<div class="divLogs-ejCor-error">
							</div>
							
						</div>
						
					</div>
					
				</form>	
  
			</div>
			
        </body>
		
</html>