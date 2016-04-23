<?php
//include "../../../adodb/adodb.inc.php";
include("../../includes/db.inc.php");
include("../../includes/common.php");

//include("generateCsvObj.php");

$pgchequeServer = array(
"Name" =>"WIN-PGCHEQUE Server",
"Host" =>"192.168.200.210",
"User" =>"sa",
"Pass" =>"sa",
"Database" =>"dbCSV");

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
"Path" =>"/fromho/Others/POINTS%20FOR%20KIOSK/"
);

//My page refresher
$page = $_SERVER['PHP_SELF'];
$sec = "3600";

// connect to ftp server1
$conn1 = ftp_connect($server1['Host']);

// open a session to an external ftp site
$login1 = ftp_login ($conn1, $server1['User'], $server1['Pass']);

ftp_pasv($conn1, true);

//$generateCsvObj = new generateCsvObj();

switch($_POST['action']){

	case 'importPerksPoi':
    
        $directory="\\\\192.168.200.231\PerksTnap\\";
        $checkEmpty  = (count(glob($directory.'PerksCard.csv')) === 0) ? 'Empty' : 'Not empty';
			
		$cimsServerCon = mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass']);
		$cimsServerDb = mssql_select_db($cimsServer['Database']);	
		
        if ($checkEmpty == "Empty"){
            //echo "$().toastmessage('showWarningToast','<b>Folder is empty!</b>');";
            echo "0";
            exit();
        }else{
            
            $bulkInsert =  "
            BULK INSERT [ExclusivesHO].[dbo].[tblTnapPerksPointsTemp]
            FROM 'C:\PerksTnap\PerksCard.csv' 
            WITH 
            (FIELDTERMINATOR = ',',
            ROWTERMINATOR = '\n'
            )
            ";
            
            /*
              insert into tblTnapPerksPoints(cCardnumber,cCustFullName,iPoints,dLastTranDate,dLastPurchaseDate,iLastPointsEarned,lastName)
    select cCardnumber,cCustFullName,iPoints,dLastTranDate,NULL,NULL,lastName from tblTnapPerksPointsTemp
            */
            
            if($sql_exec_sql_insertTblTnapPerksPoints = mssql_query($bulkInsert)){
                echo "1";  
            }     
        }
          
		exit();
	break;
    
    case 'importTnapPoi':
    
        $directory="\\\\192.168.200.231\PerksTnap\\";
        $checkEmpty  = (count(glob($directory.'TnapCard.csv')) === 0) ? 'Empty' : 'Not empty';
    
        $cimsServerCon = mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass']);
        $cimsServerDb = mssql_select_db($cimsServer['Database']);    
        
        if ($checkEmpty == "Empty"){
            //echo "$().toastmessage('showWarningToast','<b>Folder is empty!</b>');";
            echo "0";
            exit();
        }else{
            $deleteMoreThan16 = "
            delete from tblTnapPerksPointsTemp where len(cCardnumber) > 16
            ";
            
            $bulkInsert =  "
            BULK INSERT [ExclusivesHO].[dbo].[tblTnapPerksPointsTemp]
            FROM 'C:\PerksTnap\TnapCard.csv' 
            WITH 
            (FIELDTERMINATOR = ',',
            ROWTERMINATOR = '\n'
            )
            ";
            
            $inserttblTnapPerksPoints = "
            insert into tblTnapPerksPoints(cCardnumber,cCustFullName,iPoints,dLastTranDate,dLastPurchaseDate,iLastPointsEarned,lastName)
            select cCardnumber,cCustFullName,iPoints,dLastTranDate,NULL,NULL,lastName from tblTnapPerksPointsTemp
            ";
            
            if($sql_exec_sql_deleteMoreThan16 = mssql_query($deleteMoreThan16)){ 
                if($sql_exec_sql_insertTblTnapPerksPoints = mssql_query($bulkInsert)){    
                    if($sql_exec_sql_insertTblTnapPerksPoints = mssql_query($inserttblTnapPerksPoints)){
                        echo "1"; 
                    }    
                }
            }
        }
        
        
        exit();
    break;
	
    
    case 'generateTnapAndPerksPoi':
        
        $cimsServerCon = mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass']);
        $cimsServerDb = mssql_select_db($cimsServer['Database']);    
        
        //$file_desti = "../../exported_file/"; // File destination
        $file_desti="\\\\192.168.200.231\PerksTnap\\";
        //$file_desti_arc_server = "\\\\192.168.200.133\pg_src_data\\"; // ARC Server
        
        $gmt = time() + (8 * 60 * 60);
        $todayTime = date("His",$gmt);
        $datefileM = date("m",$gmt);
        $datefileD = date("d",$gmt);
        $datefileY = date("Y",$gmt);
        $fileExt = ".txt";
        //$fileExtComp = ".ZIP";
        
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
            
            $filename = $serverCurrentDateMinOne.$fileExt;
            //$filenameComp = $serverCurrentDateMinOne.$fileExtComp;
            
            //6/24/1987
            $tranDate = date("m/d/Y", strtotime("$serverDate - 1", time()));
            
                $sqlCustPoi= "
                select cCardNumber,cCustFullName,iPoints,dLastTranDate,lastName from tblTnapPerksPoints 
                where iPoints > .00
                ";
                $arrSqlCustPoi = mssql_query($sqlCustPoi);
                
                $sql6="
                EXECUTE master.dbo.xp_cmdshell  'bcp \"SELECT * from dbOracle..ORS_TblManpowerView \"  queryout $fileDesti$filename -t -c  -Uoracle -Poracle@2015 -SWIN-PGCHEQUE'
                ";

                if (file_exists($file_desti.$filename)) {
                    unlink($file_desti.$filename);
                }
                $strlength = strlen($arrSqlCustPoi); //gets the length of our $content string.
                $xcontentx = "";
                if($strlength > 0){
                
                    while($myrow=mssql_fetch_array($arrSqlCustPoi)){
                    
                        $xcontentx .= trim($myrow['cCardNumber'])."|";
                        $xcontentx .= trim($myrow['cCustFullName'])."|";
                        $xcontentx .= trim($myrow['iPoints'])."|";
                        $lastTranDte = $myrow['dLastTranDate'];
                        $lastTranDateMinOne = date("Ymd", strtotime("$lastTranDte -1 day", time()));
                        $lastTranDate = date("Y-m-d h:i:s", strtotime($lastTranDateMinOne));
                        $xcontentx .= trim($lastTranDate)."|";
                        //$lastPurchaseDate = date("Y-m-d h:i:s", strtotime($myrow['dLastPurchaseDate']));
                        //$xcontentx .= trim($lastPurchaseDate)."º";
                        $xcontentx .= trim($myrow['lastName']);
                        $xcontentx .= "\r\n";
                    }
                    $create = fopen($file_desti.$filename, "x"); //uses fopen to create our file.
                    fwrite($create, $xcontentx);                    
                    fclose($create);
                    
                    echo "$('.divLogs-tnapPoi').append('Message: TNAP Points and Perks Points Textfile Created!'+'<br/>').css('color','white');";
                    
                    /*
                    // zip file
                    $zip = new ZipArchive;
                    $res = $zip->open($file_desti.$filenameComp, ZipArchive::CREATE);
                    if ($res === TRUE) {
                        
                        if($zip->addFile($file_desti.$filename, $filename)){
                            $zip->close();
                            if(file_exists($file_desti.$filenameComp)){
                                //ftp_put($conn1, $server1['Path'].$filenameComp, $file_desti.$filenameComp, FTP_ASCII);  // upload the file
                                ftp_put($conn1, $server1['Path'].$filenameComp, $file_desti.$filenameComp, FTP_BINARY);  // upload the file
                                echo "$('.divLogs-custPoi').append('Message: Customer  Points Created'+'<br/>').css('color','white');";
                                
                            }else{
                                echo "$('.divLogs-custPoi-ftp').append('Message: Upload to FTP failed'+'<br/>').css('color','red');";
                            }
                        }else{
                            echo "$('.divLogs-custPoi-ftp').append('Message: Error in compressing file to zip file'+'<br/>').css('color','red');";
                        }
                    } else {
                        
                        echo "$('.divLogs-custPoi-zip').append('Message: Error in compressing file to zip file'+'<br/>').css('color','red');";
                    }
                    */
                    
                    // end zip file
                    
                    //$transFileToFTPMMS = ftp_put($conn1, $filename, $directory.$filename, FTP_BINARY);
                }
                else{
                    echo "$('.divLogs-tnapPerksPoi-error').append('Message: TNAP Points and Perks Points Creation Failed'+'<br/>').css('color','red');";
                    return true;
                }
            
        }
        else{
            echo "$('.divLogs-tnapPerksPoi-error').append('Message: Failed to connect to Cims Server'+'<br/>').css('color','red');";
            exit();
            return true;
        }    
        
        exit();
    break;
    
    /*
	case 'generateTnapAndPerksPoi':
    

        $qryExec[0] = "EXECUTE master.dbo.xp_cmdshell  'bcp \"select  * from PGBIS_..[".$storeCode."_FACT_SALES-2005] where TIMECD between ".$_GET['cboyear'].$_GET['cbomonth']."01 and ".$_GET['cboyear'].$_GET['cbomonth']."05\" queryout C:\wamp\www\SALESEXPORT\Sales\/\"$storeCode\"SALES".$_GET['cbomonth']."01".$_GET['cboyear'].".txt -t$delimeter -c -T  -Usa -Psa -S192.168.200.232'";
   
      
		
		$cimsServerCon = mssql_connect($cimsServer['Host'],$cimsServer['User'],$cimsServer['Pass']);
		$cimsServerDb = mssql_select_db($cimsServer['Database']);	
        
        $gmt = time() + (8 * 60 * 60);
        $todayTime = date("His",$gmt);
        $datefileM = date("m",$gmt);
        $datefileD = date("d",$gmt);
        $datefileY = date("Y",$gmt);  
        
        $curServerDate = "
        select GETDATE() as CurrentDateTime
        ";
        $assCurServerDate = mssql_fetch_assoc(mssql_query($curServerDate));
        $serverDate = $assCurServerDate['CurrentDateTime'];
        $tranDate = date("m/d/Y", strtotime("$serverDate - 1", time()));
		
		$qryExec = "EXECUTE master.dbo.xp_cmdshell  'bcp \"select  * from [ExclusivesHO].[dbo].[tblTnapPerksPointsTemp] 
        queryout C:\PerksTnap\/\"$storeCode\".txt 
        -t, 
        -c 
        -T  
        -Usa 
        -Psa 
        -S192.168.200.231'";
        if($sql_exec_sql_createTblTnapPerksPoints = mssql_query($qryExec)){
            return true;
        }
		exit();
	break;
    
    EXECUTE master.dbo.xp_cmdshell  'bcp "SELECT * from ExclusivesHO..PerksAndTnapPointsWeb"  queryout C:\wamp\file.txt -t -c  -Usa -Psa -SCIMS Server'

    SELECT * from ExclusivesHO..PerksAndTnapPointsWeb

    */
	
}
?>
		
