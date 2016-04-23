<?php require_once "../../includes/phpfileuploader/phpuploader/include_phpuploader.php" ?>
<?php session_start(); ?>

<?php

ini_set('memory_limit','128M');
ini_set('max_execution_time','0');

include("../../includes/db.inc.php");
include("../../includes/common.php");


$dbqry = new dbHandler();

if(isset($_POST['initialize']))
{
		//$dest_file_name = "C:/wamp/www/IIC/ORA_TEXTFILE/";
	
	//CONFIG
	$file_dr = "C:/wamp/www/CCDCMS/btf_files/"; //file directory
	$arch_dr = "C:/wamp/www/CCDCMS/archieves/"; //archieve directory
	
	$mms_table = "TBLBTFprocess"; //mms table
	
	if ($handlex = opendir($file_dr)) {
		//echo "Directory handlex: $handlex\n";
		//echo "Files:\n";
		
		$dbqry->execQry('EXEC delete_tblBTFprocess');
			
		/* This is the correct way to loop over the directory. */
		while (false !== ($file = readdir($handlex))) {
			//filename reference
			
			//********** BTF FILE *********
			//	.B01 -	PAYMENT FILE
			
			//get extension
			$file_ext = explode('.',$file);
			$max_val = count($file_ext);
			$file_ext = $file_ext[($max_val-1)];
			$ermsg = "";
			$wrngfl = "";
			//check file type
			
			
			if($file_ext == "B01"  )
			{
				//BTF FILES GOES HERE
				//FILE CONENT CHEAT
				//rctnum|rctdate|amt|rctmth|cusnum|cusloc|cusname|cuslocd|curr|mtrdate|invnum|ccnum|amtd|filler1|fname| mmsloc 
				
				$orafile = $file_dr.$file;
				//echo "$('#test').html('Read File ... {$file}');";
				
				if (($handle = fopen($orafile, "r")) !== FALSE) {
					while (($data = fgetcsv($handle, 0, "|")) !== FALSE) {
						$num = count($data);
						//echo "<p> $num fields in line $row: <br /></p>\n";
					
						//$info = explode("|",$data[$c]);
						$info = $data;
						
						//get customer location location IDENTITY_INSERT
						$cusmmsno = "select STRNUM FROM sql_mmpgtlib..tblstr WHERE stshrt = '{$info[5]}'";
						$cusmmsno = mssql_query($cusmmsno);
						$cusno = mssql_fetch_assoc($cusmmsno);
						$cusno = $cusno['STRNUM'];
						if(mssql_num_rows($cusmmsno) >= 1)
						{
							$xqueryx = "INSERT INTO {$mms_table} (rctnum,rctdate,amt,rctmth,cusnum,cusloc,cusname,cuslocd,curr,mtrdate,invnum,ccnum,amtd,filler1,fname,mmsloc) VALUES('".$info[0]."','".$info[1]."','".$info[2]."','".$info[3]."','".$info[4]."','".$info[5]."','".$info[6]."','".$info[7]."','".$info[8]."','".$info[9]."','".$info[10]."','".$info[11]."','".$info[12]."','".$info[13]."','".$info[14]."','{$cusno}')";
							//mssql_query("SET IDENTITY_INSERT dtsloop_x ON");
							$insert_item = mssql_query($xqueryx);
							if($insert_item)
							{
								//echo "$('#test').append('<br />Line ... {$c} added successfully');";
							}
							else
							{
								//echo "$('#test').append('<br />Line ... {$c} failed to add');";
								$ermsg .= $info[14].",";
							}
						}
						else
						{
							$wrngsup .= $wrngsup.",".$info[5]." file:".$file.",";
						}
						
						
					}
					fclose($handle);
				}
			}
			else
			{
				//FILES NOT NEEDED GOES HERE
				$wrngfl .= $wrngfl.",".$file;
			}
	
		}
		closedir($handlex);
		
		
		//MOVE FILES
		// Get array of all source files
		$files = scandir($file_dr);
		// Identify directories
		$source = $file_dr;
		
		
		  if (in_array($file, array(".",".."))) continue;
		  // If we copied this successfully, mark it for deletion
		  //ftp_pasv ($conn_id, true);
			
			// Cycle through all source files
			foreach ($files as $file) 
			{	
				
				if (in_array($file, array(".",".."))) continue;
				// If we copied this successfully, mark it for deletion
						
					if (copy($source.$file, $arch_dr.$file)) {
					
					//echo "$('#test').html('{$file} has been moved!');";
					
					$delete[] = $source.$file;
				}
				
			}

				
			// Delete all successfully-copied files
			foreach ($delete as $file) {
			  unlink($file);
			}
		
					 
		// close the FTP stream
		//ftp_close($conn_id);
		
			echo "alert('Process Finished!".$ermsg."');";
			//echo "$( '#process_file').dialog( 'close');";
			//echo "$( this ).dialog( 'close');";
	}
	exit();
}


