<?php
//include "../../../adodb/adodb.inc.php";
include("../../includes/db.inc.php");
include("../../includes/common.php");

//include("generateCsvObj.php");

$cimsServer = array(
"Name" =>"Downloads",
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
			
			
			if($truncViewArcCustomer2 = mssql_query($qryTruncViewArcCustomer2)){
				if($insertViewArcCustomer2 = mssql_query($qryInsertViewArcCustomer2)){
					if($truncViewArcCustomer = mssql_query($qryTruncViewArcCustomer)){
						if($insertViewArcCustomer = mssql_query($qryInsertViewArcCustomer)){
							echo "$('.divLogs-generateCimLoyPoi-error').append('Message: Save ARC Last success'+'<br/>').css('color','white');";
							return true;
						}
					}
				}
			}else{
				echo "$('.divLogs-generateCimLoyPoi-error').append('Message: Save ARC Last failed'+'<br/>').css('color','red');";
				return true;
			}
		
		}
		
		exit();
	break;
	
	case 'createCustomerPointsForArc':
		
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
			$tranDateMinOne = date("m/d/Y", strtotime("$serverDate  -1 day", time()));
			
			$qryTruncSTG_TBLCUSTOMERPOINTS = "
			truncate table STG_TBLCUSTOMERPOINTS
			";
			
			// Peso Value to Redeem
			$qryInsertSTG_TBLCUSTOMERPOINTS = "
			INSERT INTO STG_TBLCUSTOMERPOINTS (ccardnumber, dtrandate, mpesovaluetoredeem, dPostDate)
			SELECT     cCardNumber, '$tranDateMinOne', CASE WHEN SUM(iPointsNP + iPointsPP - iPointsRedeemed) 
								  < 100 THEN SUM(iPointsNP + iPointsPP - iPointsRedeemed) * .25 WHEN SUM(iPointsNP + iPointsPP - iPointsRedeemed) BETWEEN 100 AND 
								  4999 THEN (SUM(iPointsNP + iPointsPP - iPointsRedeemed) - SUM(iPointsNP + iPointsPP - iPointsRedeemed) % 100) 
								  * .5 + (SUM(iPointsNP + iPointsPP - iPointsRedeemed) % 100) * .25 WHEN SUM(iPointsNP + iPointsPP - iPointsRedeemed) BETWEEN 5000 AND 
								  9999 THEN (SUM(iPointsNP + iPointsPP - iPointsRedeemed) - SUM(iPointsNP + iPointsPP - iPointsRedeemed) % 100) 
								  * .75 + (SUM(iPointsNP + iPointsPP - iPointsRedeemed) % 100) * .25 ELSE (SUM(iPointsNP + iPointsPP - iPointsRedeemed) 
								  - SUM(iPointsNP + iPointsPP - iPointsRedeemed) % 100) + (SUM(iPointsNP + iPointsPP - iPointsRedeemed) % 100) * .25 END AS iPesoValue, '$tranDate'
			FROM         dbo.TBLCustTranLog
			WHERE CONVERT(smalldatetime,CONVERT(char(10),dPostDate,101))<'$tranDate' OR 
						 (CONVERT(smalldatetime,CONVERT(char(10),dPostDate,101))='$tranDate' AND (cRemarks is NULL OR (cRemarks not like 'ADJ%' AND cRemarks not like 'REBATE CERTIFICATE%')))
			GROUP BY cCardNumber
			";
			
			// Peso Value Redeemed
			$qryInsertSTG_TBLCUSTOMERPOINTS2 = "
			INSERT INTO STG_TBLCUSTOMERPOINTS (ccardnumber, dtrandate, mpesovalueredeemed, dPostDate)
			SELECT     cCardNumber, '$tranDateMinOne', CASE WHEN iPointsRedeemed < 100 THEN iPointsRedeemed * .25 WHEN iPointsRedeemed BETWEEN 
								  100 AND 4999 THEN iPointsRedeemed * .5 WHEN iPointsRedeemed BETWEEN 5000 AND 
								  9999 THEN iPointsRedeemed * .75 ELSE iPointsRedeemed END AS iPesoAmt, '$tranDate'
			FROM         dbo.TBLCustTranLog
			WHERE     (cRemarks LIKE N'REBATE CERTIFICATE%') AND CONVERT(smalldatetime,CONVERT(char(10),dPostDate,101))<'$tranDate'
			";
			
			// Points Earned (Daily)
			$qryInsertSTG_TBLCUSTOMERPOINTS3 = "
			INSERT INTO STG_TBLCUSTOMERPOINTS (ccardnumber, dtrandate, ipointsearned, dPostDate)
			SELECT     cCardNumber, '$tranDateMinOne', SUM(ipointsearned) As itotalpointsearned, '$tranDate'
			FROM         dbo.VIEW_TBLCUSTTRANLOG_EARNEDPOINTS
			WHERE     dDatePosted='$tranDate'
			GROUP BY cCardNumber, dTranDate
			";
			
			// Points Adjustment (Daily)
			$qryInsertSTG_TBLCUSTOMERPOINTS4= "
			INSERT INTO STG_TBLCUSTOMERPOINTS (ccardnumber, dtrandate, ipointsearned, dPostDate)
			SELECT     cCardNumber, '$tranDateMinOne', SUM(ipointsearned) As itotalpointsearned, '12/16/2014'
			FROM         dbo.VIEW_TBLCUSTTRANLOG_ADJPOINTS
			WHERE     dDatePosted='$tranDateMinOne'
			GROUP BY cCardNumber, dTranDate
			";
			
			// Points Redeemed (Daily)
			$qryInsertSTG_TBLCUSTOMERPOINTS5 = "
			INSERT INTO STG_TBLCUSTOMERPOINTS (ccardnumber, dtrandate, ipointsredeemed, dPostDate)
			SELECT     cCardNumber, dTranDate, SUM(ipointsredeemed) As itotalpointsredeemed, '$tranDate'
			FROM       dbo.VIEW_TBLCUSTTRANLOG_REDEMPTION
			WHERE     dTranDate='$tranDateMinOne'
			GROUP BY cCardNumber, dTranDate
			";
			
			$qryDeleteSTG_TBLCUSTOMERPOINTS = "
			DELETE FROM STG_TBLCUSTOMERPOINTS WHERE (ipointsearned+ipointsredeemed+mpesovaluetoredeem+mpesovalueredeemed) = 0
			";
			
			$qryUpdateSTG_TBLCUSTOMERPOINTS = "
			UPDATE BI_VIEW_TBLCUSTOMERPOINTS_WITH_CUSTOMERCODE
			SET cCUSTOMERCODE = DECALTERNATENUMBER
			";
			
			$qryDeleteSTG_TBLCUSTOMERPOINTS2 = "
			DELETE FROM STG_TBLCUSTOMERPOINTS WHERE cCUSTOMERCODE IS NULL
			";
			
			
			if($truncSTG_TBLCUSTOMERPOINTS = mssql_query($qryTruncSTG_TBLCUSTOMERPOINTS)){
				if($insertSTG_TBLCUSTOMERPOINTS = mssql_query($qryInsertSTG_TBLCUSTOMERPOINTS)){
					if($insertSTG_TBLCUSTOMERPOINTS2 = mssql_query($qryInsertSTG_TBLCUSTOMERPOINTS2)){
						if($insertSTG_TBLCUSTOMERPOINTS3 = mssql_query($qryInsertSTG_TBLCUSTOMERPOINTS3)){
							if($insertSTG_TBLCUSTOMERPOINTS4 = mssql_query($qryInsertSTG_TBLCUSTOMERPOINTS4)){
								if($insertSTG_TBLCUSTOMERPOINTS5 = mssql_query($qryInsertSTG_TBLCUSTOMERPOINTS5)){
									if($deleteSTG_TBLCUSTOMERPOINTS = mssql_query($qryDeleteSTG_TBLCUSTOMERPOINTS)){
										if($updateSTG_TBLCUSTOMERPOINTS = mssql_query($qryUpdateSTG_TBLCUSTOMERPOINTS)){
											if($deleteSTG_TBLCUSTOMERPOINTS2 = mssql_query($qryDeleteSTG_TBLCUSTOMERPOINTS2)){
												echo "$('.divLogs-generateCimCusFin').append('Message: Create Customer Points for ARC success'+'<br/>').css('color','white');";
												return true;
											}
										}
									}
								}
							}
						}
					}
				}
			}else{
				echo "$('.divLogs-generateCimLoyPoi-error').append('Message: Create Customer Points for ARC failed'+'<br/>').css('color','red');";
				return true;
			}
		
		exit();
	break;
	
	}
	
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
					url: 'storedProc.php',
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
						createCustomerPointsForArc();
					}		
				})	
			}
			
			function createCustomerPointsForArc(){
				$.ajax({
					url: 'storedProc.php',
					type: 'POST',
					data: 'action=createCustomerPointsForArc',
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
							<div class="divLogs-generateStsRebTyp">
							</div>
							<div class="divLogs-generateStsRebTyp-error">
							</div>
							<div class="divLogs-generateStsReaReb">
							</div>
							<div class="divLogs-generateStsReaReb-error">
							</div>
							<div class="divLogs-generateCasLis">
							</div>
							<div class="divLogs-generateCasLis-error">
							</div>
							<div class="divLogs-generateCimLoyPoi">
							</div>
							<div class="divLogs-generateCimLoyPoi-error">
							</div>
							<div class="divLogs-generateCimCusFin">
							</div>
							<div class="divLogs-generateCimCusFin-error">
							</div>
							
						</div>
						
					</div>
					
				</form>	
  
			</div>
			
        </body>
		
</html>