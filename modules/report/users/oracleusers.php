<?php
//initialize session
session_start();
//end initialize session

//URL SAMPLE: 192.168.110.42/PG-ORACLE-CHECKS/ORA_GL.php?dfrm=may-01-2013&dto=may-10-2013&company=85

//avoid error
//error_reporting(1);
//end avoid error message

//include
ini_set("memory_limit","1g");
ini_set('include_path','C:\wamp\php\PEAR');
//include("Obj.php");
include "adodb/adodb.inc.php";
require_once('Spreadsheet/Excel/Writer.php');
//end include
?>

<html>
    <head>
        <title>Oracle Users</title>
        
        <script src="../../../includes/jquery/js/jquery-1.6.2.min.js"></script>
        <script src="../../../includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script src="../../../includes/jquery/js/jquery.dataTables.js"></script>
		<style type="text/css" title="currentStyle">
			@import "../../../includes/jquery/css/redmond/jquery-ui-1.8.16.custom.css";
			@import "../../../includes/jquery/css/jquery.dataTables_themeroller.css";
		</style>
    </head>
    <body>
     

<?php
//convert time
//$strtotimeFrom =   strtotime($datefrom) - 86400;
//$strtotimeTo =   strtotime($dateto ) + 86400;

//$strtotimeTo =   strtotime($dateto );
//end convert time

########## EXCEL ##########

	$db = NewADOConnection("oci8");
	$host = "192.168.200.136"; // PROD
	//$host = "192.168.200.135"; // NEW UAT
	$port = "1521"; // PROD
	//$port = "1532"; // NEW UAT
	$sid = "PROD"; // PROD
	//$sid = "NPROD"; // NEW UAT
	$cstr = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port))
				(CONNECT_DATA=(SID=$sid)))";
	
	$orauserqry = "SELECT fnd_user.USER_ID, fnd_user.USER_NAME, fnd_user.START_DATE, fnd_user.END_DATE, fnd_user.DESCRIPTION, fnd_user_resp_groups_all.RESPONSIBILITY_ID, fnd_responsibility.RESPONSIBILITY_KEY, fnd_responsibility.START_DATE, fnd_responsibility.END_DATE
FROM (fnd_user_resp_groups_all INNER JOIN fnd_user ON fnd_user_resp_groups_all.USER_ID = fnd_user.USER_ID) INNER JOIN fnd_responsibility ON fnd_user_resp_groups_all.RESPONSIBILITY_ID = fnd_responsibility.RESPONSIBILITY_ID
where fnd_user.user_id >= 1000
GROUP BY fnd_user.USER_ID, fnd_user.USER_NAME, fnd_user.START_DATE, fnd_user.END_DATE, fnd_user.DESCRIPTION, fnd_user_resp_groups_all.RESPONSIBILITY_ID, fnd_responsibility.RESPONSIBILITY_KEY, fnd_responsibility.START_DATE, fnd_responsibility.END_DATE
order by fnd_user.user_id";
	
	//Never validated (Did not Pass on Validation Process)
	if($db->Connect($cstr, 'apps', 'apps')){			
 			$rs = $db->Execute("{$orauserqry}");
			
			
	}
	else{
		//echo "$('#confirmDialogs').dialog('close');";
		//echo "$('#confirmDialogs').dialog('destroy');";
		echo "'Failed to connect to Oracle Server/Database!'";	
		var_dump($db->Connect($cstr, 'apps', 'apps'));
		exit();	
	}
//	$workbook->close();
$db->Disconnect();

?>		
	<table id="customerpoints">
		<thead>
		<tr>
			<td>User ID</td>
			<td>User Name</td>
			<td>Start Date</td>
			<td>End Date</td>
			<td>Responsibility</td>
			<td>Description</td>
		</tr>
		</thead>
		<tbody>
			
		<?php	
			
			foreach($rs as $val)
			{
		?>
				<tr>
					<td><?php echo $rs->fields['USER_ID']; ?></td>
					<td><?php echo $rs->fields['USER_NAME']; ?></td>
					<td><?php echo $rs->fields['START_DATE']; ?></td>
					<td><?php echo $rs->fields['END_DATE']; ?></td>
					<td><?php echo $rs->fields['RESPONSIBILITY_KEY']; ?></td>
					<td><?php echo $rs->fields['DESCRIPTION']; ?></td>
				</tr>
		<?php
				/*$qry = "INSERT INTO ORA_GL VALUES('".$rs->fields['DESCRIPTION']."','".$rs->fields['DATE_CREATED']."','".$rs->fields['ATTRIBUTE10']."','".$rs->fields['DEFAULT_EFFECTIVE_DATE']."')";
				$insrt = mssql_query($qry);
				//var_dump($insrt);*/
			}	
			
		?>
		
		</tbody>
		<tfoot>
	</table>
	<a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/ORS/modules/report/users/oracleusers_excel.php" target="_blank" >Download Excel</a>

		<script type='text/javascript'>
        

		$('document').ready(function(){
			$('#customerpoints').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers"
			});
		});
        </script>
        
    </body>
</html>

	