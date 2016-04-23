<?
$now = date('Y-m-d H:i:s');
ini_set("date.timezone","Asia/Manila");
class oraToMmsObj extends commonObj {
	
	function arrRtvBatchNo(){
		$sql = "
		exec tblRtvBatchNo
		";
		return $this->getSqlAssoc($this->execQry($sql));
	}
	
	function findBankAcct(){
		$sql = "SELECT DISTINCT bankAccountNum AS bankAccountNum, bankAccountName, CAST(bankAccountName AS varchar) + ' ( ' + CAST(bankAccountNum AS varchar) + ' ) ' AS combBank
FROM         ORS_tblBankAccount
ORDER BY bankAccountName,bankAccountNum";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function findStrComp($storeShort){
	//FROM         sql_mmpgtlib.dbo.TBLSTR
		$sql = "SELECT     STRNUM, STCOMP, STSHRT
FROM         TBLSTR
WHERE     (STSHRT = '$storeShort') AND (STRNAM NOT LIKE 'X%') AND (STSHRT NOT IN ('SBC', 'HO')) AND (STRNUM <= '901')";
		return $this->getSqlAssoc($this->execQry($sql));
	}
	
	function arrResetRtvBatchNo(){
		$sql = "
		SELECT     lastRtvBatch
FROM         ORS_tblRtvBatchNo
		";
		return $this->getSqlAssoc($this->execQry($sql));
	}
	
	function arrResetRtvBatchNoExec(){
		$sql = "
		update ORS_tblRtvBatchNo set lastRtvBatch = '0';
		";
		return $this->execQry($sql);
	}
}
?>