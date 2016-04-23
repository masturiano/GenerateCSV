<?php
include "../../../adodb/adodb.inc.php";
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("oraToMmsObj.php");


//Connection of copy files from local to 192.168.200.100 
	$server1 = array(
	"Name" =>"Downloads",
	"Host" =>"192.168.200.100",
	"User" =>"amacad",
	"Pass" =>"123456",
	"Path" =>"/pgtflr/ORACHK/Unprocess/");
	
	// connect to mms server
	$conn1 = ftp_connect($server1['Host']);
	
	// open a session to an external ftp site
	$login1 = ftp_login ($conn1, $server1['User'], $server1['Pass']);
	
	ftp_pasv($conn1, true);
	
	// check open
	if ((!$conn1) || (!$login1)) {
		echo "Ftp-connect failed!"; die;
	}
	else {
		//echo "Connected to " . $server2['Name'] . " FTP server2.<br><br>";
	}
	
	//[b]ftp_set_option($conn2, FTP_TIMEOUT_SEC, 600);[/b]
	
	ftp_chdir($conn1, $server1["Path"]); 
	
	// local directory
	$directory="C:/wamp/www/ORS/modules/report/rtv/EXPORTEDFILE/";
	// create a handler to the directory
	$dirhandler = opendir($directory);
	// read all the files from directory
	$nofiles=0;
//Close connection


$oraToMmsObj = new oraToMmsObj();

$arrResetRtvBatchNo_ = $oraToMmsObj->arrResetRtvBatchNo();

			echo $arrResetRtvBatchNo_['lastRtvBatch'];
			echo "AA";

