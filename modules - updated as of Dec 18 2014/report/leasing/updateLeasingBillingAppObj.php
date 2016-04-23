<?
$now = date('Y-m-d H:i:s');
ini_set("date.timezone","Asia/Manila");
class updateLeasingBillingAppObj extends commonObj {
	
	function viewConCustomer() {
		
		$sql="
		select distinct(cusnum) as cusnum from ORS_tblARZMST order by cusnum
		";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function oracleData() {
		
		$arrConCustomer = $this->viewConCustomer();
		
		$turnOnAnsiNulls = "SET ANSI_NULLS ON";
		$turnOnAnsiWarn = "SET ANSI_WARNINGS ON";
		
		$truncTable = "truncate table ORS_tblLeasingBillingApp";
		
		if($this->execQry($truncTable)){
		
			//foreach($arrConCustomer as $valConCustomer){
				$sql="
				insert into ORS_tblLeasingBillingApp (RECEIVABLE_APPLICATION_ID,CREATION_DATE,GL_DATE,APPLY_DATE,APPLICATION_TYPE,STATUS,AMOUNT_APPLIED,APPLIED_CUSTOMER_TRX_ID,CASH_RECEIPT_ID)
				select	ORAPROD.receivable_application_id,ORAPROD.creation_date,ORAPROD.gl_date,ORAPROD.apply_date,ORAPROD.application_type,
					ORAPROD.status,ORAPROD.amount_applied,ORAPROD.applied_customer_trx_id,ORAPROD.cash_receipt_id
				from 
					openquery(ORAPROD,'
						select receivable_application_id,creation_date,gl_date,apply_date,application_type,status,amount_applied,applied_customer_trx_id,cash_receipt_id
						from ar_receivable_applications_all
						where 
						applied_customer_trx_id is not null
						and creation_date between to_date(''2014-01-01'') and to_date(''2014-09-18'')
					') ORAPROD
				";
				$this->execQry($turnOnAnsiNulls);
				$this->execQry($turnOnAnsiWarn);
				$this->execQry($sql);
			//}
			//CASH_RECEIPT_ID in  (3816553)
		}
	}
}
?>