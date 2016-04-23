<?
class commonObj extends dbHandler {
	
	function GetModules() {
		$sqlModules = "SELECT distinct moduleName,menuOrder FROM tblMenu where moduleStat='A' order by menuOrder";
		return $this->getArrRes($this->execQry($sqlModules));
	}
	function GetSubModules($moduleName) {
		$sqlSubModules = "SELECT moduleId,label,page FROM tblMenu where moduleStat='A' AND moduleName ='$moduleName' order by moduleOrder ";
		return $this->getArrRes($this->execQry($sqlSubModules));
	}
	function Login($uname,$pword) {
		$pword = base64_encode($pword);
		$sqlLogin = "SELECT * FROM tblUsers where userName='$uname' and userPass='$pword' and userStat='A'";
		return $this->getSqlAssoc($this->execQry($sqlLogin));
	}
	function CleanStr($str) {
		return str_replace("'","''",stripslashes(strtoupper($str)));
	}
	function AddTransNo($table,$field,$company) {
		$sqlUpdate = "Update $table set $field=$field+1 WHERE compCode = '$company'";
		$Trns = $this->beginTran();
		if ($Trns) {
			$Trns = $this->execQry($sqlUpdate);
		}
		if(!$Trns){
			$Trns = $this->rollbackTran();
		}
		else{
			$Trns = $this->commitTran();
		}
		$sqlTransNo = "Select $field from $table WHERE compCode = '$company'";
		$arrRes = $this->getSqlAssoc($this->execQry($sqlTransNo));
		return $arrRes["$field"];
	}
	function getEventTypes() {
		$sqlEventTypes = "SELECT typeCode,typeDesc FROM tblEventTypes where typeStat='A' order by typeDesc ";
		return $this->getArrRes($this->execQry($sqlEventTypes));
	}
	function makeArr($arrRes,$index,$value,$default){

		$arrResult = array();
		if(!empty($default)){
			$arrResult[0] = $default;
		}
		else{
			$arrResult[0] = "";
		}
		
		foreach ($arrRes as $arrVal){
			$arrResult[$arrVal[$index]] = $arrVal[$value];
		}
		return $arrResult;
	}
	function getCompanies() {
		$sqlCompany = "SELECT * FROM tblCompany WHERE (compStat = 'A') order by compName";
		return $this->getArrRes($this->execQry($sqlCompany));
	}
	
	function getBranches($compCode="") {
		$sqlBranches = "SELECT * FROM tblBranches WHERE (brnStat = 'A') and compCode like '%$compCode%' order by brnDesc";
		return $this->getArrRes($this->execQry($sqlBranches));
	}
	function getCompBranches($compCode="") {
		$sqlBranches = "SELECT * FROM tblBranches WHERE (brnStat = 'A') and compCode like '%$compCode%' order by brnShortDesc";
		return $this->getArrRes($this->execQry($sqlBranches));
	}	
	function getCompanyName() {
		$sqlCompany = "SELECT compName FROM tblCompany WHERE (compStat = 'A') and compCode='{$_SESSION['compCode']}'";
		$arr =  $this->getSqlAssoc($this->execQry($sqlCompany));
		return strtoupper($arr['compName']);
	}
	
	function getDepartments() {
		$sqlDepartments = "SELECT * FROM tblDepartment WHERE (deptStat = 'A') order by deptShortDesc";
		return $this->getArrRes($this->execQry($sqlDepartments));
	}
	function getEventBranches($EventNo) {
		$sqlBranches = "SELECT * FROM tblBranches WHERE (brnStat = 'A') and strCode Not IN (Select strCode from tblParticipants where eventNo='$EventNo' ) order by brnDesc";
		return $this->getArrRes($this->execQry($sqlBranches));
	}	
	
	function getNatureInfo($expCode) {
		$sqlNature = "Select * from tblExpenseNature where expCode = '$expCode'";
		return $this->getSqlAssoc($this->execQry($sqlNature));
	}
	function getSuppName($suppCode){
		$sql = "SELECT * FROM tblsuppliers WHERE suppCode = '$suppCode'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function currentDate() {
		$gmt = time() + (8 * 60 * 60);
		$newdate = date("m/d/Y h:iA", $gmt);
		return $newdate;
	}
	function getuserPages($userId) {
		$sqlPages ="Select pages from tblusers where userId='$userId'";
		$res = $this->getSqlAssoc($this->execQry($sqlPages));
		return explode(",",$res['pages']);	
	}	
	function getAllCompany(){
		$sql = "SELECT * FROM tblcompany where compStat is NULL or compStat = 'A'";	
		return $this->getArrRes($this->execQry($sql));	
	}
	function getProductGrp(){
		$sql = "SELECT * FROM tblProdGrp WHERE prodStat = 'A'";
		return $this->getArrRes($this->execQry($sql));	
	}
	function getStsInfo($refNo){
		$sql = "SELECT * FROM tblStsHdr where stsRefno = '$refNo'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
}
?>