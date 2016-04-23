<?
$now = date('Y-m-d H:i:s');
ini_set("date.timezone","Asia/Manila");
class leasingSetupObj extends commonObj {

	function viewConCus() {
			
			$sql="
			select cusnum from ORS_tblARZMST where cusnum = cusNum
			";
			return $this->getArrRes($this->execQry($sql));
	}
		
	function leasingSetup($dteFrom,$dteTo,$cusNum,$company) {
		
		/*
		$sql="
		select
		ORG_ID,ACCOUNT_NUMBER,ACCOUNT_NAME,LOCATION,TRX_NUMBER,TRX_DATE,AMOUNT_DUE_ORIGINAL,AMOUNT_DUE_REMAINING,CLASS,CLASS_NAME
		from ORS_tblLeasingSetup
		where TRX_DATE between '{$dteFrom}' and '{$dteTo}'
		";*/
		if($cusNum != '')
		{
			$filterCusNum = "AND HZ_CUST_ACCOUNTS.ACCOUNT_NUMBER = ''{$cusNum}''";
		}
		else{
			$filterCusNum = "";
		}
		
		if($company == "0"){
			$orgId = "85,87,133";
		}
		else
		{
			$orgId = $company;
		}
		
		
		$sql="
		select	ORAPROD.ORG_ID,ORAPROD.ACCOUNT_NUMBER,ORAPROD.ACCOUNT_NAME,ORAPROD.LOCATION,ORAPROD.TRX_NUMBER,
				ORAPROD.TRX_DATE,ORAPROD.AMOUNT_DUE_ORIGINAL,ORAPROD.AMOUNT_DUE_REMAINING,ORAPROD.CLASS,ORAPROD.NAME
		from 
			openquery(ORAPROD,'
				SELECT DISTINCT ar_payment_schedules_all.ORG_ID,
				HZ_CUST_ACCOUNTS.ACCOUNT_NUMBER,
				HZ_CUST_ACCOUNTS.ACCOUNT_NAME,
				HZ_CUST_SITE_USES_ALL.LOCATION,
				ra_customer_trx_all.TRX_NUMBER,
				ar_payment_schedules_all.TRX_DATE,
				ar_payment_schedules_all.AMOUNT_DUE_ORIGINAL,
				ar_payment_schedules_all.AMOUNT_DUE_REMAINING,
				ar_payment_schedules_all.CLASS,
				hz_cust_profile_classes.NAME
				FROM ar_payment_schedules_all
				inner JOIN ra_customer_trx_all
				ON ar_payment_schedules_all.CUSTOMER_TRX_ID = ra_customer_trx_all.CUSTOMER_TRX_ID
				INNER JOIN HZ_CUST_ACCOUNTS
				ON ar_payment_schedules_all.CUSTOMER_ID = HZ_CUST_ACCOUNTS.CUST_ACCOUNT_ID
				INNER JOIN HZ_CUST_SITE_USES_ALL
				ON ar_payment_schedules_all.CUSTOMER_SITE_USE_ID = HZ_CUST_SITE_USES_ALL.SITE_USE_ID
				LEFT JOIN hz_customer_profiles
				ON HZ_CUST_ACCOUNTS.CUST_ACCOUNT_ID               = hz_customer_profiles.CUST_ACCOUNT_ID
				AND ar_payment_schedules_all.CUSTOMER_SITE_USE_ID = hz_customer_profiles.SITE_USE_ID
				LEFT JOIN hz_cust_profile_classes
				ON hz_cust_profile_classes.PROFILE_CLASS_ID = hz_customer_profiles.PROFILE_CLASS_ID
				WHERE ar_payment_schedules_all.ORG_ID IN ({$orgId})
				{$filterCusNum}
				and ar_payment_schedules_all.TRX_DATE between ''{$dteFrom}'' and ''{$dteTo}''
				and ar_payment_schedules_all.CLASS = ''INV''
				order by 
				ar_payment_schedules_all.ORG_ID,
				HZ_CUST_ACCOUNTS.ACCOUNT_NUMBER,
				HZ_CUST_SITE_USES_ALL.LOCATION,
				ar_payment_schedules_all.TRX_DATE 
			') ORAPROD
		";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function findCustomer($term){
		$sql = "select cusnum from ORS_tblARZMST
				WHERE     (cusnum like '%$term%') 
				order by cusnum
				";
		/*$sql = "SELECT     TOP 10 CAST(INUMBR AS nvarchar(50)) + ' - ' + IDESCR AS combSkuDesc,
				CAST(INUMBR AS varchar) AS 				INUMBR
				FROM         tblsku
				WHERE     (INUMBR LIKE '%$term%') 
				";*/

		return $this->getArrRes($this->execQry($sql));
	}
	
	function checkCustomerNumber($cusNum){
		$sql = "
		select cusnum from ORS_tblARZMST where cusnum = cusNum
		";
		return $this->getRecCount($this->execQry($sql));
	}
}
?>