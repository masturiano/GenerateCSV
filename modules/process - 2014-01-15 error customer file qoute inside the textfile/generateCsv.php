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

	case 'generateStsRebTyp':
	
		$stsServerCon = mssql_connect($stsServer['Host'],$stsServer['User'],$stsServer['Pass']);
		$stsServerDb = mssql_select_db($stsServer['Database']);	
	
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
				
				$xcontentx .= trim("REBATE_TYPE_CODE,REBATE_TYPE_DESC,ACTIVE,REBATE_REASON_CODE,REBATE_REASON_DESC");
				$xcontentx .= "\r\n";
				
				while($myrow=mssql_fetch_array($arrStsRebTyp)){
					
					$xcontentx .= trim($myrow['REBATE_TYPE_CODE']).",";
					$xcontentx .= '"'.trim($myrow['REBATE_TYPE_DESC']).'",';
					$xcontentx .= trim($myrow['ACTIVE']).",";
					$xcontentx .= trim($myrow['REBATE_REASON_CODE']).",";
					$xcontentx .= '"'.trim($myrow['REBATE_REASON_DESC']).'"';
					$xcontentx .= "\r\n";
				}
				$create = fopen($file_desti.$filename, "x"); //uses fopen to create our file.
				fwrite($create, $xcontentx);
				fclose($create);
				
				if (file_exists($file_desti_arc_server.$filename)) {
					echo "$('.divLogs-generateStsRebTyp-exist').append('Message: Rebate Type CSV Exist at ARC(192.168.200.133) server'+'<br/>').css('color','red');";
				}else{
					$create2 = fopen($file_desti_arc_server.$filename, "x"); //uses fopen to create our file.
					fwrite($create2, $xcontentx);
					fclose($create2);
				}
				echo "$('.divLogs-generateStsRebTyp').append('Message: Rebate Type CSV Created'+'<br/>').css('color','white');";
				//$transFileToFTPMMS = ftp_put($conn1, $filename, $directory.$filename, FTP_BINARY);
			}
			else{
				echo "$('.divLogs-generateStsRebTyp-error').append('Message: Rebate Type CSV Creation Failed'+'<br/>').css('color','red');";
				return true;
			}
		}
		else{
			echo "$('.divLogs-generateStsRebTyp-error').append('Message: Failed to connect to Sts Server'+'<br/>').css('color','red');";
			exit();
			return true;
		}	
		
		exit();
	break;
	
	
	
	case 'generateStsReaReb':
	
		$stsServerCon = mssql_connect($stsServer['Host'],$stsServer['User'],$stsServer['Pass']);
		$stsServerDb = mssql_select_db($stsServer['Database']);	
	
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
		
		if($stsServerCon){
		
			$curServerDate = "
			select GETDATE() as CurrentDateTime
			";
			$assCurServerDate = mssql_fetch_assoc(mssql_query($curServerDate));
			$serverDate = $assCurServerDate['CurrentDateTime'];
			
			$serverCurrentDate = date("dmY", strtotime("$serverDate", time()));
			$serverCurrentDateMinOne = date("dmY", strtotime("$serverDate -1 day", time()));
			//$dateFromLY = date("Y-m-d", strtotime("$dtFrom -1 year", time()));
			
			$filename = "REBATES_REA_".$serverCurrentDateMinOne.$fileExt;
			
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
				
					$xcontentx .= trim("DATE,STORE_CODE,VENDOR_CODE,DEPARTMENT_CODE,SUBDEPARTMENT_CODE,CLASS_CODE,SUBCLASS_CODE,BUYER_CODE,PRODUCT_CODE,REBATE_CODE,REALISED_REBATE_VAL");
					$xcontentx .= "\r\n";
				
					while($myrow=mssql_fetch_array($arrStsReaReb)){
					
						$xcontentx .= trim(date("Y-m-d h:i:s",strtotime($myrow['DATE']))).",";
						$xcontentx .= trim($myrow['STORE_CODE']).",";
						$xcontentx .= trim($myrow['VENDOR_CODE']).",";
						$xcontentx .= trim($myrow['DEPARTMENT_CODE']).",";
						$xcontentx .= trim($myrow['SUBDEPARTMENT_CODE']).",";
						$xcontentx .= trim($myrow['CLASS_CODE']).",";
						$xcontentx .= trim($myrow['SUBCLASS_CODE']).",";
						$xcontentx .= trim($myrow['BUYER_CODE']).",";
						$xcontentx .= trim($myrow['PRODUCT_CODE']).",";
						$xcontentx .= trim($myrow['REBATE_CODE']).",";
						$xcontentx .= trim($myrow['REALISED_REBATE_VAL']);
						$xcontentx .= "\r\n";
					}
					$create = fopen($file_desti.$filename, "x"); //uses fopen to create our file.
					fwrite($create, $xcontentx);
					fclose($create);
					
					if (file_exists($file_desti_arc_server.$filename)) {
					echo "$('.divLogs-generateStsReaReb-exist').append('Message: Realised Rebate CSV Exist at ARC(192.168.200.133) server'+'<br/>').css('color','red');";
					}else{
						$create2 = fopen($file_desti_arc_server.$filename, "x"); //uses fopen to create our file.
						fwrite($create2, $xcontentx);
						fclose($create2);
					}
					echo "$('.divLogs-generateStsReaReb').append('Message: Realised Rebate CSV Created'+'<br/>').css('color','white');";
					//$transFileToFTPMMS = ftp_put($conn1, $filename, $directory.$filename, FTP_BINARY);
				}
				else{
					echo "$('.divLogs-generateStsReaReb-error').append('Message: Realised Rebate CSV Creation Failed'+'<br/>').css('color','red');";
					return true;
				}
			
			}
			
		}
		else{
			echo "$('.divLogs-generateStsReaReb-error').append('Message: Failed to connect to Sts Server'+'<br/>').css('color','red');";
			exit();
			return true;
		}	
	
		exit();
	break;
	
	case 'generateCasLis':
		
		$cashierListServerCon = mssql_connect($cashierListServer['Host'],$cashierListServer['User'],$cashierListServer['Pass']);
		$cashierListServerDb = mssql_select_db($cashierListServer['Database']);	
	
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
		
		if($cashierListServerCon){
		
			$curServerDate = "
			select GETDATE() as CurrentDateTime
			";
			$assCurServerDate = mssql_fetch_assoc(mssql_query($curServerDate));
			$serverDate = $assCurServerDate['CurrentDateTime'];
			
			$serverCurrentDate = date("dmY", strtotime("$serverDate", time()));
			$serverCurrentDateMinOne = date("dmY", strtotime("$serverDate -1 day", time()));
			//$dateFromLY = date("Y-m-d", strtotime("$dtFrom -1 year", time()));
			
			$filename = "EMPLOYEE_".$serverCurrentDateMinOne.$fileExt;
			
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
			
					$xcontentx .= trim("EMPLOYEE_CODE,EMPLOYEE_TYPE,EMPLOYEE_STATUS,FIRST_NAME,LAST_NAME,ACTIVE,TIME_SHIFT_NO,COMMISSION_LVL,MAX_DISC_PERCENT,STORE_CODE");
					$xcontentx .= "\r\n";
					
				while($myrow=mssql_fetch_array($arrSqlCasLisEmp)){
				
					$xcontentx .= trim($myrow['EMPLOYEE_CODE']).",";
					$xcontentx .= trim($myrow['EMPLOYEE_TYPE']).",";
					$xcontentx .= trim($myrow['EMPLOYEE_STATUS']).",";
					$xcontentx .= '"'.trim($myrow['FIRST_NAME']).'",';
					$xcontentx .= '"'.trim($myrow['LAST_NAME']).'",';
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
				
				if (file_exists($file_desti_arc_server.$filename)) {
				echo "$('.divLogs-generateCasLis-exist').append('Message: Cashier List Employee CSV Exist at ARC(192.168.200.133) server'+'<br/>').css('color','red');";
				}else{
					$create2 = fopen($file_desti_arc_server.$filename, "x"); //uses fopen to create our file.
					fwrite($create2, $xcontentx);
					fclose($create2);
				}
				echo "$('.divLogs-generateCasLis').append('Message: Cashier List Employee CSV Created'+'<br/>').css('color','white');";
				//$transFileToFTPMMS = ftp_put($conn1, $filename, $directory.$filename, FTP_BINARY);
			}
			else{
				echo "$('.divLogs-generateCasLis-error').append('Message: Cashier List Employee CSV Creation Failed'+'<br/>').css('color','red');";
				return true;
			}
			
		}
		else{
			echo "$('.divLogs-generateCasLis-error').append('Message: Failed to connect to Cashier List Server'+'<br/>').css('color','red');";
			exit();
			return true;
		}	
		
		exit();
	break;
	
	case 'generateCimLoyPoi':
		
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
			
			$filename = "LOYALTY_PTS_".$serverCurrentDateMinOne.$fileExt;
			
			//6/24/1987
			$tranDate = date("m/d/Y", strtotime("$serverDate", time()));
			
			$createCustomerPointsForArc = "
			Exec CreateCustomerPointsForArc '$tranDate'
			";
			
			//if(mssql_query($createCustomerPointsForArc)){
			
				$sqlCimCusPoi = "
				select
				TRAN_DT,CUST_CD,Points_Earned,Points_Redeemed,Peso_Redeemed,Actual_Peso_Redeemed
				FROM  BI_VIEW_CUSTOMERLOYALTYPOINTS
				";
				$arrSqlCimCusPoi = mssql_query($sqlCimCusPoi);

				if (file_exists($file_desti.$filename)) {
					unlink($file_desti.$filename);
				}
				$strlength = strlen($arrSqlCimCusPoi); //gets the length of our $content string.
				$xcontentx = "";
				if($strlength > 0){
				
					$xcontentx .= trim("TRAN_DT,CUST_CD,Points_Earned,Points_Redeemed,Peso_Redeemed,Actual_Peso_Redeemed");
					$xcontentx .= "\r\n";
				
					while($myrow=mssql_fetch_array($arrSqlCimCusPoi)){
					
						$xcontentx .= trim(date("Y-m-d h:i:s",strtotime($myrow['TRAN_DT']))).",";
						$xcontentx .= trim($myrow['CUST_CD']).",";
						$xcontentx .= trim($myrow['Points_Earned']).",";
						$xcontentx .= trim($myrow['Points_Redeemed']).",";
						$xcontentx .= trim($myrow['Peso_Redeemed']).",";
						$xcontentx .= trim($myrow['Actual_Peso_Redeemed']);
						$xcontentx .= "\r\n";
					}
					$create = fopen($file_desti.$filename, "x"); //uses fopen to create our file.
					fwrite($create, $xcontentx);
					fclose($create);
					
					if (file_exists($file_desti_arc_server.$filename)) {
					echo "$('.divLogs-generateCimLoyPoi-exist').append('Message: Customer Loyal Points CSV Exist at ARC(192.168.200.133) server'+'<br/>').css('color','red');";
					}else{
						$create2 = fopen($file_desti_arc_server.$filename, "x"); //uses fopen to create our file.
						fwrite($create2, $xcontentx);
						fclose($create2);
					}
					echo "$('.divLogs-generateCimLoyPoi').append('Message: Customer Loyal Points CSV Created'+'<br/>').css('color','white');";
					//$transFileToFTPMMS = ftp_put($conn1, $filename, $directory.$filename, FTP_BINARY);
				}
				else{
					echo "$('.divLogs-generateCimLoyPoi-error').append('Message: Customer Loyal Points CSV Creation Failed'+'<br/>').css('color','red');";
					return true;
				}
			
			//}
			
		}
		else{
			echo "$('.divLogs-generateCimLoyPoi-error').append('Message: Failed to connect to Cims Server'+'<br/>').css('color','red');";
			exit();
			return true;
		}	
		
		exit();
	break;
	
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
						$xcontentx .= '"'.trim($myrow['COMPANY_NAME']).'",'; #2
						$xcontentx .= '"'.trim($myrow['CUSTOMER_GROUP']).'",'; #3
						$xcontentx .= trim($myrow['GENDER_CODE']).","; #4
						$xcontentx .= '"'.trim($myrow['HOUSE_HOLD']).'",'; #5
						$xcontentx .= trim($myrow['INCOME']).","; #6
						$xcontentx .= '"'.trim($myrow['MARITAL_STATUS']).'",'; #7
						$xcontentx .= '"'.trim($myrow['LOYALTY_STATUS']).'",'; #8
						$xcontentx .= trim(date("Y-m-d h:i:s",strtotime($myrow['BIRTH_DATE']))).","; #9
						$xcontentx .= '"'.trim($myrow['LOYALTY_CARD_NO']).'",'; #10
						$xcontentx .= trim(date("Y-m-d h:i:s",strtotime($myrow['LOYALTY_CARD_ISSUE_DATE']))).","; #11
						$xcontentx .= '"'.trim($myrow['FIRST_NAME']).'",'; #12
						$xcontentx .= '"'.trim($myrow['LAST_NAME']).'",'; #13
						$xcontentx .= '"'.trim($myrow['ADDRESS1']).'",'; #14
						$xcontentx .= '"'.trim($myrow['ADDRESS2']).'",'; #15
						$xcontentx .= '"'.trim($myrow['LOCATION1']).'",'; #16
						$xcontentx .= '"'.trim($myrow['LOCATION2']).'",'; #17
						$xcontentx .= '"'.trim($myrow['LOCATION3']).'",'; #18
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
						eval(data);
						generateCimLoyPoi();
					}		
				})	
			}
			
			function generateCimLoyPoi(){
				$.ajax({
					url: 'generateCsv.php',
					type: 'POST',
					data: 'action=generateCimLoyPoi',
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
						generateCimCusFin();
					}		
				})	
			}
			
			function generateCimCusFin(){
				$.ajax({
					url: 'generateCsv.php',
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
							<input type="button" name="btnGenerateCsv" class="btn btn-info" onClick="generateCsv();" id="btnGenerateCsv" value="Generate CSV">
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