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
include("Obj.php");
include "adodb/adodb.inc.php";
require_once('Spreadsheet/Excel/Writer.php');
//end include

//
echo "Do Not Close ... Please Wait... Inserting Data From Oracle...";

//session variable
$company = $_GET['company'];
$dfrm = strtotime(trim($_GET['dfrm']));
$dto = strtotime(trim($_GET['dto']));

$orafrm = substr(date('F',$dfrm),0,3)."-".substr(date('Y',$dfrm),-2);
$orato = substr(date('F',$dto),0,3)."-".substr(date('Y',$dto),-2);

//GETDATE
// $dfrmx = date('d',strtotime($_GET['dfrm']))."-".substr(date('F',strtotime($_GET['dfrm'])),0,3)."-".date('Y',strtotime($_GET['dfrm']));
// $dtox = date('d',strtotime($_GET['dto']))."-".substr(date('F',strtotime($_GET['dto'])),0,3)."-".date('Y',strtotime($_GET['dto']));

//end session variable

//convert company to letters


if($company=='85'){
$companydesc = 'PG';
$excld = "DESCRIPTION NOT LIKE '700%'";
$glmpc = "(GLMCMP IN (''101'',''102'',''103'',''104'',''105'',''301''))";
}
else{
$companydesc = 'PJ';
$excld = "DESCRIPTION LIKE '700%'";
$glmpc = "GLMCMP =''700'' ";
}
//end convert company to letters

//convert time
//$strtotimeFrom =   strtotime($datefrom) - 86400;
//$strtotimeTo =   strtotime($dateto ) + 86400;

//$strtotimeTo =   strtotime($dateto );
//end convert time

########## EXCEL ##########

if($company!=""){
	
	$db = NewADOConnection("oci8");
	$host = "192.168.200.136"; // PROD
	//$host = "192.168.200.135"; // NEW UAT
	$port = "1521"; // PROD
	//$port = "1532"; // NEW UAT
	$sid = "PROD"; // PROD
	//$sid = "NPROD"; // NEW UAT
	$cstr = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port))
				(CONNECT_DATA=(SID=$sid)))";
	
	//Never validated (Did not Pass on Validation Process)
	if($db->Connect($cstr, 'apps', 'apps')){			
 			$rs = $db->Execute("select SUBSTR(SUBSTR(NAME,0,12),-8) as DESCRIPTION,DATE_CREATED,ATTRIBUTE10,DEFAULT_EFFECTIVE_DATE from gl_je_headers where je_category = '2'
AND (PERIOD_NAME = '{$orafrm}' OR PERIOD_NAME = '{$orato}') and NAME like '%SA%' AND ATTRIBUTE10 LIKE '{$companydesc}%'");
	
	//$cnt=1;
/*		if($rs!=''){
			  foreach ($rs as $valRs) {
			  	//echo $rs->fields['INVOICE_ID']."\n";
				//echo $rs->fields['Attribute11']."gfgf\n";
				//$worksheet->write(5,1,$rs->fields['Attribute11'],$headerFormat);
			//$cnt++;
				}
		}
		else{
			echo "alert('gdf');";
		}*/
	

	
	}
	else{
		//echo "$('#confirmDialogs').dialog('close');";
		//echo "$('#confirmDialogs').dialog('destroy');";
		echo "'Failed to connect to Oracle Server/Database!'";	
		
	}
//	$workbook->close();
$db->Disconnect();
}

?>
<?php


//sql connection
$h="192.168.200.226";
$u="sa";  
$p="";          
$d="ORA";      
$link = mssql_connect($h, $u, $p) or die("Could not connect");
$db = mssql_select_db($d, $link) or die("Could not select database");


//end sql connection

mssql_query('TRUNCATE TABLE ORA_GL');
foreach($rs as $val){
		
		$qry = "INSERT INTO ORA_GL VALUES('".$rs->fields['DESCRIPTION']."','".$rs->fields['DATE_CREATED']."','".$rs->fields['ATTRIBUTE10']."','".$rs->fields['DEFAULT_EFFECTIVE_DATE']."')";
		$insrt = mssql_query($qry);
		//var_dump($insrt);
	}	

