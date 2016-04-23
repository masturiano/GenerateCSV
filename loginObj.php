<?
$now = date('Y-m-d H:i:s');
ini_set("date.timezone","Asia/Manila");
class loginObj extends commonObj {
	
	function loginNotExist($uName,$pWord) {
		$sqlLogin = "SELECT * FROM tblUsers where userName='$uName' and userPass='$pWord' and userStat='A'";
		return $this->execQry($sqlLogin);
	}
	function login($uName,$pWord) {
		$sqlLogin = "SELECT * FROM tblUsers where userName='$uName' and userPass='$pWord' and userStat='A'";
		return $this->getSqlAssoc($this->execQry($sqlLogin));
	}
	
}
?>