switch($_POST['action']){
	
	case 'getRtvBatch':
		$arrRtvBatchNo = $oraToMmsObj->arrRtvBatchNo();
		
		echo "CO".str_pad($arrRtvBatchNo['lastRtvBatch'], 6, '0', STR_PAD_LEFT);
		exit();
	break;
	
	case "create":
		
		########## ADO DB CONNECTION ##########  
		$db = NewADOConnection("oci8");
		$host = "192.168.200.136"; // PROD
		//$host = "192.168.200.135"; // NEW UAT
		$port = "1521"; // PROD
		//$port = "1532"; // NEW UAT
		$sid = "PROD"; // PROD
		//$sid = "NPROD"; // NEW UAT
		$cstr = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port))
				(CONNECT_DATA=(SID=$sid)))";
		
		$checkFrom = $_POST['checkFrom'];
		$checkTo = $_POST['checkTo'];

		if(!empty($checkFrom) && empty($checkTo)){
			$FromOpt = "AND a.CHECK_NUMBER = $checkFrom";	
		}
		if(!empty($checkFrom) && !empty($checkTo)){
			$FromToOpt = "AND a.CHECK_NUMBER >= $checkFrom AND a.CHECK_NUMBER <= $checkTo";	
		}
			
		/*if((!empty($checkFrom) && empty($checkTo)) || (!empty($checkFrom) && !empty($checkTo) && $checkFrom <= $checkTo)){*/
		if($db->Connect($cstr, 'apps', 'apps')){	
		$rs = $db->Execute("
			SELECT a.CHECK_ID,
			a.CHECK_DATE,
			a.CHECK_NUMBER,
			a.CHECKRUN_NAME,
			a.CREATED_BY,
			a.CREATION_DATE,
			c.INVOICE_NUM,
			d.SEGMENT1,
			d.VENDOR_NAME,
			c.INVOICE_DATE,
			c.SOURCE,
			c.INVOICE_AMOUNT,
			f.USER_NAME,
			e.VENDOR_SITE_CODE,
      		d.SEGMENT1
			FROM ap_checks_all a,
			ap_invoice_payments_all b,
			ap_invoices_all c,
			ap_suppliers d,
			ap_supplier_sites_all e,
			fnd_user f
			WHERE a.CHECK_ID     = b.CHECK_ID
			AND b.INVOICE_ID     = c.INVOICE_ID
			AND c.VENDOR_ID      = d.VENDOR_ID
			AND c.VENDOR_SITE_ID = e.VENDOR_SITE_ID
			AND a.CREATED_BY     = f.USER_ID
			AND (c.SOURCE = 'RTV'
			AND a.org_id in (85,87)
			AND a.bank_account_num = '".$_POST['bankAcctNum']."'
			$FromOpt
			$FromToOpt)");
			
			$file_desti = "EXPORTEDFILE/";//file destination
			
			$gmt = time() + (8 * 60 * 60);
			$todayTime = date("His",$gmt);
			$datefileM = date("m",$gmt);
			$datefileD = date("d",$gmt);
			$datefileY = date("y",$gmt);
			$fileExt = ".CSV";
			
			$filename = "CO".$datefileM.$datefileD.$datefileY."_".$todayTime.$fileExt;
			
			if (file_exists($file_desti.$filename)) {
				unlink($file_desti.$filename);
			}
			$strlength = strlen($rs); //gets the length of our $content string.
			$xcontentx = "";
			if($strlength>177){
				foreach($rs as $xcontents){
				//while(!$rs.EOF){
					$xcontentx .= trim(preg_replace("/[^0-9]/","",$rs->fields['INVOICE_NUM'])).",";	#RTVNO#RTV NUMBER #INVOICE_NUM 1
					$xcontentx .= "1".",";	#RTVCEN#RTV DATE CEN #HARDCODE_1 2
					$xcontentx .= date("ymd",strtotime($rs->fields['INVOICE_DATE'])).",";	#RTVDTE#RTV DATE #INVOICE_DATE 3
					$arrStrComp = $oraToMmsObj->findStrComp($rs->fields['VENDOR_SITE_CODE']);
					$xcontentx .= trim($arrStrComp['STRNUM']).",";	#RTVSTR#Store Number 4
					$xcontentx .= trim($arrStrComp['STCOMP']).",";	#RTVCMP#COMPANY NUMBER 5
					$xcontentx .= trim($rs->fields['SEGMENT1']).",";	#RTVVEN#Vendor Number #SEGMENT1 6
					$xcontentx .= "".",";	#RTABCH#AP Batch Number 7
					$xcontentx .= $_POST['batchNumber'].",";	#RTCBCH#CR Batch Number #HARDCODE_BLANK 8
					if (strpos(trim($rs->fields['VENDOR_NAME']),'{TRADE}') !== false){
						$vendorType = '1';
					}if (strpos(trim($rs->fields['VENDOR_NAME']),'{NON TRADE}') !== false){
						$vendorType = '2';
					}
					$xcontentx .= $vendorType.",";	#RTVTPE#Vendor Type #VENDOR_NAME 9
					$xcontentx .= trim($rs->fields['INVOICE_NUM']);	#RTVINV#Invoice Number #INVOICE_NUM 10
					$xcontentx .= "\r\n";
				}
				$create = fopen($file_desti.$filename, "x"); //uses fopen to create our file.
				fwrite($create, $xcontentx);
				fclose($create);
				echo "$().toastmessage('showSuccessToast','Textfile created!<br>Your Batch Number is <b><h4>".$_POST['batchNumber']."<h4></b>');";
				echo "$('#getBatch').val('');";
				echo "$('#bankAccountNum').val('');";
				echo "$('#checkFrom').val('');";
				echo "$('#checkTo').val('');";
				echo "$('#btnGetBatch').removeAttr('disabled');";
				$transFileToFTPMMS = ftp_put($conn1, $filename, $directory.$filename, FTP_BINARY);
			}
			else{
				echo "$().toastmessage('showErrorToast','No textfile created!<br>Please check Bank Account or Check Number!');";
				return false;
			}
			//$size = filesize($create); //151 bytes is 0kb default
//			if($size > 151){
//				echo "alert('aaaaa')";
//			}
//			else{
//				echo "alert('bbbbb')";
//				unlink($create);
//			}

			######################## EXPORT TEXTFILE VERSION 2
			######################## EXPORT TEXTFILE VERSION 2
			
			//$filename = 'somefile.txt';
			//echo $create . ': ' . filesize($create) . ' bytes';
			
							//echo'<meta http-equiv="refresh" content="0;URL=index.php">';
				
				/* Uploading */

				//copy ($_FILES['pic']['tmp_name'], "$site/".$_FILES['pic']['name']);
				
				/* Uploaded */
		}
		else{
			//echo "$('#confirmDialogs').dialog('close');";
			//echo "$('#confirmDialogs').dialog('destroy');";
			echo "alert('Failed to connect to Oracle Server/Database!');";
			exit();
		}
		$db->Disconnect();
		/*}*/
		
		exit();
	break;
		
}
?>

