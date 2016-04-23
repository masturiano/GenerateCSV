<?
$now = date('Y-m-d H:i:s');
ini_set("date.timezone","Asia/Manila");
class updateBankAcctObj extends commonObj {
	
	function cleartblBankAccount(){
		$sql = "
		TRUNCATE TABLE    ORS_tblBankAccount
		";
		return $this->execQry($sql);
	}
	
	function updateBankAcct($content){
		$sql = "
		insert into ORS_tblBankAccount(bankAccountNum,bankAccountName) values(".trim($content).")
		";
		return $this->execQry($sql);
	}
}
?>