<html>

	<head>
	
	<meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
	
		
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
					url: 'tnapPerksPoi.php',
					type: 'POST',
					data: 'action=importPerksPoi',
					beforeSend: function() {
						 $('#btnGenerateCsv').val('Running...');
                         $('#btnGenerateCsv').attr('disabled','disabled');
					},  
                    error: function(data){
                        $('.divLogs-PerksCsv-error').append('Message: Perks Points failed to import'+'<br/>').css('color','red');
                    },  
					success: function(data){
						if(data == 0){
							$().toastmessage('showToast', {
								text: 'PerksCard.csv not found in the folder!',
								sticky: true,
								position: 'middle-center',
								type: 'warning',
								closeText: '',
								close: function () 
								{
								console.log("toast is closed ...");
								jQuery('#activity_pane').hideLoading();
                                $('#btnGenerateCsv').val('TNAP Points & Perks Points');
                                $('#btnGenerateCsv').removeAttr('disabled','disabled');
								}       
							});
						}else{
							$('#btnGenerateCsv').val('Running...');
							$('#btnGenerateCsv').attr('disabled','disabled');
							$(".btn:hover").css({
								"text-decoration": "none",
								"background": "#3498db"
							});
							eval(data);  
							$('.divLogs-PerksCsv').append('Message: Perks Points successfully imported'+'<br/>').css('color','white');
                            importTnapPoi();
						}
					}		
				})	
			}
			
			function importTnapPoi(){
                $.ajax({
                    url: 'tnapPerksPoi.php',
                    type: 'POST',
                    data: 'action=importTnapPoi',
                    beforeSend: function() {
                         $('#btnGenerateCsv').val('Running...');
                         $('#btnGenerateCsv').attr('disabled','disabled');
                    },
                    error: function(data){
                        $('.divLogs-TnapCsv-error').append('Message: TNAP Points failed to import'+'<br/>').css('color','red');
                    },
                    success: function(data){
                        if(data == 0){
                            $().toastmessage('showToast', {
                                text: 'TnapCard.csv not found in the folder!',
                                sticky: true,
                                position: 'middle-center',
                                type: 'warning',
                                closeText: '',
                                close: function () 
                                {
                                console.log("toast is closed ...");
                                jQuery('#activity_pane').hideLoading();
                                $('#btnGenerateCsv').val('TNAP Points & Perks Points');
                                $('#btnGenerateCsv').removeAttr('disabled','disabled');
                                }       
                            });
                        }else{
                            $('#btnGenerateCsv').val('Running...');
                            $('#btnGenerateCsv').attr('disabled','disabled');
                            $(".btn:hover").css({
                                "text-decoration": "none",
                                "background": "#3498db"
                            });
                            eval(data);
                            $('.divLogs-TnapCsv').append('Message: TNAP Points successfully imported'+'<br/>').css('color','white');
                            generateTnapAndPerksPoi();
                        }
                    }        
                })    
			}
			
			function generateTnapAndPerksPoi(){
				$.ajax({
					url: 'tnapPerksPoi.php',
					type: 'POST',
					data: 'action=generateTnapAndPerksPoi',
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
							<input type="button" name="btnGenerateCsv" class="btn btn-info" onClick="generateCsv();" id="btnGenerateCsv" value="TNAP Points & Perks Points">
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
							<div class="divLogs-PerksCsv">
							</div>
							<div class="divLogs-PerksCsv-error">
							</div>
                            <div class="divLogs-TnapCsv">
                            </div>
                            <div class="divLogs-TnapCsv-error">
                            </div>
							<div class="divLogs-tnapPoi">
							</div>
							<div class="divLogs-tnapPoi-error">
							</div>
							</div>
							<div class="divLogs-tnapPerksPoi">
							</div>
							<div class="divLogs-tnapPerksPoi-error">
							</div>
							
						</div>
						
					</div>
					
				</form>	
  
			</div>
			
        </body>
		
</html>