<html>
	<head>
    	<!-- jQuery, Bootstrap -->
    	<link rel="stylesheet" href="../../../includes/bootstrap/css/bootstrap.css"/>
        <link rel="stylesheet" href="../../../includes/bootstrap/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="../../../includes/bootstrap/css/bootstrap-responsive.css"/>
        <link rel="stylesheet" href="../../../includes/bootstrap/css/bootstrap-responsive.min.css"/>
        <!-- jQuery, Bootstrap -->
        
        <script src="../../../includes/jquery/js/jquery-1.6.2.min.js"></script>
        <script src="../../../includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script src="../../../includes/bootbox/bootbox.js"></script>
		
		<script src="../../../includes/jquery/js/jquery-1.6.2.min.js"></script>
        <script src="../../../includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script src="../../../includes/bootbox/bootbox.js"></script>
        
        <script src="../../../includes/toastmessage/src/main/javascript/jquery.toastmessage.js"></script>
        <link rel="stylesheet" type="text/css" href="../../../includes/toastmessage/src/main/resources/css/jquery.toastmessage.css" />
        
        <script src="../../../includes/modal/modal.js"></script>
        <link rel="stylesheet" href="../../../includes/modal/modal.css">
        
        
        <link href="../../../includes/showLoading/css/showLoading.css" rel="stylesheet" media="screen" /> 
        <!--<script type="text/javascript" src="../../../includes/showLoading/js/jquery-1.3.2.min.js"></script>-->
        <script type="text/javascript" src="../../../includes/showLoading/js/jquery.showLoading.js"></script>
        
        <script type="text/javascript">
		

		function CreateTextfile(){
			
			if($('#getBatch').val()==0){
					$().toastmessage('showToast', {
					text: 'Please get Batch Number first!',
					sticky: true,
					position: 'middle-center',
					type: 'error',
					closeText: '',
					close: function () {
						console.log("toast is closed ...");
					}
				});
				return false;
			}
			
			if($('#bankAccountNum').val()==0){
					$().toastmessage('showToast', {
					text: 'Please select Bank Account!',
					sticky: true,
					position: 'middle-center',
					type: 'error',
					closeText: '',
					close: function () {
						console.log("toast is closed ...");
					}
				});
				return false;
			}
			
			if($('#checkFrom').val()==0 && $('#checkTo').val()==0){
					$().toastmessage('showToast', {
					text: 'Please input check number!',
					sticky: true,
					position: 'middle-center',
					type: 'error',
					closeText: '',
					close: function () {
						console.log("toast is closed ...");
					}
				});
				return false;
			}
			if($('#checkFrom').val()==0 && $('#checkTo').val()!=0){
					$().toastmessage('showToast', {
					text: 'Please input check number From!',
					sticky: true,
					position: 'middle-center',
					type: 'error',
					closeText: '',
					close: function () {
						console.log("toast is closed ...");
					}
				});
				return false;
			}
			if($('#checkFrom').val()!=0 && $('#checkTo').val()!=0 && $('#checkFrom').val() > $('#checkTo').val()){
					$().toastmessage('showToast', {
					text: 'Invalid range! From is less than To',
					sticky: true,
					position: 'middle-center',
					type: 'error',
					closeText: '',
					close: function () {
						console.log("toast is closed ...");
					}
				});
				return false;
			}
			
			$.ajax({
				url: 'oraToMms.php',
				type: 'POST',
				data: 'action=create&batchNumber='+$("#getBatch").val()+'&bankAcctNum='+$("#bankAccountNum").val()+'&checkFrom='+$("#checkFrom").val()+'&checkTo='+$("#checkTo").val(),
				beforeSend: function() {
					jQuery('#activity_pane').showLoading();
				},
				success: function(data){
					jQuery('#activity_pane').hideLoading();
					eval(data);
					//$("#getBatch").val('');
//					$("#bankAccountNum").val('')
//					$("#checkFrom").val('');
//					$("#checkTo").val('');
//					$('#btnGetBatch').removeAttr('disabled');
					//$(document).ajaxStop(function(){
					// setTimeout("window.location = 'oraToMms.php'",2000);
					//});
				}
				
			});
			
		}

		
		function getRtvBatch(){
			$.ajax({
				url: 'oraToMms.php',
				type: 'POST',
				data: 'action=getRtvBatch',
				success: function(data){
					$("#getBatch").val(data);
					$('#btnGetBatch').attr('disabled','disabled');
				}		
			})	
		}

		function numericFilter(txb) {
		   txb.value = txb.value.replace(/[^0-9]/ig, "");
		}
		

		</script>
        
        <style type="text/css">
		<!--
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
		.dvContainer{
			margin-left:10px;
		}
		#input-userName{
			margin-left:50px;
		}
		
		.selectBox{
			width:568px;	
		}
		