//SA with no pair
$npair = "SELECT ORA_GL.SABATCH, ORA_GL.COMPANY
FROM ORA_GL
WHERE ORA_GL.SABATCH IN 
(SELECT ORA_GL.SABATCH
FROM ORA_GL
GROUP BY ORA_GL.SABATCH
HAVING count(ORA_GL.SABATCH) <= 1)
ORDER BY ORA_GL.COMPANY";
$npair = mssql_query($npair);

$np_cntnt = "";

while($np = mssql_fetch_array($npair))
{
	$np_cntnt .= $np['SABATCH']."|".$np['COMPANY']."\n";
}	
	
$filen = "SA_NO_PAIR_".date('mdy')."_".date('his').".txt";	
$fromFileName="./EXPORTEDFILE/".$filen; 
if (file_exists($fromFileName)) {
	unlink($fromFileName);
}
$handleFromFileName = fopen ($fromFileName, "x");

fwrite($handleFromFileName, $np_cntnt);
fclose($handleFromFileName) ;

//end SA with no pair


$nl_frm = date('ymd',$dfrm);
$nl_to = date('ymd',$dto);	

//SA Not Loaded

//set ansi nulls and ansi warnings
mssql_query("SET ANSI_NULLS ON");
mssql_query("SET ANSI_WARNINGS ON");

$nlsa = "SELECT MMS_GL.GLTBCH,MMS_GL.GLMCMP,MMS_GL.GLTDAT
FROM 
(select GLTDAT,GLMCMP,GLTBCH from openquery(pgjda,'SELECT * FROM mmpgtlib.GLYEAR WHERE  (GLTDAT BETWEEN ''{$nl_frm}'' AND ''{$nl_to}'') AND {$glmpc} AND GLTSRC=''S/A'' ')) as MMS_GL 
WHERE MMS_GL.GLTBCH NOT IN (
SELECT ORA_GL.SABATCH FROM ORA_GL
GROUP BY ORA_GL.SABATCH
)
GROUP BY MMS_GL.GLTBCH,MMS_GL.GLTDAT,MMS_GL.GLMCMP";

//echo $nlsa;

$nlsa = mssql_query($nlsa);
var_dump($nlsa);
$nl_cntnt = "";

while($nl = mssql_fetch_array($nlsa))
{
	$nl_cntnt .= $nl['GLTBCH']."|".$nl['GLMCMP']."|".$nl['GLTDAT']."\n";
}
	
$filen = "SA_NO_LOADED_".date('mdy')."_".date('his').".txt";	
$fromFileName="./EXPORTEDFILE/".$filen; 
if (file_exists($fromFileName)) {
	unlink($fromFileName);
}
$handleFromFileName = fopen ($fromFileName, "x");

fwrite($handleFromFileName, $nl_cntnt);
fclose($handleFromFileName) ;

//end SA Not Loaded	


//SA loaded More than Once
$smone = "SELECT ORA_GL.SABATCH, ORA_GL.COMPANY
FROM ORA_GL
WHERE ORA_GL.SABATCH IN 
(SELECT ORA_GL.SABATCH
FROM ORA_GL
GROUP BY ORA_GL.SABATCH
HAVING count(ORA_GL.SABATCH) > 2)
ORDER BY ORA_GL.COMPANY";
$smone = mssql_query($smone);

$mo_cntnt = "";

while($np = mssql_fetch_array($smone))
{
	$mo_cntnt .= $np['SABATCH']."|".$np['COMPANY']."\n";
}	
	
$filen = "SA_LOADEDTWICE_".date('mdy')."_".date('his').".txt";	
$fromFileName="./EXPORTEDFILE/".$filen; 
if (file_exists($fromFileName)) {
	unlink($fromFileName);
}
$handleFromFileName = fopen ($fromFileName, "x");

fwrite($handleFromFileName, $mo_cntnt);
fclose($handleFromFileName) ;

//end SA loaded More than Once


	
	
echo "You Can Now close this Window...";
?>