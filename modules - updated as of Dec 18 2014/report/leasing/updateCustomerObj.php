<?
$now = date('Y-m-d H:i:s');
ini_set("date.timezone","Asia/Manila");
class updateCustomerObj extends commonObj {
	
	function cleartblARZMST(){
		$sql = "
		TRUNCATE TABLE ORS_tblARZMST
		";
		return $this->execQry($sql);
	}
	
	function updatetblARZMST(){
		$sql = "
		insert into ORS_tblARZMST(cusnum)
		select cusnum
		from openquery(pgjda, 'select distinct(cusnum) from mmpgtlib.ARZMST WHERE 
		CUSCLS = ''CON''')
		";
		return $this->execQry($sql);
	}
	
	function cleartblCIMCUS(){
		$sql = "
		TRUNCATE TABLE ORS_tblCIMCUS
		";
		return $this->execQry($sql);
	}
	
	function updatetblCIMCUS(){
		$sql = "
		insert into ORS_tblCIMCUS(CUSTOMER_NUMBER,FULL_NAME)
		select CUSTOMER_NUMBER,FULL_NAME from openquery(pgjda, 'select CUSTOMER_NUMBER,FULL_NAME from mmpgtlib.CIMCUS')
		";
		return $this->execQry($sql);
	}
}
?>