a {
  color: blue;
  cursor:pointer;
	  text-decoration: underline;
}

	div.instructions_container {
   float: left;
	   width: 100%;

	}

div#activity_pane {
	   float:left;
	   width: 100%;
	   height: 100%;
	   border: 1px solid #CCCCCC;
	   background-color:#FFF;
   padding-top: 0px;
   text-align: center;
	   
	}

	div.example_links 
	 .link_category {
	   margin-bottom: 15px;
	}

.loading-indicator-bars {
	background-image: url('images/loading-bars.gif');
	width: 150px;
}
		-->	
		</style>   
    </head>
    	<body>
			<div id="activity_pane">
			<br />
            
            <form class="form-horizontal">
                <div class="dvContainer">
                	<table border=0>
                    	<tr>
                        	<td>
                   				 <b>Batch Number :</b>
                            </td>
                           	<td>
                                <div class="input-prepend">
                                <span class="add-on"><i class="icon-tag"></i></span>
                                <input type="text" id="getBatch" name="getBatch" style="height:30px;" value="<? echo $arrRtvBatchNo;?>" class="input-xlarge" readonly></div>
                                
                            </td>
                            <td colspan="3">
                            	<input type="button" name="btnGetBatch" class="btn btn-info" onClick="getRtvBatch();" id="btnGetBatch" value="Get Batch">
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="3" align="center" style="height:6px;">
                            </td>
                        </tr>
                        <tr>
                        	<td>
                   				 <b>Bank Account :</b>
                            </td>
                            <td colspan=2>
                                <div class="input-prepend">
                                <span class="add-on"><i class="icon-info-sign "></i></span>
                            	<? $oraToMmsObj->DropDownMenu($oraToMmsObj->makeArr($oraToMmsObj->findBankAcct(),'bankAccountNum','combBank',''),'bankAccountNum','','class="selectBox"'); ?>
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="3" align="center" style="height:6px;">
                            </td>
                        </tr>
                        <tr>
                        	<td>
                   				 <b>Check Number :</b>
                            </td>
                           	<td>
                                <div class="input-prepend">
                                <span class="add-on"><i class="icon-circle-arrow-left"></i></span>
                                <input type="text" id="checkFrom" name="checkFrom" style="height:30px;" class="input-xlarge"
                                placeholder="From" onKeyUp="numericFilter(this);"></div>
                            </td>
                            <td>
                            	<div class="input-prepend">
                                <span class="add-on"><i class="icon-circle-arrow-right"></i></span>
                                <input type="text" id="checkTo" name="checkTo" style="height:30px;" class="input-xlarge"
                                placeholder="To" onKeyUp="numericFilter(this);"></div>
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="3" align="center" style="height:6px;">
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="3">
                            	<input type="button" name="submit" class="btn btn-success" onClick="CreateTextfile();" value="CREATE">
                            </td>
                        </tr>
                     </table>
                </div>
            </form>	
            
            <!--<img src="../../../includes/images/Spinner.gif">-->
  
			</div>
            <div style="clear:both;"></div>
			
        </body>
</html>