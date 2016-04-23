<?
$now = date('Y-m-d H:i:s');
ini_set("date.timezone","Asia/Manila");
class uploaderObj extends commonObj {
	
	//,$loc_code,$cusnum
	function cleartblTxtfileTemp(){
		$sql = "
		TRUNCATE TABLE    tblTxtfileTemp
		";
		return $this->execQry($sql);
	}
	function cleartblTxtfileTempDup(){
		$sql = "
		TRUNCATE TABLE    tblTxtfileTempDup
		";
		return $this->execQry($sql);
	}
	function checkIfDuplicate($store,$transDate,$custNum,$invNum){
		$sql = "
		SELECT     loc_code, tran_date, cusnum, mms_invnum
		FROM         POS_TRAN_SUMM
		WHERE     loc_code = '$store' and tran_date = '$transDate' and cusnum = '$custNum' and mms_invnum = '$invNum'
		";
		return $this->getRecCount($this->execQry($sql));
	}
	function inserttblTxtfileTemp($store,$transDate,$custNum,$invNum,$grossAmt,$fc,$ewt,$creditCardNum,$seqNo){
		$sql = "
		INSERT INTO tblTxtfileTemp(store,transDate,custNum,invNum,grossAmt,fc,ewt,creditCardNum,seqNo) VALUES('".$store."','".$transDate."','".$custNum."','".$invNum."',".$grossAmt.",".$fc.",".$ewt.",'".$creditCardNum."','".$seqNo."')
		";
		return $this->execQry($sql);
	}
	function inserttblTxtfileTempDup($store,$transDate,$custNum,$invNum,$grossAmt,$fc,$ewt,$creditCardNum,$seqNo){
		$sql = "
		INSERT INTO tblTxtfileTempDup(store,transDate,custNum,invNum,grossAmt,fc,ewt,creditCardNum,seqNo) VALUES('".$store."','".$transDate."','".$custNum."','".$invNum."',".$grossAmt.",".$fc.",".$ewt.",'".$creditCardNum."','".$seqNo."')
		";
		return $this->execQry($sql);
	}
	function insertHeader($batfname){
		$sql = "
		INSERT INTO POS_TRAN_SUMM
		SELECT     transDate, store, custNum, invNum, SUM(ewt) AS totEwtAmt, SUM(fc) AS totFcAmt, SUM(grossAmt) AS totGrossAmt,'".$batfname."' AS batfname, NULL AS Expr1, NULL 
							  AS Expr2, NULL AS Expr3, NULL AS Expr4, NULL AS Expr5
		FROM         tblTxtfileTemp
		GROUP BY transDate, store, custNum, invNum
		";
		return $this->execQry($sql);
	}
	function insertDetails(){
		$sql = "
		INSERT INTO POS_TRAN_DTL
SELECT     POS_TRAN_SUMM.pos_tran_id AS pos_tran_id, tblTxtfileTemp.invNum AS invNum, tblTxtfileTemp.creditCardNum AS creditCardNum, 
                      tblTxtfileTemp.grossAmt AS grossAmt, tblTxtfileTemp.fc AS fc, tblTxtfileTemp.ewt AS ewt, 0 AS tot_pay_amt, 0 AS tot_ewt_amt, tblTxtfileTemp.seqNo AS seqNo, NULL 
                      AS exf1, NULL AS exf2, NULL AS exf3, NULL AS exf4, NULL AS exf5
FROM         POS_TRAN_SUMM RIGHT OUTER JOIN
                      tblTxtfileTemp ON POS_TRAN_SUMM.tran_date = tblTxtfileTemp.transDate AND POS_TRAN_SUMM.loc_code = tblTxtfileTemp.store AND 
                      POS_TRAN_SUMM.cusnum = tblTxtfileTemp.custNum AND POS_TRAN_SUMM.mms_invnum = tblTxtfileTemp.invNum
		";
		return $this->execQry($sql);
	}
	function displayDuplicate($store,$transDate,$custNum,$invNum){
		$sql = "
		SELECT  store,transDate,custNum,invNum
		FROM         tblTxtfileTempDup
		GROUP BY store,transDate,custNum,invNum
		";
		return $this->getArrRes($this->execQry($sql));
		//WHERE     store = '$store' and transDate = '$transDate' and custNum = '$custNum' and invNum = '$invNum'
	}
	//	return $this->getArrRes($this->execQry($sql));
}
?>