?>

<html>
    <head>
        <title>Demo 1 - use SaveDirectory property</title>
        
        <script src="../../includes/jquery/js/jquery-1.6.2.min.js"></script>
        <script src="../../includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script src="../../includes/bootbox/bootbox.js"></script>
        
        <script src="../../includes/toastmessage/src/main/javascript/jquery.toastmessage.js"></script>
        <link rel="stylesheet" type="text/css" href="../../includes/toastmessage/src/main/resources/css/jquery.toastmessage.css" />
    	<script type="text/javascript">
     
            function showSuccessToast() {
                $().toastmessage('showSuccessToast', "Success Dialog which is fading away ...");
            }
            function showStickySuccessToast() {
                $().toastmessage('showToast', {
                    text: 'Success Dialog which is sticky',
                    sticky: true,
                    position: 'middle-center',
                    type: 'success',
                    closeText: '',
                    close: function () {
                        console.log("toast is closed ...");
                    }
                });
     
            }
            function showNoticeToast() {
                $().toastmessage('showNoticeToast', "Notice  Dialog which is fading away ...");
            }
            function showStickyNoticeToast() {
                $().toastmessage('showToast', {
                    text: 'Notice Dialog which is sticky',
                    sticky: true,
                    position: 'middle-center',
                    type: 'notice',
                    closeText: '',
                    close: function () { console.log("toast is closed ..."); }
                });
            }
            function showWarningToast() {
                $().toastmessage('showWarningToast', "Warning Dialog which is fading away ...");
            }
            function showStickyWarningToast() {
                $().toastmessage('showToast', {
                    text: 'Warning Dialog which is sticky',
                    sticky: true,
                    position: 'middle-center',
                    type: 'warning',
                    closeText: '',
                    close: function () {
                        console.log("toast is closed ...");
                    }
                });
            }
            function showErrorToast() {
                $().toastmessage('showErrorToast', "Error Dialog which is fading away ...");
            }
            function showStickyErrorToast() {
                $().toastmessage('showToast', {
                    text: 'Error Dialog which is sticky',
                    sticky: true,
                    position: 'middle-center',
                    type: 'error',
                    closeText: '',
                    close: function () {
                        console.log("toast is closed ...");
                    }
                });
            }
			
			$('document').ready(function(){
				$('#processbtf').click(function(){
					$.ajax({
						url:"uploadbtffile.php",
						type:"POST",
						data:{initialize:"yes"},
						//beforeSend: showprogress('open'),
						success: function(data){
							eval(data);
							//showprogress('close');
							//$( this ).dialog( "close" );
						}
					});
				});
			});
			
			
		</script>
		
    </head>
    <body>
        <div>
			<h2>Upload BTF Files</h2>
        
			<input type="button" name="processbtf" value="PROCESS BTF" id="processbtf" /> 
		
        </div>
         
        
    </body>
</html>