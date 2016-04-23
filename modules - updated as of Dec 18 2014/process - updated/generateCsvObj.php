<?
$now = date('Y-m-d H:i:s');
ini_set("date.timezone","Asia/Manila");
class generateCsvObj extends commonObj {
	
	
	function rebTypLog(){
		$sql = "
		select date,typeId,status from tblLogs where typeId = 1 and date = '{$logDate}' and status = 1
		";
		return $this->getArrRes($this->execQry($sql));
	}
	
	
	//scratch below
	function stsRebTyp(){
		$sql = "
		select REBATE_TYPE_CODE,REBATE_TYPE_DESC,ACTIVE,REBATE_REASON_CODE,REBATE_REASON_DESC from BI_VIEW_REBATE_TYPE
		";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function stsRebTyp___________(){
		$sql = "
		exec tblRtvBatchNo
		";
		return $this->getArrRes($this->execQry($sql));
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