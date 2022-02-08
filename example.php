<?php
// Use this class to deserialize error caught
use XeroAPI\XeroPHP\AccountingObjectSerializer;
use XeroAPI\XeroPHP\PayrollAuObjectSerializer;
use XeroAPI\XeroPHP\FinanceObjectSerializer;

class ExampleClass
{
	public $apiInstance;

	function __construct() {
   	}

   	public function init($arg) {
		$apiInstance = $arg;
   	}


/*
PAYROLL AU APIs
Following methods demonstrate Xero's 
AU Payroll API endpoints
https://raw.githubusercontent.com/XeroAPI/Xero-OpenAPI/master/accounting-yaml/xero_accounting.yaml 
*/
public function getPayrollAuEmployees($xeroTenantId,$payrollAuApi,$returnObj=false)
{
	$str = '';
	
//[PayrollAuEmployee:Read]
$result = $payrollAuApi->getEmployees($xeroTenantId);
//[/PayrollAuEmployee:Read]

	if($returnObj) {
		return $result;
	} else {
		$str = $str . "Get all employees total: " . count($result->getEmployees()) . "<br>";
		return $str;
	}
}

public function createPayrollAuEmployees($xeroTenantId,$payrollAuApi,$returnObj=false)
{
	$str = '';
	
//[PayrollAuEmployee:Create]
$employee = new XeroAPI\XeroPHP\Models\PayrollAu\Employee;
$employee->setFirstName("Fred");
$employee->setLastName("Potter");
$employee->setEmail("albus@hogwarts.edu");
$dateOfBirth = DateTime::createFromFormat('m/d/Y', '05/29/2000');
$employee->setDateOfBirthAsDate($dateOfBirth);

$address = new XeroAPI\XeroPHP\Models\PayrollAu\HomeAddress;
$address->setAddressLine1("101 Green St");
$address->setCity("Island Bay");
$address->setRegion(\XeroAPI\XeroPHP\Models\PayrollAu\State::NSW);
$address->setCountry("AUSTRALIA");
$address->setPostalCode("6023");
$employee->setHomeAddress($address);

$newEmployees = [];		
array_push($newEmployees, $employee);

$result = $payrollAuApi->createEmployee($xeroTenantId, $newEmployees);
	//[/PayrollAuEmployee:Create]

	if($returnObj) {
		return $result;
	} else {
		$str = $str . "Created employee: " . $result->getEmployees()[0]->getFirstName() . "<br>";
		return $str;
	}
}



public function createPayrollAuLeaveApplications($xeroTenantId,$payrollAuApi,$returnObj=false)
{
	$str = '';
	$employee = $this->createPayrollAuEmployees($xeroTenantId, $payrollAuApi, true); 
	$employeeId = $employee->getEmployees()[0]->getEmployeeId();
	$leaveapplications = $payrollAuApi->getLeaveApplications($xeroTenantId);
	$leaveTypeId = $leaveapplications->getLeaveApplications()[0]->getLeaveTypeId(); 
//[PayrollAuLeaveApplication:Create]
$leaveapplication = new XeroAPI\XeroPHP\Models\PayrollAu\LeaveApplication;
$leaveapplication->setDescription("Fred");
$leaveapplication->setEmployeeID($employeeId);
$leaveapplication->setLeaveTypeID($leaveTypeId);
$leaveapplication->setStartDateAsDate(new DateTime('2020-05-02'));
$leaveapplication->setEndDateAsDate(new DateTime('2020-05-12'));

$arr_leaveapplications = [];		
array_push($arr_leaveapplications, $leaveapplication);
try {
	$result = $payrollAuApi->createLeaveApplication($xeroTenantId, $arr_leaveapplications);
	$str = $str . "Created leave application: " . $result[0]->getLeaveApplicationId() . "<br>";
} catch (\XeroAPI\XeroPHP\ApiException $e) {
	$error = PayrollAuObjectSerializer::deserialize($e->getResponseBody(), '\XeroAPI\XeroPHP\Models\PayrollAu\APIException',[]);
	$str = "ApiException - " . $error->getMessage();
}
	//[/PayrollAuLeaveApplication:Create]

	
	if($returnObj) {
		return $result;
	} else {
		return $str;
	}
}



/*
IDENTITY APIs
Following methods demonstrate Xero's 
Accounting API endpoints
https://raw.githubusercontent.com/XeroAPI/Xero-OpenAPI/master/accounting-yaml/xero_accounting.yaml 
*/
	public function deleteConnection($xeroTenantId,$identityApi,$returnObj=false)
	{
		$str = '';
		

//[Connection:Delete]
$connections = $identityApi->getConnections();
$id = $connections[0]->getId();
$result = $identityApi->deleteConnection($id);
//[/Connection:Delete]

		if($returnObj) {
			return $result;
		} else {
			$str = $str . "Organisation connection  deleted<br>";
			return $str;
		}
	}

	public function getConnections($identityApi,$returnObj=false)
	{
		$str = '';


//[Connections:Read]
		$result = $identityApi->getConnections();
//[/Connections:Read]

		if ($returnObj) {
			return $result;
		} else {
			$str = $str . "Connected tenants " . array_reduce($result, function ($carry, $item) {#
					return trim($carry . ' ' . $item->getTenantId() . ' = "'. $item->getTenantName() . '"');
				}) . '<br>';
			return $str;
		}
	}

	/*
    ACCOUNTING APIs
    Following methods demonstrate Xero's
    Accounting API endpoints
    https://raw.githubusercontent.com/XeroAPI/Xero-OpenAPI/master/accounting-yaml/xero_accounting.yaml
    */
   public function getAccount($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$where = 'Status=="ACTIVE"';
		$accounts = $apiInstance->getAccounts($xeroTenantId, null, $where); 
		$accountId = $accounts->getAccounts()[0]->getAccountId();
//[Account:Read]
$result = $apiInstance->getAccount($xeroTenantId,$accountId); 						
//[/Account:Read]

		if($returnObj) {
			return $result;
		} else {
			$str = $str . "Get specific Account: " . $result->getAccounts()[0]->getName() . "<br>";
			$str = $str . "Get Account Updated Date: " . $result->getAccounts()[0]->getUpdatedDateUtcAsDate()->format('Y-m-d H:i:s') . "<br>";
			return $str;
		}
	}

	public function createAccount($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Account:Create]
$account = new XeroAPI\XeroPHP\Models\Accounting\Account;
$account->setCode($this->getRandNum());
$account->setName("Foo" . $this->getRandNum());
$account->setType("EXPENSE");
$account->setDescription("Hello World");	
$result = $apiInstance->createAccount($xeroTenantId,$account); 
//[/Account:Create]
		
		$str = $str ."Create Account: " . $result->getAccounts()[0]->getName() . "<br>";
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateAccount($xeroTenantId,$apiInstance)
	{
		$str = '';

		$new = $this->createAccount($xeroTenantId,$apiInstance,true);
		$accountId = $new->getAccounts()[0]->getAccountId();								
					
//[Account:Update]
$account = new XeroAPI\XeroPHP\Models\Accounting\Account;
$account->setStatus(NULL);
$account->setDescription("Goodbye World");	
$result = $apiInstance->updateAccount($xeroTenantId,$accountId,$account);  
//[/Account:Update]

		$str = $str . "Update Account: " . $result->getAccounts()[0]->getName() . "<br>" ;

		return $str;
	}

	public function archiveAccount($xeroTenantId,$apiInstance)
	{
		$str = '';

		$new = $this->createAccount($xeroTenantId,$apiInstance,true);
		$accountId = $new->getAccounts()[0]->getAccountId();								
		
//[Account:Archive]
$account = new XeroAPI\XeroPHP\Models\Accounting\Account;
$account->setStatus("ARCHIVED");	
$result = $apiInstance->updateAccount($xeroTenantId,$accountId,$account);  
//[/Account:Archive]

		$str = $str . "Archive Account: " . $result->getAccounts()[0]->getName() . "<br>" ;

		return $str;
	}

	public function deleteAccount($xeroTenantId,$apiInstance)
	{
		$str = '';

		$new = $this->createAccount($xeroTenantId,$apiInstance,true);
		$accountId = $new->getAccounts()[0]->getAccountId();								
		 				
//[Account:Delete]
$result = $apiInstance->deleteAccount($xeroTenantId,$accountId);
//[/Account:Delete]

		$str = $str . "Deleted Account: " . $result->getAccounts()[0]->getName() . "<br>" ;
		return $str;
	}


	public function attachmentAccount($xeroTenantId,$apiInstance)
	{
		$str = '';

		$account = $this->getAccounts($xeroTenantId,$apiInstance,true);
//[Account:Attachment]
$guid = $account->getAccounts()[2]->getAccountId();
		
$filename = "./helo-heros.jpg";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
fclose($handle);

$result = $apiInstance->createAccountAttachmentByFileName($xeroTenantId,$guid,"helo-heros.jpg",$contents);
//[/Account:Attachment]
		$str =  "Account (". $result->getAttachments()[0]->getFileName() .") attachment url:";
		$str = $str . $result->getAttachments()[0]->getUrl();

		return $str;
	}

	public function getAccountAttachmentById($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';
		// Create new attachment on an account
		$account = $this->getAccounts($xeroTenantId,$apiInstance,true);
		$accountId = $account->getAccounts()[0]->getAccountId();
		$filename = "./helo-heros.jpg";
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		$new = $apiInstance->createAccountAttachmentByFileName($xeroTenantId,$accountId,"helo-heros.jpg",$contents);
		
		// Get attachments list
		$attachments = $apiInstance->getAccountAttachments($xeroTenantId,$accountId); 			
		$attachmentId = $attachments->getAttachments()[0]->getAttachmentId();
		$contentType = $attachments->getAttachments()[0]->getMimeType();
		$savedFileName = $attachments->getAttachments()[0]->getFileName();
		
//[Account:AttachmentById]

// get a specific attachment for this account
$result = $apiInstance->getAccountAttachmentById($xeroTenantId, $accountId, $attachmentId,$contentType); 

// read attachment contents
$content = $result->fread($result->getSize());

//check if a temp dir exsits
$dir_to_save = "./temp/";
if (!is_dir($dir_to_save)) {
  mkdir($dir_to_save);
}
// write to temp dir
file_put_contents($dir_to_save . $savedFileName , $content);
//[/Account:AttachmentById]
		
		$str = $str . "Account attachment saved: " . $savedFileName . " in the temp folder<br>";

		if($returnObj) {
			return $result->getInvoices()[0];
		} else {
			return $str;
		}
	}	

	public function getAccounts($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';
//[Accounts:Read]
// read all
$result = $apiInstance->getAccounts($xeroTenantId); 						

// filter for only active
$where = 'Status=="ACTIVE"';
$result2 = $apiInstance->getAccounts($xeroTenantId, null, $where); 
//[/Accounts:Read]

		if($returnObj) {
			return $result;
		} else {
			$str = $str . "Get accounts total: " . count($result->getAccounts()) . "<br>";
			$str = $str . "Get ACTIVE accounts total: " . count($result2->getAccounts()) . "<br>";
			return $str;
		}
	}

	public function getBankTransaction($xeroTenantId,$apiInstance)
	{	
		$str = '';
		$new = $this->createBankTransactions($xeroTenantId,$apiInstance,true);
		$banktransactionId = $new->getBankTransactions()[0]->getBankTransactionId();

//[BankTransaction:Read]
$result = $apiInstance->getBankTransactions($xeroTenantId, $banktransactionId); 						
//[/BankTransaction:Read]

		$str = $str . "Get specific BankTransaction Total: " . $result->getBankTransactions()[0]->getTotal() . "<br>";	
		return $str;
	}

	public function updateBankTransaction($xeroTenantId,$apiInstance)
	{
		$str = '';

		$new = $this->createBankTransactions($xeroTenantId,$apiInstance,true);
		$banktransactionId = $new->getBankTransactions()[0]->getBankTransactionId();

//[BankTransaction:Update]
$banktransaction = new XeroAPI\XeroPHP\Models\Accounting\BankTransaction;
$banktransaction->setReference("Goodbye World");
$result = $apiInstance->updateBankTransaction($xeroTenantId,$banktransactionId,$banktransaction);
//[/BankTransaction:Update]

		$str = $str . "Updated Bank Transaction: " . $result->getBankTransactions()[0]->getReference();

		return $str;
	}

	public function deleteBankTransaction($xeroTenantId,$apiInstance)
	{
		$account = $this->getBankAccount($xeroTenantId,$apiInstance,true);

		if (count((array)$account)) {
			$str = '';
			
			$new = $this->createBankTransactions($xeroTenantId,$apiInstance,true);
			$banktransactionId = $new->getBankTransactions()[0]->getBankTransactionId();

//[BankTransaction:Delete]
$banktransaction = new XeroAPI\XeroPHP\Models\Accounting\BankTransaction;
$banktransaction->setStatus(XeroAPI\XeroPHP\Models\Accounting\BankTransaction::STATUS_DELETED);
$result = $apiInstance->updateBankTransaction($xeroTenantId,$banktransactionId,$banktransaction);  
//[/BankTransaction:Delete]

			$str = $str . "Deleted Bank Transaction";

		} else {
			$str = $str . "No Bank Account Found - can't work with Transactions without it.";
		}
	
		return $str;
	}

	public function getBankTransactions($xeroTenantId,$apiInstance)
	{	
		$str = '';
//[BankTransactions:Read]
// read all bank transactions
$result = $apiInstance->getBankTransactions($xeroTenantId); 						

// filter for only authorised bank transactions
$where = 'Status=="AUTHORISED"';
$result2 = $apiInstance->getBankTransactions($xeroTenantId, null, $where); 
//[/BankTransactions:Read]

		$str = $str . "Get BankTransaction total: " . count($result->getBankTransactions()) . "<br>";
		$str = $str . "Get ACTIVE BankTransaction total: " . count($result2->getBankTransactions()) . "<br>";
		
		return $str;
	}

	public function createBankTransactions($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();
		$getAccount = $this->getBankAccount($xeroTenantId,$apiInstance,true);
		$code = $getAccount->getAccounts()[0]->getCode();
		$accountId = $getAccount->getAccounts()[0]->getAccountId();
		$lineitem = $this->getLineItem();
		$lineitems = [];
		array_push($lineitems, $lineitem);

//[BankTransactions:Create]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactId($contactId);

$bankAccount = new XeroAPI\XeroPHP\Models\Accounting\Account;
$bankAccount->setCode($code)
            ->setAccountId($accountId);

$lineitems = [];		
array_push($lineitems, $lineitem);

$arr_banktransactions = [];	

$banktransaction_1 = new XeroAPI\XeroPHP\Models\Accounting\BankTransaction;
$banktransaction_1->setReference('Ref-' . $this->getRandNum())
	->setDate(new DateTime('2019-12-02'))
	->setLineItems($lineitems)
	->setType("RECEIVE")
	->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE)
	->setBankAccount($bankAccount)
	->setContact($contact);
array_push($arr_banktransactions, $banktransaction_1);

$banktransaction_2 = new XeroAPI\XeroPHP\Models\Accounting\BankTransaction;
$banktransaction_2->setReference('Ref-' . $this->getRandNum())
	->setDate(new DateTime('2019-12-08'))
	->setLineItems($lineitems)
	->setType("RECEIVE")
	->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE)
	->setBankAccount($bankAccount)
	->setContact($contact);
array_push($arr_banktransactions, $banktransaction_2);
		
$banktransactions = new XeroAPI\XeroPHP\Models\Accounting\BankTransactions;
$banktransactions->setBankTransactions($arr_banktransactions);

$result = $apiInstance->createBankTransactions($xeroTenantId, $banktransactions); 
//[/BankTransactions:Create]

		$str = $str ."Create Bank Transaction: " . $result->getBankTransactions()[0]->getReference() ." --- Create Bank Transaction 2: " . $result->getBankTransactions()[1]->getReference();	
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateOrCreateBankTransactions($xeroTenantId,$apiInstance)
	{
		$str = '';

		$new = $this->createBankTransactions($xeroTenantId,$apiInstance,true);
		$banktransactionId = $new->getBankTransactions()[0]->getBankTransactionId();
		
		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();
		$getAccount = $this->getBankAccount($xeroTenantId,$apiInstance,true);
		$code = $getAccount->getAccounts()[0]->getCode();
		$accountId = $getAccount->getAccounts()[0]->getAccountId();
		$lineitem = $this->getLineItem();
		$lineitems = [];		
		array_push($lineitems, $lineitem);

		$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
		$contact->setContactId($contactId);

		$bankAccount = new XeroAPI\XeroPHP\Models\Accounting\Account;
		$bankAccount->setCode($code)
					->setAccountId($accountId);

		$lineitems = [];		
		array_push($lineitems, $lineitem);

//[BankTransactions:UpdateOrCreate]
$arr_banktransactions = [];	

// Create a new bank transaction
$banktransaction_1 = new XeroAPI\XeroPHP\Models\Accounting\BankTransaction;
$banktransaction_1->setReference('Ref-' . $this->getRandNum())
	->setDate(new DateTime('2019-12-02'))
	->setLineItems($lineitems)
	->setType("RECEIVE")
	->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE)
	->setBankAccount($bankAccount)
	->setContact($contact);
array_push($arr_banktransactions, $banktransaction_1);

// Update an existing transaction
$banktransaction = new XeroAPI\XeroPHP\Models\Accounting\BankTransaction;
$banktransaction->setReference("Goodbye World");
$banktransaction->setBankTransactionId($banktransactionId);

array_push($arr_banktransactions, $banktransaction);

$banktransactions = new XeroAPI\XeroPHP\Models\Accounting\BankTransactions;
$banktransactions->setBankTransactions($arr_banktransactions);

$result = $apiInstance->updateOrCreateBankTransactions($xeroTenantId,$banktransactions, false, null);
//[/BankTransactions:UpdateOrCreate]

		$str = $str . "New Bank Transaction: " . $result->getBankTransactions()[0]->getReference() . "<br>Updated Bank Transaction: " . $result->getBankTransactions()[1]->getReference() . "<br>"; 

		return $str;
	}

	public function getBankTransfer($xeroTenantId,$apiInstance)
	{
		$str = '';

//[BankTransfers:Read]
// READ ALL
$result = $apiInstance->getBankTransfers($xeroTenantId); 					
//[/BankTransfers:Read]

		$str = $str . "Get BankTransaction total: " . count($result->getBankTransfers()) . "<br>";
	
		return $str;
	}

	public function createBankTransfer($xeroTenantId,$apiInstance)
	{
		$str = '';

		$account = $this->getBankAccount($xeroTenantId,$apiInstance);

		if (count((array)$account) > 1) {

			$fromBankAccount = new XeroAPI\XeroPHP\Models\Accounting\Account;
			$fromBankAccount->setCode($account->getAccounts()[0]->getCode())
				->setAccountId($account->getAccounts()[0]->getAccountId());

			$toBankAccount = new XeroAPI\XeroPHP\Models\Accounting\Account;
			$toBankAccount->setCode($account->getAccounts()[1]->getCode())
				->setAccountId($account->getAccounts()[1]->getAccountId());

//[BankTransfers:Create]
$banktransfer = new XeroAPI\XeroPHP\Models\Accounting\BankTransfer;

$banktransfer->setDate(new DateTime('2017-01-02'))
	->setToBankAccount($toBankAccount)
	->setFromBankAccount($fromBankAccount)
	->setAmount("50");

$result = $apiInstance->createBankTransfer($xeroTenantId, $banktransfer);			
//[/BankTransfers:Create]

			$str = $str ."Create BankTransfer: " . $result->getBankTransfers()[0]->getAmount();

		} else {
			$str = $str ."Found less than 2 Bank Accounts  - can't work with Bank Transfers without 2. ";
		}

		return $str;
	}

	public function getBrandingTheme($xeroTenantId,$apiInstance)
	{
		$str = '';

//[BrandingThemes:Read]
// READ ALL
$result = $apiInstance->getBrandingThemes($xeroTenantId); 			
//[/BrandingThemes:Read]

		$str = $str ."Get BrandingThemes: " . count($result->getBrandingThemes()) . "<br>";

		return $str;
	}


	public function getContact($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$new = $this->createContacts($xeroTenantId,$apiInstance, true); 
		$contactId = $new->getContacts()[0]->getContactId();
//[Contact:Read]
$result = $apiInstance->getContacts($xeroTenantId, $contactId);
//[/Contact:Read]

		$str = $str . "Get specific Contact name: " . $result->getContacts()[0]->getName() . "<br>";

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateContact($xeroTenantId,$apiInstance)
	{
		$str = '';
		
		$new = $this->createContacts($xeroTenantId,$apiInstance,true);
		$contactId = $new->getContacts()[0]->getContactId();								
					
//[Contact:Update]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setName("Goodbye" . $this->getRandNum());	
$result = $apiInstance->updateContact($xeroTenantId,$contactId,$contact);  
//[/Contact:Update]

		$str = $str . "Update Contacts: " . $result->getContacts()[0]->getName() . "<br>" ;

		return $str;
	}
	
	public function archiveContact($xeroTenantId,$apiInstance)
	{
		$str = '';

		$new = $this->createContacts($xeroTenantId,$apiInstance,true);
		$contactId = $new->getContacts()[0]->getContactId();								
					
//[Contact:Archive]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactStatus(\XeroAPI\XeroPHP\Models\Accounting\Contact::CONTACT_STATUS_ARCHIVED);	
$result = $apiInstance->updateContact($xeroTenantId,$contactId,$contact);  
//[/Contact:Archive]

		$str = $str . "Archive Contacts: " . $result->getContacts()[0]->getName() . "<br>" ;

		return $str;
	}

	public function getContacts($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Contacts:Read]
// read all contacts 
$result = $apiInstance->getContacts($xeroTenantId); 		

// filter by contacts by status
$where = 'ContactStatus=="ACTIVE"';
$result2 = $apiInstance->getContacts($xeroTenantId, null, $where); 
//[/Contacts:Read]

		$str = $str . "Get Contacts Total: " . count($result->getContacts()) . "<br>";
		$str = $str . "Get ACTIVE Contacts Total: " . count($result2->getContacts()) . "<br>";

		if($returnObj) {
			return $result2;
		} else {
			return $str;
		}
	}

	public function createContacts($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Contacts:Create]
$arr_contacts = [];	

$contact_1 = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact_1->setName('FooBar' . $this->getRandNum())
	->setFirstName("Foo" . $this->getRandNum())
	->setLastName("Bar" . $this->getRandNum())
	->setEmailAddress("ben.bowden@24locks.com");
array_push($arr_contacts, $contact_1);
	
$contact_2 = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact_2->setName('FooBar' . $this->getRandNum())
	->setFirstName("Foo" . $this->getRandNum())
	->setLastName("Bar" . $this->getRandNum())
	->setEmailAddress("ben.bowden@24locks.com");
array_push($arr_contacts, $contact_2);
			
$contacts = new XeroAPI\XeroPHP\Models\Accounting\Contacts;
$contacts->setContacts($arr_contacts);

$result = $apiInstance->createContacts($xeroTenantId,$contacts); 
//[/Contacts:Create]
		
		$str = $str ."Create Contact 1: " . $result->getContacts()[0]->getName() ." --- Create Contact 2: " . $result->getContacts()[1]->getName() . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}	
	}
	
	public function updateOrCreateContacts($xeroTenantId,$apiInstance)
	{
		$str = '';
		
		$new = $this->createContacts($xeroTenantId,$apiInstance,true);
		$contactId = $new->getContacts()[0]->getContactId();								
					
//[Contacts:UpdateOrCreate]
$arr_contacts = [];	

$contact_1 = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact_1->setName('FooBar' . $this->getRandNum())
	->setFirstName("Foo" . $this->getRandNum())
	->setLastName("Bar" . $this->getRandNum())
	->setEmailAddress("ben.bowden@24locks.com");
array_push($arr_contacts, $contact_1);
	
$contact_2 = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact_2->setName("Goodbye" . $this->getRandNum())
		  ->setContactId($contactId);	
array_push($arr_contacts, $contact_2);

$contacts = new XeroAPI\XeroPHP\Models\Accounting\Contacts;
$contacts->setContacts($arr_contacts);

$result = $apiInstance->updateOrCreateContacts($xeroTenantId,$contacts,false);  
//[/Contacts:UpdateOrCreate]

		$str = $str . "New Contact: " . $result->getContacts()[0]->getName() . "<br>" . "Updated Contacts: " . $result->getContacts()[1]->getName() . "<br>" ;

		return $str;
	}

	public function getContactGroup($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[ContactGroups:Read]
$result = $apiInstance->getContactGroups($xeroTenantId); 
//[/ContactGroups:Read]

		$str = $str . "Get Contacts Total: " . count($result->getContactGroups()) . "<br>";

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function createContactGroup($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$new = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $new->getContacts()[0]->getContactId();

//[ContactGroups:Create]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactID($contactId);
$contacts = [];		
array_push($contacts, $contact);

$contactgroup = new XeroAPI\XeroPHP\Models\Accounting\ContactGroup;
$contactgroup->setName('Rebels-' . $this->getRandNum())
             ->setContacts($contacts);

try {
	$result = $apiInstance->createContactGroup($xeroTenantId,$contactgroup); 
} catch (\XeroAPI\XeroPHP\ApiException $e) {
	$error = AccountingObjectSerializer::deserialize($e->getResponseBody(), '\XeroAPI\XeroPHP\Models\Accounting\Error',[]);
	$str = "ApiException - " . $error->getElements()[0]["validation_errors"][0]["message"];
}
//[/ContactGroups:Create]

		$str = $str ."Create ContactGroups: " . $result->getContactGroups()[0]->getName() . "<br>";
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateContactGroup($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';		

		$new = $this->createContactGroup($xeroTenantId,$apiInstance,true);
		$contactgroupId = $new->getContactGroups()[0]->getContactGroupId();

//[ContactGroups:Update]
$contactgroup = new XeroAPI\XeroPHP\Models\Accounting\ContactGroup;
$contactgroup->setName("Goodbye" . $this->getRandNum());	
$result = $apiInstance->updateContactGroup($xeroTenantId,$contactgroupId,$contactgroup);  
//[/ContactGroups:Update]

		$str = $str . "Update ContactGroup: " . $result->getContactGroups()[0]->getName() .   "<br>" ;

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function archiveContactGroup($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';		

		$new = $this->getContactGroup($xeroTenantId,$apiInstance,true);

if (count($new->getContactGroups()) > 0) {

		$contactgroupId = $new->getContactGroups()[0]->getContactGroupID();

//[ContactGroups:Archive]
$contactgroup = new XeroAPI\XeroPHP\Models\Accounting\ContactGroup;
$contactgroup->setStatus(XeroAPI\XeroPHP\Models\Accounting\ContactGroup::STATUS_DELETED);
$result = $apiInstance->updateContactGroup($xeroTenantId,$contactgroupId,$contactgroup);  
//[/ContactGroups:Archive]
		
	$str = $str . "Set Status to DELETE for ContactGroup: " . $new->getContactGroups()[0]->getName() . "<br>" ;
} else {
	$str = $str . "No Contact Groups exist - create one before trying to archive";
}

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function createContactGroupContacts($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$new = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $new->getContacts()[0]->getContactId();

		$newContactGroup = $this->getContactGroup($xeroTenantId,$apiInstance,true);
		$contactgroupId = $newContactGroup->getContactGroups()[0]->getContactGroupId();

//[ContactGroups:AddContact]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactID($contactId);
$arr_contacts = [];		
array_push($arr_contacts, $contact);
$contacts = new XeroAPI\XeroPHP\Models\Accounting\Contacts;
$contacts->setContacts($arr_contacts);

$result = $apiInstance->createContactGroupContacts($xeroTenantId,$contactgroupId,$contacts); 
//[/ContactGroups:AddContact]

		$str = $str ."Add " . count($result->getContacts()) . " Contacts <br>";
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function removeContactFromContactGroup($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';		

		// Get a Contact
		$new = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $new->getContacts()[0]->getContactId();

		// Get a Contact Group
		$newContactGroup = $this->getContactGroup($xeroTenantId,$apiInstance,true);
		$contactgroupId = $newContactGroup->getContactGroups()[0]->getContactGroupId();

		// Add that contact to the contact group
		$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
		$contact->setContactID($contactId);
		$arr_contacts = [];		
		array_push($arr_contacts, $contact);
		$contacts = new XeroAPI\XeroPHP\Models\Accounting\Contacts;
		$contacts->setContacts($arr_contacts);

		$contactAddedToGroup = $apiInstance->createContactGroupContacts($xeroTenantId,$contactgroupId,$contacts); 


//[ContactGroups:RemoveContact]
$result = $apiInstance->deleteContactGroupContact($xeroTenantId,$contactgroupId,$contactId);  
//[/ContactGroups:RemoveContact]

		$str = $str . "Deleted Contact from Group<br>" ;

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function getCreditNote($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[CreditNotes:Read]
// READ ALL 
$result = $apiInstance->getCreditNotes($xeroTenantId); 		

// READ only ACTIVE
$where = 'Status=="' . \XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_DRAFT . '"';
$result2 = $apiInstance->getCreditNotes($xeroTenantId, null, $where); 
//[/CreditNotes:Read]

		$str = $str . "Get CreditNotes Total: " . count($result->getCreditNotes()) . "<br>";
		$str = $str . "Get ACTIVE CreditNotes Total: " . count($result2->getCreditNotes()) . "<br>";

		if($returnObj) {
			return $result->getCreditNotes()[0];
		} else {
			return $str;
		}
	}
	public function createCreditNotes($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$lineitems = [];		
		array_push($lineitems, $this->getLineItem());

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();
		
//[CreditNotes:Create]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactId($contactId);

$arr_creditnotes = [];	

$creditnote_1 = new XeroAPI\XeroPHP\Models\Accounting\CreditNote;
$creditnote_1->setDate(new DateTime('2019-12-15'))
	->setContact($contact)
	->setLineItems($lineitems)
	->setType(XeroAPI\XeroPHP\Models\Accounting\CreditNote::TYPE_ACCPAYCREDIT);
array_push($arr_creditnotes, $creditnote_1);
	
$creditnote_2 = new XeroAPI\XeroPHP\Models\Accounting\CreditNote;
$creditnote_2->setDate(new DateTime('2019-12-02'))
	->setContact($contact)
	->setLineItems($lineitems)
	->setType(XeroAPI\XeroPHP\Models\Accounting\CreditNote::TYPE_ACCPAYCREDIT);
array_push($arr_creditnotes, $creditnote_2);
			
$creditnotes = new XeroAPI\XeroPHP\Models\Accounting\CreditNotes;
$creditnotes->setCreditNotes($arr_creditnotes);

$result = $apiInstance->createCreditNotes($xeroTenantId,$creditnotes); 
//[/CreditNotes:Create]
		
		$str = $str ."Create CreditNote 1: " . $result->getCreditNotes()[0]->getTotal() ." --- Create CreditNote 2: " . $result->getCreditNotes()[1]->getTotal() . "<br>" ;

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateCreditNote($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$new = $this->createCreditNotes($xeroTenantId,$apiInstance,true);
		$creditnoteId = $new->getCreditNotes()[0]->getCreditNoteId();
		
//[CreditNotes:Update]
$creditnote = new XeroAPI\XeroPHP\Models\Accounting\CreditNote;
$creditnote->setDate(new DateTime('2020-01-02'));
$result = $apiInstance->updateCreditNote($xeroTenantId,$creditnoteId,$creditnote); 
//[/CreditNotes:Update]
		
		$str = $str ."Update CreditNote: $" . $result->getCreditNotes()[0]->getTotal() .  "<br>" ;

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function deleteCreditNote($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$new = $this->createCreditNotes($xeroTenantId,$apiInstance,true);
		$creditnoteId = $new->getCreditNotes()[0]->getCreditNoteId();
		
//[CreditNotes:Delete]
$creditnote = new XeroAPI\XeroPHP\Models\Accounting\CreditNote;
$creditnote->setStatus(XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_DELETED);
$result = $apiInstance->updateCreditNote($xeroTenantId,$creditnoteId,$creditnote); 
//[/CreditNotes:Delete]

		$str = $str . "CreditNote status: " . $result->getCreditNotes()[0]->getStatus() . "<br>" ;

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function allocateCreditNote($xeroTenantId,$apiInstance)
	{
		$str = '';

		$newInv = $this->createInvoiceAccPay($xeroTenantId,$apiInstance,true);
		$invoiceId = $newInv->getInvoices()[0]->getInvoiceId();
		
		$new = $this->createCreditNoteAuthorised($xeroTenantId,$apiInstance,true);
		$creditnoteId = $new->getCreditNotes()[0]->getCreditNoteID();

//[CreditNotes:Allocate]
$creditnote = $apiInstance->getCreditNote($xeroTenantId,$creditnoteId); 

$invoice = new XeroAPI\XeroPHP\Models\Accounting\Invoice;
$invoice->setInvoiceID($invoiceId);

$allocation = new XeroAPI\XeroPHP\Models\Accounting\Allocation;
$allocation->setInvoice($invoice)
	->setAmount("2.00")
	->setDate(new DateTime('2019-09-02'));

$result = $apiInstance->createCreditNoteAllocation($xeroTenantId,$creditnoteId,$allocation); 

$result2 = $apiInstance->getInvoice($xeroTenantId,$invoiceId); 
var_dump( $result2->getInvoices()[0]->getCreditNotes()[0]->getAppliedAmount());
//[/CreditNotes:Allocate]

		$str = $str . "Allocate amount: " . $result->getAllocations()[0]->getAmount() . "<br>" ;
		
		return $str;
		
	}

	public function refundCreditNote($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$account = $this->getBankAccount($xeroTenantId,$apiInstance,true);
		$bankaccount = new XeroAPI\XeroPHP\Models\Accounting\Account;
		$bankaccount->setAccountId($account->getAccounts()[0]->getAccountId());

		$newCN = $this->createCreditNoteAuthorised($xeroTenantId,$apiInstance,true);
		$creditnote = new XeroAPI\XeroPHP\Models\Accounting\CreditNote;
		$creditnote->setCreditNoteID($newCN->getCreditNotes()[0]->getCreditNoteID());

//[CreditNotes:Refund]
$payment = new XeroAPI\XeroPHP\Models\Accounting\Payment;

$payment->setCreditNote($creditnote)
	->setAccount($bankaccount)
	->setDate(new DateTime('2019-09-02'))
	->setReference("foobar")
	->setAmount("2.00");

$result = $apiInstance->createPayment($xeroTenantId,$payment);
//[/CreditNotes:Refund]
		
		$str = $str . "CreditNote Refund payment ID: " . $result->getPayments()[0]->getPaymentId() . "<br>" ;
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}	
	

	public function voidCreditNote($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$new = $this->createCreditNoteAuthorised($xeroTenantId,$apiInstance,true);
		$creditnoteId = $new->getCreditNotes()[0]->getCreditNoteID();
		
//[CreditNotes:Void]
$creditnote = new XeroAPI\XeroPHP\Models\Accounting\CreditNote;
$creditnote->setCreditNoteID($creditnoteId)
	->setStatus(XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_VOIDED);
$result = $apiInstance->updateCreditNote($xeroTenantId,$creditnoteId,$creditnote);
//[/CreditNotes:Void]

		$str = $str . "Void CreditNote: " . $result->getCreditNotes()[0]->getCreditNoteID() . "<br>" ;

		return $str;
	}

	public function getCurrency($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Currencies:Read]
$result = $apiInstance->getCurrencies($xeroTenantId); 		
//[/Currencies:Read]

		$str = $str . "Get Currencies Total: " . count($result->getCurrencies()) . "<br>";
		
		if($returnObj) {
			return $result->getCurrencies()[0];
		} else {
			return $str;
		}
		
	}	

	public function createCurrency($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Currencies:Create]
$currency = new XeroAPI\XeroPHP\Models\Accounting\Currency;
$currency->setCode(XeroAPI\XeroPHP\Models\Accounting\CurrencyCode::NZD)
		 ->setDescription("New Zealand Dollar");
		
$result = $apiInstance->createCurrency($xeroTenantId,$currency); 		
//[/Currencies:Create]

		$str = $str . "New currency code: " . $result->getCurrencies()[0]->getCode() . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}	

	public function getEmployee($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Employees:Read]
$result = $apiInstance->getEmployees($xeroTenantId); 		 		

// READ only ACTIVE
$where = 'Status=="ACTIVE"';
$result2 = $apiInstance->getEmployees($xeroTenantId, null, $where); 
//[/Employees:Read]

		$str = $str . "Get Employees Total: " . count($result->getEmployees()) . "<br>";
		$str = $str . "Get ACTIVE Employees Total: " . count($result2->getEmployees()) . "<br>";

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}	

	public function createEmployees($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Employees:Create]
$arr_employees = [];	

$employee_1 = new XeroAPI\XeroPHP\Models\Accounting\Employee;
$employee_1->setFirstName('Sid-' . $this->getRandNum())
	->setLastName("Maestre - " . $this->getRandNum());	
array_push($arr_employees, $employee_1);
	
$employee_2 = new XeroAPI\XeroPHP\Models\Accounting\Employee;
$employee_2->setFirstName('Sid-' . $this->getRandNum())
	->setLastName("Maestre - " . $this->getRandNum());	
array_push($arr_employees, $employee_2);
			
$employees = new XeroAPI\XeroPHP\Models\Accounting\Employees;
$employees->setEmployees($arr_employees);

$result = $apiInstance->createEmployees($xeroTenantId,$employees); 
//[/Employees:Create]
		
		$str = $str . "Create a new Employee 1: " . $result->getEmployees()[0]->getFirstName() . " and Create a new Employee 2: " . $result->getEmployees()[1]->getFirstName() . "<br>" ;

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}	
	
	public function updateEmployee($xeroTenantId,$apiInstance)
	{
		$str = '';

		$new = $this->getEmployee($xeroTenantId,$apiInstance,true);
		$employeeId = $new->getEmployees()[3]->getEmployeeID();	
		$firstName = $new->getEmployees()[0]->getFirstName();	
		$lastName = $new->getEmployees()[0]->getLastName();	

//[Employees:Update]
$external_link = new XeroAPI\XeroPHP\Models\Accounting\ExternalLink;
$external_link ->setUrl("http://twitter.com/#!/search/Homer+Simpson");

$employee = new XeroAPI\XeroPHP\Models\Accounting\Employee;
$employee->setExternalLink($external_link);
$employee->setFirstName($firstName);
$employee->setLastName($lastName);

$result = $apiInstance->updateEmployee($xeroTenantId,$employeeId,$employee); 
//[/Employees:Update]

		var_dump($result);
		//$str = $str . "Update Employee: " . $employee["FirstName"] . "  " . $employee["LastName"]   . "<br>" ;

		return $str;
	}	

	public function getExpenseClaim($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[ExpenseClaims:Read]
// READ ALL 
$result = $apiInstance->getExpenseClaims($xeroTenantId); 						
// READ only ACTIVE
$where = 'Status=="SUBMITTED"';
$result2 = $apiInstance->getExpenseClaims($xeroTenantId, null, $where); 
//[/ExpenseClaims:Read]

		$str = $str . "Get ExpenseClaim total: " . count($result->getExpenseClaims()) . "<br>";
		$str = $str . "Get ACTIVE ExpenseClaim total: " . count($result2->getExpenseClaims()) . "<br>";

		if($returnObj) {
			return $result->getExpenseClaims()[0];
		} else {
			return $str;
		}
	}	


	public function createExpenseClaim($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$allUsers = $this->getUser($xeroTenantId,$apiInstance,true);
		$userId = $allUsers->getUsers()[0]->getUserID();

		$lineitem = $this->getLineItemForReceipt($xeroTenantId,$apiInstance);

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();
		
		if (count($allUsers->getUsers())) {	
//[ExpenseClaims:Create]
$lineitems = [];
array_push($lineitems, $lineitem);
$user = new XeroAPI\XeroPHP\Models\Accounting\User;
$user->setUserID($userId);

$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactId($contactId);

// CREATE RECEIPT
$receipt = new XeroAPI\XeroPHP\Models\Accounting\Receipt;
$receipt->setDate(new DateTime('2017-01-02'))
		->setLineItems($lineitems)
		->setContact($contact)
		->setTotal(20.00)
		->setUser($user);

$receipts = new XeroAPI\XeroPHP\Models\Accounting\Receipts;
$arr_receipts = [];
array_push($arr_receipts, $receipt);
$receipts->setReceipts($arr_receipts);
$new_receipt = $apiInstance->createReceipt($xeroTenantId,$receipts); 

// CREATE EXPENSE CLAIM
$expenseclaim = new XeroAPI\XeroPHP\Models\Accounting\ExpenseClaim;
$expenseclaim->setUser($user)
             ->setReceipts($new_receipt->getReceipts());

$expenseclaims = new XeroAPI\XeroPHP\Models\Accounting\ExpenseClaims;
$arr_expenseclaims = [];
array_push($arr_expenseclaims, $expenseclaim);
$expenseclaims->setExpenseClaims($arr_expenseclaims);

$result = $apiInstance->createExpenseClaims($xeroTenantId,$expenseclaims); 
//[/ExpenseClaims:Create]

			$str = $str ."Created a new Expense Claim: " . $result->getExpenseClaims()[0]->getExpenseClaimID() . "<br>" ;
		}

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}	

	public function updateExpenseClaim($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$new = $this->createExpenseClaim($xeroTenantId,$apiInstance,true);
		$guid = $new->getExpenseClaims()[0]->getExpenseClaimID();

//[ExpenseClaims:Update]
$expenseclaim = new XeroAPI\XeroPHP\Models\Accounting\ExpenseClaim;
$expenseclaim->setStatus(XeroAPI\XeroPHP\Models\Accounting\ExpenseClaim::STATUS_AUTHORISED);
$expenseclaim->setExpenseClaimId($guid);
		
$result = $apiInstance->updateExpenseClaim($xeroTenantId,$guid,$expenseclaim); 
//[/ExpenseClaims:Update]
			
		$str = $str . "Updated a Expense Claim: " . $result->getExpenseClaims()[0]->getExpenseClaimID() . "<br>" ;
		
		return $str;
	}	

	public function getInvoice($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Invoices:Read]
// READ ALL 
$result = $apiInstance->getInvoices($xeroTenantId); 						
// READ only ACTIVE
$where = 'Status=="VOIDED"';
$result2 = $apiInstance->getInvoices($xeroTenantId, null, $where); 
//[/Invoices:Read]
		$str = $str . "Get Invoice total: " . count($result->getInvoices()) . "<br>";
		$str = $str . "Get Voided Invoice total: " . count($result2->getInvoices()) . "<br>";

		if($returnObj) {
			return $result->getInvoices()[0];
		} else {
			return $str;
		}
	}	

	public function getInvoiceAsPdf($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Invoices:Readpdf]
// READ ALL 
$invoices = $apiInstance->getInvoices($xeroTenantId); 						
$invoiceId = $invoices->getInvoices()[0]->getInvoiceId();
$result = $apiInstance->getInvoiceAsPdf($xeroTenantId, $invoiceId, "application/pdf"); 						

// read PDF contents
$content = $result->fread($result->getSize());

//check if a temp dir exsits
$dir_to_save = "./temp/";
if (!is_dir($dir_to_save)) {
  mkdir($dir_to_save);
}
// write to temp dir
file_put_contents($dir_to_save . $result->getFileName() . ".pdf", $content);
//[/Invoices:ReadPdf]
		
		$str = $str . "PDF of Invoice name: " . $result->getFileName() . ".pdf" . "<br>";

		if($returnObj) {
			return $result->getInvoices()[0];
		} else {
			return $str;
		}
	}	

	public function updateOrCreateInvoices($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';
		
		$lineitems = [];		
		array_push($lineitems, $this->getLineItem());

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();

		$new = $this->createInvoices($xeroTenantId,$apiInstance,true);
		$existingInvoice = $new->getInvoices()[0];

//[Invoices:UpdateOrCreate]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactId($contactId);

$arr_invoices = [];	

$invoice_1 = new XeroAPI\XeroPHP\Models\Accounting\Invoice;
$invoice_1->setReference('Ref-' . $this->getRandNum())
	->setDueDate(new DateTime('2019-12-10'))
	->setContact($contact)
	->setLineItems($lineitems)
	->setStatus(XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_AUTHORISED)
	->setType(XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCPAY)
	->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE);	
array_push($arr_invoices, $invoice_1);

array_push($arr_invoices, $existingInvoice);
			
$invoices = new XeroAPI\XeroPHP\Models\Accounting\Invoices;
$invoices->setInvoices($arr_invoices);

$result = $apiInstance->updateOrCreateInvoices($xeroTenantId,$invoices); 
//[/Invoices:UpdateOrCreate]
		
		$str = $str ."Create Invoice 1 total amount: " . $result->getInvoices()[0]->getTotal() ." and Create Invoice 2 total amount: " . $result->getInvoices()[1]->getTotal() . "<br>" ;

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}	



	public function createInvoices($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';
		
		$lineitems = [];		
		array_push($lineitems, $this->getLineItem());

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();

//[Invoices:Create]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactId($contactId);

$arr_invoices = [];	

$invoice_1 = new XeroAPI\XeroPHP\Models\Accounting\Invoice;
$invoice_1->setReference('Ref-' . $this->getRandNum())
	->setDueDate(new DateTime('2019-12-10'))
	->setContact($contact)
	->setLineItems($lineitems)
	->setStatus(XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_AUTHORISED)
	->setType(XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCPAY)
	->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE);	
array_push($arr_invoices, $invoice_1);
	
$invoice_2 = new XeroAPI\XeroPHP\Models\Accounting\Invoice;
$invoice_2->setReference('Ref-' . $this->getRandNum())
	->setDueDate(new DateTime('2019-12-02'))
	->setContact($contact)
	->setLineItems($lineitems)
	->setStatus(XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_AUTHORISED)
	->setType(XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCPAY)
	->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE);	
array_push($arr_invoices, $invoice_2);
			
$invoices = new XeroAPI\XeroPHP\Models\Accounting\Invoices;
$invoices->setInvoices($arr_invoices);

$result = $apiInstance->createInvoices($xeroTenantId,$invoices); 
//[/Invoices:Create]
		
		$str = $str ."Create Invoice 1 total amount: " . $result->getInvoices()[0]->getTotal() ." and Create Invoice 2 total amount: " . $result->getInvoices()[1]->getTotal() . "<br>" ;

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}	

	public function updateInvoice($xeroTenantId,$apiInstance)
	{
		$str = '';
		$new = $this->createInvoices($xeroTenantId,$apiInstance,true);
		$guid = $new->getInvoices()[0]->getInvoiceID();

//[Invoices:Update]
$invoice = new XeroAPI\XeroPHP\Models\Accounting\Invoice;
$invoice->setReference('Ref-' . $this->getRandNum());
$result = $apiInstance->updateInvoice($xeroTenantId,$guid,$invoice); 
//[/Invoices:Update]

		$str = $str . "Update Invoice: " . $result->getInvoices()[0]->getReference() . "<br>" ;

		return $str;
	}

	public function deleteInvoice($xeroTenantId,$apiInstance)
	{
		$str = '';

		$new = $this->createInvoiceDraft($xeroTenantId,$apiInstance,true);
		$invoiceId = $new->getInvoices()[0]->getInvoiceID();

//[Invoices:Delete]
$invoice = new XeroAPI\XeroPHP\Models\Accounting\Invoice;
$invoice->setStatus(XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_DELETED);
$result = $apiInstance->updateInvoice($xeroTenantId,$invoiceId,$invoice); 
//[/Invoices:Delete]

		$str = $str . "Delete Invoice";

		return $str;
	}

	public function voidInvoice($xeroTenantId,$apiInstance)
	{
		$str = '';

		$new = $this->createInvoices($xeroTenantId,$apiInstance,true);
		$invoiceId = $new->getInvoices()[0]->getInvoiceID();

//[Invoices:Void]
$invoice = new XeroAPI\XeroPHP\Models\Accounting\Invoice;
$invoice->setStatus(XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_VOIDED);
$result = $apiInstance->updateInvoice($xeroTenantId,$invoiceId,$invoice); 
//[/Invoices:Void]

		$str = $str . "Void Invoice";

		return $str;
	}

	public function getInvoiceReminder($xeroTenantId,$apiInstance)
	{
		$str = '';

//[InvoiceReminders:Read]
// READ  
$result = $apiInstance->getInvoiceReminders($xeroTenantId); 
//[/InvoiceReminders:Read]
		
		$str = $str . "Invoice Reminder Enabled?: ";
		if ($result->getInvoiceReminders()[0]->getEnabled() == 1) {
			$str = $str . "YES";
		} else {
			$str = $str ."NO";
		}

		return $str;
	}

	public function getItem($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Items:Read]
// READ ALL 
$result = $apiInstance->getItems($xeroTenantId); 						
//[/Items:Read]

		$str = $str . "Get Items total: " . count($result->getItems()) . "<br>";
		
		if($returnObj) {
			return $result->getItems()[0];
		} else {
			return $str;
		}
	}	

	public function createItems($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Items:Create]
$arr_items = [];	

// $purchase = new XeroAPI\XeroPHP\Models\Accounting\Purchase;
// $purchase->setUnitPrice(17.39130714285714)
// 	->setTaxType(INPUT)
// 	->setCOGSAccountCode(310);

// $sales = new XeroAPI\XeroPHP\Models\Accounting\Purchase;
// $sales->setUnitPrice(24.34783)
// 	->setTaxType(OUTPUT)
// 	->setCOGSAccountCode(400);

// $item_1 = new XeroAPI\XeroPHP\Models\Accounting\Item;
// $item_1->setName("Moisture Mist Balancing Gel Mist")
// 	->setCode("SKU-198")
// 	->setDescription("Moisture Mist Balancing Gel Mist Moisture Mist Balancing Gel Mist It can be used under makeup as a pre-makeup base or over makeup for added moisture during the day.\n")
// 	->setIsTrackedAsInventory(true)
// 	->setInventoryAssetAccountCode(140)
// 	->setSalesDetails($sales)
// 	->setPurchaseDetails($purchase);
// array_push($arr_items, $item_1);

$item_1 = new XeroAPI\XeroPHP\Models\Accounting\Item;
$item_1->setName('My Item-' . $this->getRandNum())
	->setCode($this->getRandNum())
	->setDescription("This is my Item description.")
	->setIsTrackedAsInventory(false);
array_push($arr_items, $item_1);

$item_2 = new XeroAPI\XeroPHP\Models\Accounting\Item;
$item_2->setName('My Item-' . $this->getRandNum())
	->setCode($this->getRandNum())
	->setDescription("This is my Item description.")
	->setIsTrackedAsInventory(false);
array_push($arr_items, $item_2);
	
$items = new XeroAPI\XeroPHP\Models\Accounting\Items;
$items->setItems($arr_items);

$result = $apiInstance->createItems($xeroTenantId,$items,true,4); 
//[/Items:Create]
		
		$str = $str . "Create item 1: " . $result->getItems()[0]->getName() . " and Create item 2: " . $result->getItems()[1]->getName() . "<br>" ;
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateItem($xeroTenantId,$apiInstance)
	{
		$str = '';
	
		$new = $this->createItems($xeroTenantId,$apiInstance,true);
		$itemId = $new->getItems()[0]->getItemId();
		$code = $new->getItems()[0]->getCode();
	
		//[Items:Update]
$item = new XeroAPI\XeroPHP\Models\Accounting\Item;
$item->setName('Change Item-' . $this->getRandNum())
     ->setCode($code);
$result = $apiInstance->updateItem($xeroTenantId,$itemId,$item); 
		//[/Items:Update]

		$str = $str . "Update item: " . $result->getItems()[0]->getName() . "<br>";
		
		return $str;
	}
	
	public function deleteItem($xeroTenantId,$apiInstance)
	{
		$str = '';
	
		$new = $this->createItem($xeroTenantId,$apiInstance,true);
		$itemId = $new->getItems()[0]->getItemId();
	
//[Items:Delete]
$result = $apiInstance->deleteItem($xeroTenantId,$itemId);
//[/Items:Delete]

		$str = $str . "Item deleted <br>" ;

		return $str;
	}			

	public function getJournal($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';
//[Journals:Read]
// READ ALL 
$result = $apiInstance->getJournals($xeroTenantId); 						
//[/Journals:Read]
		$str = $str . "Get Journals total: " . count($result->getJournals()) . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function getLinkedTransaction($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[LinkedTransactions:Read]
// READ ALL 
$result = $apiInstance->getLinkedTransactions($xeroTenantId); 						
//[/LinkedTransactions:Read]

		$str = $str . "Get LinkedTransactions total: " . count($result->getLinkedTransactions()) . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function createLinkedTransaction($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$new = $this->createInvoiceAccPay($xeroTenantId,$apiInstance,true);
		$guid = $new->getInvoices()[0]->getInvoiceID();
		$lineitemid = $new->getInvoices()[0]->getLineItems()[0]->getLineItemId();
		
//[LinkedTransactions:Create]
$linkedtransaction = new XeroAPI\XeroPHP\Models\Accounting\LinkedTransaction;
$linkedtransaction->setSourceTransactionID($guid)
	->setSourceLineItemID($lineitemid);

$result = $apiInstance->createLinkedTransaction($xeroTenantId,$linkedtransaction); 	
//[/LinkedTransactions:Create]

		$str = $str . "Created LinkedTransaction ID: " . $result->getLinkedTransactions()[0]->getLinkedTransactionID();
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateLinkedTransaction($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$new = $this->createLinkedTransaction($xeroTenantId,$apiInstance,true);
		$linkedtransactionId = $new->getLinkedTransactions()[0]->getLinkedTransactionID();
		
		$invNew = $this->createInvoiceAccRec($xeroTenantId,$apiInstance,true);
		$invoiceId = $invNew->getInvoices()[0]->getInvoiceID();
		$lineitemid = $invNew->getInvoices()[0]->getLineItems()[0]->getLineItemId();
		$contactid= $invNew->getInvoices()[0]->getContact()->getContactId();

//[LinkedTransactions:Update]
$linkedtransaction = new XeroAPI\XeroPHP\Models\Accounting\LinkedTransaction;
$linkedtransaction->setTargetTransactionID($invoiceId)
			->setTargetLineItemID($lineitemid)
			->setContactID($contactid);

$linkedtransactions = new XeroAPI\XeroPHP\Models\Accounting\LinkedTransactions;
$arr_linkedtransactions = [];
array_push($arr_linkedtransactions, $linkedtransaction);
$linkedtransactions->setLinkedTransactions($arr_linkedtransactions);
		
$result = $apiInstance->updateLinkedTransaction($xeroTenantId,$linkedtransactionId,$linkedtransactions); 
//[/LinkedTransactions:Update]

		$str = $str . "Updated LinkedTransaction ID: " . $result->getLinkedTransactions()[0]->getLinkedTransactionID();
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function deleteLinkedTransaction($xeroTenantId,$apiInstance)
	{
		$str = '';

		// Need a linked transaction to work with ... so create one.
		$new = $this->createLinkedTransaction($xeroTenantId,$apiInstance,true);
		$linkedtransactionId = $new->getLinkedTransactions()[0]->getLinkedTransactionID();

//[LinkedTransactions:Delete]
$result = $apiInstance->deleteLinkedTransaction($xeroTenantId,$linkedtransactionId); 
//[/LinkedTransactions:Delete]

		$str = $str . "LinkedTransaction Deleted";

		return $str;
	}
		
	public function getManualJournal($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[ManualJournals:Read]
$result = $apiInstance->getManualJournals($xeroTenantId); 						
//[/ManualJournals:Read]
		$str = $str . "Get ManualJournals: " . count($result->getManualJournals()) . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function createManualJournals($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$credit = $this->getJournalLineCredit();
		$debit = $this->getJournalLineDebit();

//[ManualJournals:Create]
$arr_journallines = [];
array_push($arr_journallines, $credit);
array_push($arr_journallines, $debit);

$arr_manualjournals = [];
$manualjournal_1 = new XeroAPI\XeroPHP\Models\Accounting\ManualJournal;
$manualjournal_1->setNarration('MJ from SDK -' . $this->getRandNum())
              ->setJournalLines($arr_journallines);
array_push($arr_manualjournals, $manualjournal_1);

$manualjournal_2 = new XeroAPI\XeroPHP\Models\Accounting\ManualJournal;
$manualjournal_2->setNarration('MJ from SDK -' . $this->getRandNum())
              ->setJournalLines($arr_journallines);
array_push($arr_manualjournals, $manualjournal_2);

$manualjournals = new XeroAPI\XeroPHP\Models\Accounting\ManualJournals;
$manualjournals->setManualJournals($arr_manualjournals);

$result = $apiInstance->createManualJournals($xeroTenantId,$manualjournals); 
//[/ManualJournals:Create]
		
		$str = $str . "Create ManualJournal 1: " . $result->getManualJournals()[0]->getNarration() . " and Create ManualJournal 2: " . $result->getManualJournals()[1]->getNarration() . "<br>" ;
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateManualJournal($xeroTenantId,$apiInstance)
	{
		$str = '';
		
		$new = $this->createManualJournals($xeroTenantId,$apiInstance,true);
		$manualjournalId = $new->getManualJournals()[0]->getManualJournalID();

//[ManualJournals:Update]
$manualjournal = new XeroAPI\XeroPHP\Models\Accounting\ManualJournal;
$manualjournal->setNarration('MJ from SDK -' . $this->getRandNum());

$manualjournals = new XeroAPI\XeroPHP\Models\Accounting\ManualJournals;
$arr_manualjournals = [];
array_push($arr_manualjournals, $manualjournal);
$manualjournals->setManualJournals($arr_manualjournals);

$result = $apiInstance->updateManualJournal($xeroTenantId,$manualjournalId,$manualjournals); 
//[/ManualJournals:Update]

		$str = $str . "Update ManualJournal: " .  $result->getManualJournals()[0]->getNarration() . "<br>";
		
		return $str;
	}

	public function getOrganisation($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Organisations:Read]
$result = $apiInstance->getOrganisations($xeroTenantId); 						
//[/Organisations:Read]

		$str = $str . "Get Organisations: " . $result->getOrganisations()[0]->getName() . "<br>";
		
		if($returnObj) {
			return $result->getOrganisations()[0];
		} else {
			return $str;
		}
	}

	public function getOverpayment($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Overpayments:Read]
$result = $apiInstance->getOverpayments($xeroTenantId); 						
//[/Overpayments:Read]

		$str = $str . "Get Overpayments: " . count($result->getOverpayments()) . "<br>";
		
		if($returnObj) {
			return $result->getOverpayments()[0];
		} else {
			return $str;
		}
	}

	public function createOverpayment($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$lineitem = $this->getLineItemForOverpayment($xeroTenantId,$apiInstance);
		$lineitems = [];		
		array_push($lineitems, $lineitem);

		$getAccount = $this->getBankAccount($xeroTenantId,$apiInstance);
		$accountId = $getAccount->getAccounts()[0]->getAccountId();

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();

		if (count($getAccount->getAccounts())) {

//[Overpayments:Create]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactId($contactId);

$bankAccount = new XeroAPI\XeroPHP\Models\Accounting\Account;
$bankAccount->setCode($getAccount->getAccounts()[0]->getCode())
	->setAccountId($accountId);

$overpayment = new XeroAPI\XeroPHP\Models\Accounting\BankTransaction;
$overpayment->setReference('Ref-' . $this->getRandNum())
	->setDate(new DateTime('2017-01-02'))
	->setType(XeroAPI\XeroPHP\Models\Accounting\BankTransaction::TYPE_RECEIVE_OVERPAYMENT) 
	->setLineItems($lineitems)
	->setContact($contact)
	->setLineAmountTypes("NoTax")
	->setBankAccount($bankAccount);

$result = $apiInstance->createBankTransactions($xeroTenantId,$overpayment); 
//[/Overpayments:Create]

			$str = $str ."Create Overpayment(Bank Transaction) ID: " . $result->getBankTransactions()[0]->getBankTransactionId() . "<br>" ;

		} else {
			$str = $str . "No Bank Account exists";	
		}

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function allocateOverpayments($xeroTenantId,$apiInstance)
	{
		$str = '';

		$invNew = $this->createInvoiceAccRec($xeroTenantId,$apiInstance,true);
		$invoiceId = $invNew->getInvoices()[0]->getInvoiceID();
		$overpaymentNew = $this->createOverpayment($xeroTenantId,$apiInstance,true);
		$overpaymentId = $overpaymentNew->getBankTransactions()[0]->getOverpaymentId();

//[Overpayments:Allocate]
$invoice = new XeroAPI\XeroPHP\Models\Accounting\Invoice;
$invoice->setInvoiceID($invoiceId);

$arr_allocations = [];	

$allocation_1 = new XeroAPI\XeroPHP\Models\Accounting\Allocation;
$allocation_1->setInvoice($invoice)
	->setAmount("1.00")
	->setDate(new DateTime('2019-12-02'));
array_push($arr_allocations, $allocation_1);

$allocation_2 = new XeroAPI\XeroPHP\Models\Accounting\Allocation;
$allocation_2->setInvoice($invoice)
	->setAmount("1.00")
	->setDate(new DateTime('2019-12-07'));
array_push($arr_allocations, $allocation_2);

$allocations = new XeroAPI\XeroPHP\Models\Accounting\Allocations;	
$allocations->setAllocations($arr_allocations);

$result = $apiInstance->createOverpaymentAllocations($xeroTenantId,$overpaymentId,$allocations);
//[/Overpayments:Allocate]
		
		$str = $str . "Allocate 2 Overpayment to Invoice ID: " . $result->getAllocations()[0]->getInvoice()->getInvoiceId() . "<br>" ;
	
		return $str;
	}

	public function refundOverpayment($xeroTenantId,$apiInstance)
	{
		$str = '';

		$account = $this->getBankAccount($xeroTenantId,$apiInstance);
		$accountId = $account->getAccounts()[0]->getAccountId();
		$newOverpayment = $this->createOverpayment($xeroTenantId,$apiInstance,true);
		$guid = $newOverpayment->getBankTransactions()[0]->getOverpaymentID();

//[Overpayments:Refund]
$bankaccount = new XeroAPI\XeroPHP\Models\Accounting\Account;
$bankaccount->setAccountId($accountId);

$overpayment = new XeroAPI\XeroPHP\Models\Accounting\Overpayment;
$overpayment->setOverpaymentId($guid);

$payment = new XeroAPI\XeroPHP\Models\Accounting\Payment;
$payment->setOverpayment($overpayment)
	->setAccount($bankaccount)
	->setAmount("2.00");

$result = $apiInstance->createPayment($xeroTenantId,$payment);
//[/Overpayments:Refund]

		$str = $str . "Create Overpayment Refund (Payments ID): " . $result->getPayments()[0]->getPaymentId()  ." <br>" ;
		
		return $str;
	}	

	public function getPayment($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Payments:Read]
$result = $apiInstance->getPayments($xeroTenantId); 						
//[/Payments:Read]

		$str = $str . "Get Payments: " . count($result->getPayments()) . "<br>";
		
		if($returnObj) {
			return $result->getPayments()[0];
		} else {
			return $str;
		}
	}

	public function createPayment($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$newInv = $this->createInvoiceAccRec($xeroTenantId,$apiInstance,true);
		$invoiceId = $newInv->getInvoices()[0]->getInvoiceID();
		$newAcct = $this->getBankAccount($xeroTenantId,$apiInstance);
		$accountId = $newAcct->getAccounts()[0]->getAccountId();

//[Payments:Create]
$invoice = new XeroAPI\XeroPHP\Models\Accounting\Invoice;
$invoice->setInvoiceID($invoiceId);

$bankaccount = new XeroAPI\XeroPHP\Models\Accounting\Account;
$bankaccount->setAccountID($accountId);

$payment = new XeroAPI\XeroPHP\Models\Accounting\Payment;
$payment->setInvoice($invoice)
	->setAccount($bankaccount)
	->setAmount("2.00");

$result = $apiInstance->createPayment($xeroTenantId,$payment);
//[/Payments:Create]
		
		$str = $str . "Create Payment ID: " . $result->getPayments()[0]->getPaymentID() . "<br>" ;
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function createPayments($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$newInv = $this->createInvoiceAccRec($xeroTenantId,$apiInstance,true);
		$invoiceId = $newInv->getInvoices()[0]->getInvoiceID();
		$newAcct = $this->getBankAccount($xeroTenantId,$apiInstance);
		$accountId = $newAcct->getAccounts()[0]->getAccountId();

//[Payments:CreateMulti]
$invoice = new XeroAPI\XeroPHP\Models\Accounting\Invoice;
$invoice->setInvoiceID($invoiceId);

$bankaccount = new XeroAPI\XeroPHP\Models\Accounting\Account;
$bankaccount->setAccountID($accountId);

$arr_payments = [];

$payment_1 = new XeroAPI\XeroPHP\Models\Accounting\Payment;
$payment_1->setInvoice($invoice)
	->setAccount($bankaccount)
	->setAmount("2.00");
array_push($arr_payments, $payment_1);
	
$payment_2 = new XeroAPI\XeroPHP\Models\Accounting\Payment;
$payment_2->setInvoice($invoice)
	->setAccount($bankaccount)
	->setAmount("2.00");
array_push($arr_payments, $payment_2);
			
$payments = new XeroAPI\XeroPHP\Models\Accounting\Payments;
$payments->setPayments($arr_payments);

$result = $apiInstance->createPayment($xeroTenantId,$payments);
//[/Payments:CreateMulti]
		
		$str = $str . "Create Payment ID: " . $result->getPayments()[0]->getPaymentID() . "<br>" ;
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function deletePayment($xeroTenantId,$apiInstance)
	{
		$str = '';

		$newPayment = $this->createPayment($xeroTenantId,$apiInstance,true);
		$paymentId = $newPayment->getPayments()[0]->getPaymentID();

//[Payments:Delete]
$payment = new XeroAPI\XeroPHP\Models\Accounting\Payment;
$payment->setPaymentID($paymentId)
        ->setStatus(XeroAPI\XeroPHP\Models\Accounting\PAYMENT::STATUS_DELETED);
	
$result = $apiInstance->deletePayment($xeroTenantId,$paymentId,$payment);
//[/Payments:Delete]
		
		$str = $str . "Payment deleted ID: " . $result->getPayments()[0]->getPaymentId() ."<br>" ;
		
		return $str;
	}

	
	public function getPrepayment($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Prepayments:Read]
// READ ALL 
$result = $apiInstance->getPrepayments($xeroTenantId); 						
//[/Prepayments:Read]
		$str = $str . "Get Prepayments: " . count($result->getPrepayments()) . "<br>";
		
		if($returnObj) {
			return $result->getPrepayments()[0];
		} else {
			return $str;
		}
	}


	public function createPrepayment($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$lineitem = $this->getLineItemForPrepayment($xeroTenantId,$apiInstance);
		$lineitems = [];		
		array_push($lineitems, $lineitem);

		$getAccount = $this->getBankAccount($xeroTenantId,$apiInstance);
		$accountId = $getAccount->getAccounts()[0]->getAccountId();
		$accountCode = $getAccount->getAccounts()[0]->getCode();

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();

		if (count($getAccount->getAccounts())) {

//[Prepayments:Create]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactId($contactId);

$bankAccount = new XeroAPI\XeroPHP\Models\Accounting\Account;
$bankAccount->setCode($accountCode)
	->setAccountId($accountId);

$prepayment = new XeroAPI\XeroPHP\Models\Accounting\BankTransaction;
$prepayment->setReference('Ref-' . $this->getRandNum())
	->setDate(new DateTime('2017-01-02'))
	->setType(XeroAPI\XeroPHP\Models\Accounting\BankTransaction::TYPE_RECEIVE_PREPAYMENT) 
	->setLineItems($lineitems)
	->setContact($contact)
	->setLineAmountTypes("NoTax")
	->setBankAccount($bankAccount)
	->setReference("Sid Prepayment 2");

$result = $apiInstance->createBankTransactions($xeroTenantId,$prepayment); 
//[/Prepayments:Create]
		}

		$str = $str . "Created prepayment ID: " . $result->getBankTransactions()[0]->getPrepaymentId() . "<br>";

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}
 
	public function allocatePrepayment($xeroTenantId,$apiInstance)
	{
		$str = '';

		$invNew = $this->createInvoiceAccRec($xeroTenantId,$apiInstance,true);
		$invoiceId = $invNew->getInvoices()[0]->getInvoiceID();
		$newPrepayement = $this->createPrepayment($xeroTenantId,$apiInstance,true);
		$prepaymentId = $newPrepayement->getBankTransactions()[0]->getPrepaymentId();

//[Prepayments:Allocate]
$invoice = new XeroAPI\XeroPHP\Models\Accounting\Invoice ;
$invoice->setInvoiceID($invoiceId);

$prepayment = new XeroAPI\XeroPHP\Models\Accounting\Prepayment ;
$prepayment->setPrepaymentID($prepaymentId);

$allocation = new XeroAPI\XeroPHP\Models\Accounting\Allocation;
$allocation->setInvoice($invoice)
	->setAmount("1.00");
$arr_allocation = [];		
array_push($arr_allocation, $allocation);

$allocations = new XeroAPI\XeroPHP\Models\Accounting\Allocations;	
$allocations->setAllocations($arr_allocation);

$result = $apiInstance->createPrepaymentAllocation($xeroTenantId,$prepaymentId,$allocation);
//[/Prepayments:Allocate]
		
		$str = $str . "Allocate Prepayment amount: " . $result->getAllocations()[0]->getAmount() . "<br>" ;
		
		return $str;
	}

	public function refundPrepayment($xeroTenantId,$apiInstance)
	{
		$str = '';

		$account = $this->getBankAccount($xeroTenantId,$apiInstance);
		$accountId = $account->getAccounts()[0]->getAccountId();
		$newPrepayment = $this->createPrepayment($xeroTenantId,$apiInstance,true);
		$prepaymentId = $newPrepayment->getBankTransactions()[0]->getPrepaymentID();

//[Prepayments:Refund]
$bankaccount = new XeroAPI\XeroPHP\Models\Accounting\Account;
$bankaccount->setAccountId($accountId);

$prepayment = new XeroAPI\XeroPHP\Models\Accounting\Prepayment;
$prepayment->setPrepaymentId($prepaymentId);

$payment = new XeroAPI\XeroPHP\Models\Accounting\Payment;
$payment->setPrepayment($prepayment)
	->setAccount($bankaccount)
	->setAmount("2.00");

$result = $apiInstance->createPayment($xeroTenantId,$payment);
//[/Prepayments:Refund]

		$str = $str . "Create Prepayment Refund (Payments ID): " . $result->getPayments()[0]->getPaymentId()  ." <br>" ;
		
		return $str;
	}	

	public function getPurchaseOrder($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[PurchaseOrders:Read]
// READ ALL 
$result = $apiInstance->getPurchaseOrders($xeroTenantId); 						
//[/PurchaseOrders:Read]

		$str = $str . "Total purchase orders: " . count($result->getPurchaseOrders()) . "<br>";
		
		if($returnObj) {
			return $result->getPurchaseOrders()[0];
		} else {
			return $str;
		}
	}

	public function createPurchaseOrders($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$lineitem = $this->getLineItemForPurchaseOrder($xeroTenantId,$apiInstance);
		$lineitems = [];		
		array_push($lineitems, $lineitem);

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();

//[PurchaseOrders:Create]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactId($contactId);

$arr_purchaseorders = [];	
$purchaseorder_1 = new XeroAPI\XeroPHP\Models\Accounting\PurchaseOrder;
$purchaseorder_1->setReference('Ref original -' . $this->getRandNum())
	->setContact($contact)
	->setLineItems($lineitems);
array_push($arr_purchaseorders, $purchaseorder_1);

$purchaseorder_2 = new XeroAPI\XeroPHP\Models\Accounting\PurchaseOrder;
$purchaseorder_2->setReference('Ref original -' . $this->getRandNum())
	->setContact($contact)
	->setLineItems($lineitems);
array_push($arr_purchaseorders, $purchaseorder_2);
		
$mypurchaseorders = new XeroAPI\XeroPHP\Models\Accounting\PurchaseOrders;
$mypurchaseorders.setPurchaseOrders($arr_purchaseorders);

$result = $apiInstance->createPurchaseOrders($xeroTenantId,$purchaseorders);
//[/PurchaseOrders:Create]
		
		$str = $str . "Created PurchaseOrder Number: " . $result->getPurchaseOrders()[0]->getPurchaseOrderNumber() . " and Created PurchaseOrder Number: " . $result->getPurchaseOrders()[0]->getPurchaseOrderNumber() . "<br>" ;
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updatePurchaseOrder($xeroTenantId,$apiInstance)
	{
		$str = '';
	
		$new = $this->createPurchaseOrders($xeroTenantId,$apiInstance,true);
		$purchaseorderId = $new->getPurchaseOrders()[0]->getPurchaseOrderID();

//[PurchaseOrders:Update]
$purchaseorder = new XeroAPI\XeroPHP\Models\Accounting\PurchaseOrder;
$purchaseorder->setReference('New Ref -' . $this->getRandNum());
$result = $apiInstance->updatePurchaseOrder($xeroTenantId,$purchaseorderId,$purchaseorder);
//[/PurchaseOrders:Update]

		$str = $str . "Updated Purchase Order: " . $result->getPurchaseOrders()[0]->getReference() . "<br>";
		
		return $str;
	}

	public function deletePurchaseOrder($xeroTenantId,$apiInstance)
	{
		$str = '';
	
		$new = $this->createPurchaseOrders($xeroTenantId,$apiInstance,true);
		$purchaseorderId = $new->getPurchaseOrders()[0]->getPurchaseOrderID();

//[PurchaseOrders:Delete]
$purchaseorder = new XeroAPI\XeroPHP\Models\Accounting\PurchaseOrder;
$purchaseorder->setStatus(XeroAPI\XeroPHP\Models\Accounting\PurchaseOrder::STATUS_DELETED);
$result = $apiInstance->updatePurchaseOrder($xeroTenantId,$purchaseorderId,$purchaseorder);
//[/PurchaseOrders:Delete]

		$str = $str . "Deleted PurchaseOrder: " . $result->getPurchaseOrders()[0]->getReference() . "<br>";
		
		return $str;
	}

	public function getQuotes($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Quotes:Read]
// READ ALL 
$result = $apiInstance->getQuotes($xeroTenantId); 						
//[/Quotes:Read]

		$str = $str . "Total quotes: " . count($result) . "<br>";
		
		if($returnObj) {
			return $result[0];
		} else {
			return $str;
		}
	}

	public function createQuotes($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$lineitem = $this->getLineItem($xeroTenantId,$apiInstance);
		$lineitems = [];		
		array_push($lineitems, $lineitem);

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();

//[Quotes:Create]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactId($contactId);

$arr_quotes = [];	
$quote_1 = new XeroAPI\XeroPHP\Models\Accounting\Quote;
$quote_1->setDate(new DateTime('2020-06-02'))
	->setContact($contact)
	->setLineItems($lineitems);
array_push($arr_quotes, $quote_1);

$quote_2 = new XeroAPI\XeroPHP\Models\Accounting\Quote;
$quote_2->setDate(new DateTime('2020-06-12'))
	->setContact($contact)
	->setLineItems($lineitems);
array_push($arr_quotes, $quote_2);
		
$quotes_obj = new XeroAPI\XeroPHP\Models\Accounting\Quotes;
$quotes_obj->setQuotes($arr_quotes);

$result = $apiInstance->createQuotes($xeroTenantId,$quotes_obj);
//[/Quotes:Create]
		
		$str = $str . "Created Quotes Number: " . $result[0]->getQuoteNumber() . " and Created Quote Number: " . $result[1]->getQuoteNumber() . "<br>" ;
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateOrCreateQuotes($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$lineitem = $this->getLineItem($xeroTenantId,$apiInstance);
		$lineitems = [];		
		array_push($lineitems, $lineitem);

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();

//[Quotes:UpdateOrCreate]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactId($contactId);

$arr_quotes = [];	
$quote_1 = new XeroAPI\XeroPHP\Models\Accounting\Quote;
$quote_1->setDate(new DateTime('2020-06-02'))
	->setContact($contact)
	->setLineItems($lineitems);
array_push($arr_quotes, $quote_1);

$quote_2 = new XeroAPI\XeroPHP\Models\Accounting\Quote;
$quote_2->setContact($contact)
	->setLineItems($lineitems);
array_push($arr_quotes, $quote_2);
		
$quotes_obj = new XeroAPI\XeroPHP\Models\Accounting\Quotes;
$quotes_obj->setQuotes($arr_quotes);

$result = $apiInstance->updateOrCreateQuotes($xeroTenantId,$quotes_obj,false);
//[/Quotes:UpdateOrCreate]
		
		$str = $str . "Created Quotes Number: " . $result[0]->getQuoteNumber() . " and Created Quote Number: " . $result[1]->getQuoteNumber() . "<br>" ;
		
		if(count($result[1]->getValidationErrors()) > 0) 
		{
			$str = $str . 'Error message: ' . $result[1]->getValidationErrors()[0]->getMessage();
		}

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function getQuote($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';
		$quote = $this->getQuotes($xeroTenantId,$apiInstance,true); 	
		$quoteId = $quote->getQuoteId();

//[Quote:Read]
// READ ALL 
$result = $apiInstance->getQuote($xeroTenantId, $quoteId); 						
//[/Quote:Read]

		$str = $str . "Get quote: " . $result[0]->getQuoteNumber() . "<br>";
		
		if($returnObj) {
			return $result[0];
		} else {
			return $str;
		}
	}

	public function updateQuote($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$quote = $this->getQuotes($xeroTenantId,$apiInstance,true); 	
		$quoteId = $quote->getQuoteId();

		$lineitem = $this->getLineItem($xeroTenantId,$apiInstance);
		$lineitems = [];		
		array_push($lineitems, $lineitem);

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();

//[Quote:Update]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactId($contactId);

$arr_quotes = [];	
$quote_1 = new XeroAPI\XeroPHP\Models\Accounting\Quote;
$quote_1->setDate(new DateTime('2020-04-01'))
	->setContact($contact)
	->setLineItems($lineitems);
array_push($arr_quotes, $quote_1);

$quotes_obj = new XeroAPI\XeroPHP\Models\Accounting\Quotes;
$quotes_obj->setQuotes($arr_quotes);

$result = $apiInstance->updateQuote($xeroTenantId, $quoteId, $quotes_obj);
//[/Quote:Update]
		
		$str = $str  . "Updated Quote Number: " . $result[0]->getQuoteNumber() . "<br>" ;
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function getReceipt($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Receipts:Read]
// READ ALL 
$result = $apiInstance->getReceipts($xeroTenantId); 						
//[/Receipts:Read]
		$str = $str . "Get Receipts: " . count($result->getReceipts()) . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}


	public function createReceipt($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$allUsers = $this->getUser($xeroTenantId,$apiInstance,true);
		$userId = $allUsers->getUsers()[0]->getUserID();

		$lineitem = $this->getLineItemForReceipt($xeroTenantId,$apiInstance);

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();
		
		if (count($allUsers->getUsers())) {	
//[Receipts:Create]
$lineitems = [];
array_push($lineitems, $lineitem);
$user = new XeroAPI\XeroPHP\Models\Accounting\User;
$user->setUserID($userId);

$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactId($contactId);

// CREATE RECEIPT
$receipt = new XeroAPI\XeroPHP\Models\Accounting\Receipt;
$receipt->setDate(new DateTime('2017-01-02'))
		->setLineItems($lineitems)
		->setContact($contact)
		->setTotal(20.00)
		->setUser($user);

$receipts = new XeroAPI\XeroPHP\Models\Accounting\Receipts;
$arr_receipts = [];
array_push($arr_receipts, $receipt);
$receipts->setReceipts($arr_receipts);
$result = $apiInstance->createReceipt($xeroTenantId,$receipts); 
//[/Receipts:Create]
		}

		$str = $str . "Create Receipt: " . $result->getReceipts()[0]->getReceiptID() . "<br>" ;
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}


	public function updateReceipt($xeroTenantId,$apiInstance)
	{
		$str = '';
	
		$new = $this->createReceipt($xeroTenantId,$apiInstance,true);
		$receiptId = $new->getReceipts()[0]->getReceiptID();
		$user = new XeroAPI\XeroPHP\Models\Accounting\User;
		$user->setUserID($new->getReceipts()[0]->getUser()->getUserId());

//[Receipts:Update]
$receipt = new XeroAPI\XeroPHP\Models\Accounting\Receipt;
$receipt->setReference('Add Ref to receipt ' . $this->getRandNum())
        ->setUser($user);
$receipts = new XeroAPI\XeroPHP\Models\Accounting\Receipts;
$arr_receipts = [];
array_push($arr_receipts, $receipt);
$receipts->setReceipts($arr_receipts);
$result = $apiInstance->updateReceipt($xeroTenantId,$receiptId,$receipts);
//[/Receipts:Update]

		$str = $str . "Updated Receipt: " . $result->getReceipts()[0]->getReceiptID() . "<br>";
		
		return $str;
	}

	public function getRepeatingInvoice($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[RepeatingInvoices:Read]
// READ ALL 
$result = $apiInstance->getRepeatingInvoices($xeroTenantId); 						
//[/RepeatingInvoices:Read]
		$str = $str . "Get RepeatingInvoices: " . count($result->getRepeatingInvoices()) . "<br>";
		
		if($returnObj) {
			return $result->getRepeatingInvoices()[0];
		} else {
			return $str;
		}
	}

// REPORTS
	public function getTenNinetyNine($xeroTenantId,$apiInstance)
	{
		$str = '';

//[Reports:TenNinetyNine]
$result = $apiInstance->getReportTenNinetyNine($xeroTenantId,2018);
//[/Reports:TenNinetyNine]

		$str = $str . "Report ID: " . $result->getReports()[0]->getReportName();

		return $str;
	}

	public function getAgedPayablesByContact($xeroTenantId,$apiInstance)
	{
		$str = '';

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();
//[Reports:AgedPayablesByContact]
$result = $apiInstance->getReportAgedPayablesByContact($xeroTenantId,$contactId);
//[/Reports:AgedPayablesByContact]

		$str = $str . "Report ID: " . $result->getReports()[0]->getReportId();

		return $str;
	}


	public function getAgedReceivablesByContact($xeroTenantId,$apiInstance)
	{
		$str = '';

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();

//[Reports:AgedReceivablesByContact]
$result = $apiInstance->getReportAgedReceivablesByContact($xeroTenantId,$contactId);
//[/Reports:AgedReceivablesByContact]

		$str = $str . "Report ID: " . $result->getReports()[0]->getReportId();

		return $str;
	}

	public function getBalanceSheet($xeroTenantId,$apiInstance)
	{
		$str = '';

//[Reports:BalanceSheet]
$result = $apiInstance->getReportBalanceSheet($xeroTenantId);
//[/Reports:BalanceSheet]
		
		$str = $str . "Report ID: " . $result->getReports()[0]->getReportId();

		return $str;
	}

	public function getBankSummary($xeroTenantId,$apiInstance)
	{
		$str = '';

//[Reports:BankSummary]
$result = $apiInstance->getReportBankSummary($xeroTenantId);
//[/Reports:BankSummary]

		$str = $str . "Report ID: " . $result->getReports()[0]->getReportId();


		return $str;
	}

	public function getBudgetSummary($xeroTenantId,$apiInstance)
	{
		$str = '';

//[Reports:BudgetSummary]
$result = $apiInstance->getReportBudgetSummary($xeroTenantId);
//[/Reports:BudgetSummary]

		$str = $str . "Report ID: " . $result->getReports()[0]->getReportId();

		return $str;
	}

	public function getExecutiveSummary($xeroTenantId,$apiInstance)
	{
		$str = '';

//[Reports:ExecutiveSummary]
$result = $apiInstance->getReportExecutiveSummary($xeroTenantId);
//[/Reports:ExecutiveSummary]

		$str = $str . "Report ID: " . $result->getReports()[0]->getReportId();

		return $str;
	}

	public function getProfitAndLoss($xeroTenantId,$apiInstance)
	{
		$str = '';

//[Reports:ProfitAndLoss]
$result = $apiInstance->getReportProfitandLoss($xeroTenantId);
//[/Reports:ProfitAndLoss]
		
		$str = $str . "Report ID: " . $result->getReports()[0]->getReportId();

		return $str;
	}

	public function getTrialBalance($xeroTenantId,$apiInstance)
	{
		$str = '';

//[Reports:TrialBalance]
$result = $apiInstance->getReportTrialBalance($xeroTenantId);
//[/Reports:TrialBalance]
		
		$str = $str . "Report ID: " . $result->getReports()[0]->getReportId();

		return $str;
	}

	public function getTaxRate($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[TaxRates:Read]
// READ ALL 
$result = $apiInstance->getTaxRates($xeroTenantId); 						
//[/TaxRates:Read]
		$str = $str . "Get TaxRates: " . count($result->getTaxRates()) . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function createTaxRates($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[TaxRates:Create]
$taxcomponent = new XeroAPI\XeroPHP\Models\Accounting\TaxComponent;
$taxcomponent->setName('Tax-' . $this->getRandNum())
             ->setRate(5);

$arr_taxcomponent = [];
array_push($arr_taxcomponent, $taxcomponent);

$taxrate = new XeroAPI\XeroPHP\Models\Accounting\TaxRate;
$taxrate->setName('Rate -' . $this->getRandNum())
        ->setTaxComponents($arr_taxcomponent);

$result = $apiInstance->createTaxRates($xeroTenantId,$taxrate); 
//[/TaxRates:Create]
		
		$str = $str . "Create TaxRate: " . $result->getTaxRates()[0]->getName() . "<br>" ;
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateTaxRate($xeroTenantId,$apiInstance)
	{
		$str = '';
	
		$newTaxRate = $this->createTaxRates($xeroTenantId,$apiInstance,true);
		$taxName = $newTaxRate->getTaxRates()[0]->getName();

//[TaxRates:Update]
$taxrate = new XeroAPI\XeroPHP\Models\Accounting\TaxRate;
$taxrate->setName($taxName)
        ->setStatus(XeroAPI\XeroPHP\Models\Accounting\TaxRate::STATUS_DELETED);
$result = $apiInstance->updateTaxRate($xeroTenantId,$taxrate); 
//[/TaxRates:Update]
		$str = $str . "Update TaxRate: " . $result->getTaxRates()[0]->getName() . "<br>";
		return $str;
	}

	public function getTrackingCategory($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[TrackingCategories:Read]
// READ ALL 
$result = $apiInstance->getTrackingCategories($xeroTenantId); 						
//[/TrackingCategories:Read]
		$str = $str . "Get TrackingCategories: " . count($result->getTrackingCategories()) . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}


	public function createTrackingCategory($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';	

//[TrackingCategories:Create]
$trackingcategory = new XeroAPI\XeroPHP\Models\Accounting\TrackingCategory;
$trackingcategory->setName('Avengers -' . $this->getRandNum());
$result = $apiInstance->createTrackingCategory($xeroTenantId,$trackingcategory); 
//[/TrackingCategories:Create]
		
		$str = $str . "Create TrackingCategory: " . $result->getTrackingCategories()[0]->getName() . "<br>" ;
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateTrackingCategory($xeroTenantId,$apiInstance)
	{
		$str = '';
	
		$trackingCategories = $this->getTrackingCategory($xeroTenantId,$apiInstance,true);
		$trackingCategory = $trackingCategories->getTrackingCategories()[0];
		$trackingCategoryId = $trackingCategory->getTrackingCategoryId();

//[TrackingCategories:Update]
$trackingCategory->setName('Foobar' . $this->getRandNum());
$result = $apiInstance->updateTrackingCategory($xeroTenantId,$trackingCategoryId,$trackingCategory); 
//[/TrackingCategories:Update]

		$str = $str . "Update TrackingCategory: " . $result->getTrackingCategories()[0]->getName() . "<br>";
		
		return $str;
	}

// WEIRD VALIDATION

	//https://api-admin.hosting.xero.com/History/Detail?id=abdb9c2b-1f4c-42d3-bf3e-0665c4a4974c
	public function archiveTrackingCategory($xeroTenantId,$apiInstance)
	{
		$str = '';

		$getTrackingCategories = $this->getTrackingCategory($xeroTenantId,$apiInstance,true);
		$getTrackingCategory = $getTrackingCategories->getTrackingCategories()[0];
		$trackingCategoryId = $getTrackingCategory->getTrackingCategoryId();

//[TrackingCategories:Archive]
$trackingcategory = new XeroAPI\XeroPHP\Models\Accounting\TrackingCategory;
$trackingcategory->setStatus(\XeroAPI\XeroPHP\Models\Accounting\TrackingCategory::STATUS_ARCHIVED);
$result = $apiInstance->updateTrackingCategory($xeroTenantId,$trackingCategoryId,$trackingcategory); 
//[/TrackingCategories:Archive]

		$str = $str . "Archive TrackingCategory: " . $result->getTrackingCategories()[0]->getName()  . "<br>";
		
		return $str;
	}

	public function deleteTrackingCategory($xeroTenantId,$apiInstance)
	{
		$str = '';

		$trackingCategories = $this->getTrackingCategory($xeroTenantId,$apiInstance,true);
		$trackingCategory = $trackingCategories->getTrackingCategories()[0];
		$trackingCategoryId = $trackingCategory->getTrackingCategoryId();

//[TrackingCategories:Delete]
$result = $apiInstance->deleteTrackingCategory($xeroTenantId,$trackingCategoryId); 
//[/TrackingCategories:Delete]
		$str = $str . "Delete TrackingCategory: " . $result->getTrackingCategories()[0]->getName() . "<br>";
				
		return $str;
	}

	public function createTrackingOptions($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';	
		$trackingCategories = $this->getTrackingCategory($xeroTenantId,$apiInstance,true);
		$trackingCategory = $trackingCategories->getTrackingCategories()[0];
		$trackingCategoryId = $trackingCategory->getTrackingCategoryId();

//[TrackingOptions:Create]
$option = new XeroAPI\XeroPHP\Models\Accounting\TrackingOption;
$option->setName('IronMan -' . $this->getRandNum());
$result = $apiInstance->createTrackingOptions($xeroTenantId,$trackingCategoryId,$option); 
//[/TrackingOptions:Create]

		$str = $str . "Create TrackingOptions now Total: " . count($result->getOptions()) . "<br>" ;
		
		return $str;
	}

	public function deleteTrackingOptions($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';	
		$trackingCategories = $this->getTrackingCategory($xeroTenantId,$apiInstance,true);
		$trackingCategory = $trackingCategories->getTrackingCategories()[0];
		$trackingCategoryId = $trackingCategory->getTrackingCategoryId();
		$optionId = $trackingCategory->getOptions()[3]->getTrackingOptionId();

//[TrackingOptions:Delete]
$result = $apiInstance->deleteTrackingOptions($xeroTenantId,$trackingCategoryId,$optionId); 
//[/TrackingOptions:Delete]
		$str = $str . "Delete TrackingOptions Name: " . $result->getOptions()[0]->getName() . "<br>" ;

		return $str;
	}

	public function updateTrackingOptions($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';	

		$trackingCategories = $this->getTrackingCategory($xeroTenantId,$apiInstance,true);
		$trackingCategory = $trackingCategories->getTrackingCategories()[0];
		$trackingCategoryId = $trackingCategory->getTrackingCategoryId();
		$optionId = $trackingCategory->getOptions()[0]->getTrackingOptionId();
		
//[TrackingOptions:Update]
$option = new XeroAPI\XeroPHP\Models\Accounting\TrackingOption;
$option->setName('Hello' . $this->getRandNum());
$result = $apiInstance->updateTrackingOptions($xeroTenantId,$trackingCategoryId,$optionId,$option); 
//[/TrackingOptions:Update]

		$str = $str . "Update TrackingOptions Name: " . $result->getOptions()[0]->getName() . "<br>" ;

		return $str;
	}

	public function getUser($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Users:Read]
// READ ALL 
$result = $apiInstance->getUsers($xeroTenantId); 						
//[/Users:Read]
		$str = $str . "Get Users: " . count($result->getUsers()) . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

/*
FIXED ASSETS
following methods for assets endpoints

*/

	public function getAsset($xeroTenantId,$assetsApi,$returnObj=false)
	{
		$str = '';
		$assets = $this->getAssets($xeroTenantId,$assetsApi,true);
		$assetId = $assets->getItems()[0]->getAssetId();
//[Asset:Read]
$result = $assetsApi->getAssetById($xeroTenantId, $assetId); 						
//[/Asset:Read]

		$str = $str . "Get specific Fixed Asset: " . $result->getAssetName() . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function createAsset($xeroTenantId,$assetsApi,$returnObj=false)
	{
		$str = '';
		
//[Asset:Create]
$asset = new XeroAPI\XeroPHP\Models\Asset\Asset;
$asset->setAssetName('Computer -' . $this->getRandNum())
	->setAssetNumber($this->getRandNum())
	->setPurchaseDate((new DateTime('2019-01-02')))
	->setPurchasePrice(100.0)
	->setDisposalPrice(23.23)
	->setAssetStatus("Draft");

$result = $assetsApi->createAsset($xeroTenantId, $asset); 						
//[/Asset:Create]

		$str = $str . "Get specific Fixed Asset: " . $result->getAssetName() . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateAsset($xeroTenantId,$assetsApi,$returnObj=false)
	{
		$str = '';
		$new = $this->createAsset($xeroTenantId,$assetsApi,true);
		$assetId = $new->getAssetId();
		
//[Asset:Update]
$bookDepreciationDetail = new XeroAPI\XeroPHP\Models\Asset\BookDepreciationDetail;
$asset = new XeroAPI\XeroPHP\Models\Asset\Asset;
$asset->setAssetName('Latop -' . $this->getRandNum())
	  ->setAssetNumber($this->getRandNum())
	  ->setAssetStatus("Draft")
	  ->setAssetId($assetId);
$result = $assetsApi->createAsset($xeroTenantId, $asset); 						
//[/Asset:Update]

		$str = $str . "Get specific Fixed Asset: " . $result->getAssetName() . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function getAssets($xeroTenantId,$assetsApi,$returnObj=false)
	{
		$str = '';

//[Assets:Read]
// read all assets with status of DRAFT
$result = $assetsApi->getAssets($xeroTenantId,"DRAFT" ); 						
//[/Assets:Read]

		$str = $str . "Get total Fixed Assets: " . $result->getPagination()->getItemCount() . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function getAssetTypes($xeroTenantId,$assetsApi,$returnObj=false)
	{
		$str = '';

//[AssetTypes:Read]
$result = $assetsApi->getAssetTypes($xeroTenantId); 						
//[/AssetTypes:Read]

		$str = $str . "Get total Asset Types: " . count($result) . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function createAssetType($xeroTenantId,$assetsApi,$apiInstance, $returnObj=false)
	{
		$str = '';
		$where = 'Status=="' . \XeroAPI\XeroPHP\Models\Accounting\Account::STATUS_ACTIVE .'" AND Type=="' .  \XeroAPI\XeroPHP\Models\Accounting\AccountType::FIXED . '"';
		$accountFixedAsset = $apiInstance->getAccounts($xeroTenantId, null, $where); 
		$fixedAssetAccountId = $accountFixedAsset->getAccounts()[0]->getAccountId(); 

		$where = 'Status=="' . \XeroAPI\XeroPHP\Models\Accounting\Account::STATUS_ACTIVE .'" AND Type=="' .  \XeroAPI\XeroPHP\Models\Accounting\AccountType::EXPENSE . '"';
		$accountDepreciationExpense = $apiInstance->getAccounts($xeroTenantId, null, $where); 
		$depreciationExpenseAccountId = $accountDepreciationExpense->getAccounts()[0]->getAccountId(); 

		$where = 'Status=="' . \XeroAPI\XeroPHP\Models\Accounting\Account::STATUS_ACTIVE .'" AND Type=="' .  \XeroAPI\XeroPHP\Models\Accounting\AccountType::DEPRECIATN . '"';
		$accountAccumulatedDepreciation = $apiInstance->getAccounts($xeroTenantId, null, $where); 
		$accumulatedDepreciationAccountId = $accountAccumulatedDepreciation->getAccounts()[0]->getAccountId(); 

//[AssetType:Create]
$depreciationRate = floatval(0.5);
$bookDepreciationSetting = new XeroAPI\XeroPHP\Models\Asset\BookDepreciationSetting;
$bookDepreciationSetting->setAveragingMethod(\XeroAPI\XeroPHP\Models\Asset\BookDepreciationSetting::AVERAGING_METHOD_ACTUAL_DAYS)
					->setDepreciationCalculationMethod(\XeroAPI\XeroPHP\Models\Asset\BookDepreciationSetting::DEPRECIATION_CALCULATION_METHOD_NONE)
					->setDepreciationRate($depreciationRate)
					->setDepreciationMethod(\XeroAPI\XeroPHP\Models\Asset\BookDepreciationSetting::DEPRECIATION_METHOD_DIMINISHING_VALUE100);

$assetType = new XeroAPI\XeroPHP\Models\Asset\AssetType;
$assetType->setAssetTypeName('Computer -' . $this->getRandNum())
	->setFixedAssetAccountId($fixedAssetAccountId)
	->setDepreciationExpenseAccountId($depreciationExpenseAccountId)
	->setAccumulatedDepreciationAccountId($accumulatedDepreciationAccountId)
	->setBookDepreciationSetting($bookDepreciationSetting);

$result = $assetsApi->createAssetType($xeroTenantId, $assetType); 						
//[/AssetType:Create]

		$str = $str . "Get specific Fixed Asset: " . $result->getAssetTypeName() . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function getAssetSettings($xeroTenantId,$assetsApi,$returnObj=false)
	{
		$str = '';

//[AssetTypes:Read]
$result = $assetsApi->getAssetSettings($xeroTenantId); 						
//[/AssetTypes:Read]

		$str = $str . "Get Asset number prefix: " . $result->getAssetNumberPrefix() . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function getProject($xeroTenantId,$projectApi,$returnObj=false)
	{
		$str = '';
		$one = $this->getProjects($xeroTenantId,$projectApi,true);
		$projectId = $one->getItems()[0]->getProjectId();
//[Project:Read]
$result = $projectApi->getProject($xeroTenantId,$projectId); 						
//[/Project:Read]

		$str = $str . "Get project name: " . $result->getName() . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function createProject($xeroTenantId,$projectApi,$accountingApi,$returnObj=false)
	{
		$str = '';
		$new = $this->createContacts($xeroTenantId,$accountingApi,true);
		$contactId = $new->getContacts()[0]->getContactId();
//[Project:Create]
$projectCreateOrUpdate = new XeroAPI\XeroPHP\Models\Project\ProjectCreateOrUpdate;
$projectCreateOrUpdate->setContactId($contactId)
	->setName("New Fence")
	->setDeadlineUtc(new DateTime('2019-12-10T12:59:59Z'))
	->setEstimateAmount(199.00);
	
$result = $projectApi->createProject($xeroTenantId,$projectCreateOrUpdate); 						
//[/Project:Create]

		$str = $str . "Create project name: " . $result->getName() . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateProject($xeroTenantId,$projectApi,$accountingApi,$returnObj=false)
	{
		$str = '';
		$new = $this->createProject($xeroTenantId,$projectApi,$accountingApi,true);
		$projectId = $new->getProjectId();
//[Project:Create]
$projectCreateOrUpdate = new XeroAPI\XeroPHP\Models\Project\ProjectCreateOrUpdate;
$projectCreateOrUpdate->setName("New Bathroom")
	->setDeadlineUtc(new DateTime('2019-12-10T12:59:59Z'))
	->setEstimateAmount(199.00);
	
$result = $projectApi->updateProject($xeroTenantId,$projectId,$projectCreateOrUpdate); 						
//[/Project:Create]

		$str = $str . "Create project name: " . $result->getName() . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function getProjects($xeroTenantId,$projectApi,$returnObj=false)
	{
		$str = '';

//[Projects:Read]
$result = $projectApi->getProjects($xeroTenantId); 						
//[/Projects:Read]

		$str = $str . "Get project count: " . $result->getPagination()->getItemCount() . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}


/*
Finance APIs
Following methods demonstrate Xero's 
Finance API endpoints
https://raw.githubusercontent.com/XeroAPI/Xero-OpenAPI/master/finance-yaml/xero_finance.yaml 
*/

public function getAccountingActivityAccountUsage($xeroTenantId,$financeApi,$returnObj=false)
	{
		$str = '';

		//[AccountingActivityAccountUsage:Read]
		$result = $financeApi->getAccountingActivityAccountUsage($xeroTenantId); 						
		//[/AccountingActivityAccountUsage:Read]
		$str = $str . "Get Accounting Activity Account Usage: <br>" . $result . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function getAccountingActivityLockHistory($xeroTenantId,$financeApi,$returnObj=false)
	{
		$str = '';

		//[AccountingActivityLockHistory:Read]
		$result = $financeApi->getAccountingActivityLockHistory($xeroTenantId); 						
		//[/AccountingActivityLockHistory:Read]
		$str = $str . "Get Accounting Activity Lock History: <br>" . $result . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

public function getAccountingActivityReportHistory($xeroTenantId,$financeApi,$returnObj=false)
	{
		$str = '';

		//[AccountingActivityReportHistory:Read]
		$result = $financeApi->getAccountingActivityReportHistory($xeroTenantId); 						
		//[/AccountingActivityReportHistory:Read]
		$str = $str . "Get Accounting Activity Report History: <br>" . $result . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

public function getAccountingActivityUserActivities($xeroTenantId,$financeApi,$returnObj=false)
	{
		$str = '';

		//[AccountingActivityUserActivities:Read]
		$result = $financeApi->getAccountingActivityUserActivities($xeroTenantId); 						
		//[/AccountingActivityUserActivities:Read]
		$str = $str . "Get Financial Statement Balance Sheet: <br>" . $result . "<br>";

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}


public function getCashValidation($xeroTenantId,$financeApi,$returnObj=false)
	{
		$str = '';

		//[CashValidation:Read]
		$result = $financeApi->getCashValidation($xeroTenantId); 						
		//[/CashValidation:Read]
		$resultstr ='';
		foreach($result as $value){
			//Print the element out.
			$resultstr .= $value. '<br>';
		}
		$str = $str . "Get cash validation: <br>" . $resultstr . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}


public function getFinancialStatementBalanceSheet($xeroTenantId,$financeApi,$returnObj=false)
	{
		$str = '';

		//[FinancialStatementBalanceSheet:Read]
		$result = $financeApi->getFinancialStatementBalanceSheet($xeroTenantId);					
		//[/FinancialStatementBalanceSheet:Read]

		$str = $str . "Get Financial Statement Balance Sheet: <br>" . $result . "<br>";
		
			if($returnObj) {
				return $result;
			} else {
				return $str;
			}
	}

	public function getFinancialStatementCashflow($xeroTenantId,$financeApi,$returnObj=false)
	{
		$str = '';

		//[FinancialStatementCashflow:Read]
		$result = $financeApi->getFinancialStatementCashflow($xeroTenantId); 						
		//[/FinancialStatementCashflow:Read]

		$str = $str . "Get Financial Statement Cash Flow: <br>" . $result . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function getFinancialStatementProfitAndLoss($xeroTenantId,$financeApi,$returnObj=false)
	{
		$str = '';

		//[FinancialStatementProfitAndLoss:Read]
		$result = $financeApi->getFinancialStatementProfitAndLoss($xeroTenantId); 						
		//[/FinancialStatementProfitAndLoss:Read]

		$str = $str . "Get Financial Statement Profit And Loss: <br>" . $result . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function getFinancialStatementTrialBalance($xeroTenantId,$financeApi,$returnObj=false)
	{
		$str = '';

		//[FinancialStatementTrialBalance:Read]
		$result = $financeApi->getFinancialStatementTrialBalance($xeroTenantId); 						
		//[/FinancialStatementTrialBalance:Read]

		$str = $str . "Get Financial Statement Trial Balance: <br>" . $result . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function getFinancialStatementContactsRevenue($xeroTenantId,$financeApi,$returnObj=false)
	{
		$str = '';

		//[FinancialStatementContactsRevenue:Read]
		$result = $financeApi->getFinancialStatementContactsRevenue($xeroTenantId); 						
		//[/FinancialStatementContactsRevenue:Read]

		$str = $str . "Get Financial Statement Contacts Revenue: <br>" . $result . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function getFinancialStatementContactsExpense($xeroTenantId,$financeApi,$returnObj=false)
	{
		$str = '';

		//[FinancialStatementContactsExpense:Read]
		$result = $financeApi->getFinancialStatementContactsExpense($xeroTenantId); 						
		//[/FinancialStatementContactsExpense:Read]

		$str = $str . "Get Financial Statement Contacts Expense: <br>" . $result . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

// HELPERS METHODS
	public function getRandNum()
	{
		$randNum = strval(rand(1000,100000)); 

		return $randNum;
	}

	public function getLineItem()
	{

		$lineitem = new XeroAPI\XeroPHP\Models\Accounting\LineItem;
		$lineitem->setDescription('Sample Item' . $this->getRandNum())
			->setQuantity(1)
			->setUnitAmount(20)
			->setAccountCode("400");

		return $lineitem;
	}	

	public function getLineItemForReceipt($xeroTenantId,$apiInstance)
	{
		$lineitem = new XeroAPI\XeroPHP\Models\Accounting\LineItem;
		$lineitem->setDescription('My Receipt 1 -' .  $this->getRandNum())
			->setQuantity(1)
			->setUnitAmount(20)
			->setAccountCode("123");

		return $lineitem;
	}	

	public function getLineItemForOverpayment($xeroTenantId,$apiInstance)
	{
		$account = $this->getAccRecAccount($xeroTenantId,$apiInstance);

		$lineitem = new XeroAPI\XeroPHP\Models\Accounting\LineItem;
		$lineitem->setDescription('INV-' . $this->getRandNum())
			->setQuantity(1)
			->setUnitAmount(20)
			->setAccountCode($account->getAccounts()[0]->getCode());
		return $lineitem;
	}


	public function getLineItemForPrepayment($xeroTenantId,$apiInstance)
	{
		$account = $this->getAccountExpense($xeroTenantId,$apiInstance);

		$lineitem = new XeroAPI\XeroPHP\Models\Accounting\LineItem;
		$lineitem->setDescription('Something-' . $this->getRandNum())
			->setQuantity(1)
			->setUnitAmount(20)
			->setAccountCode($account->getAccounts()[0]->getCode());
		return $lineitem;
	}

	public function getLineItemForPurchaseOrder($xeroTenantId,$apiInstance)
	{
		$account = $this->getAccountRevenue($xeroTenantId,$apiInstance);

		$lineitem = new XeroAPI\XeroPHP\Models\Accounting\LineItem;
		$lineitem->setDescription('PO-' . $this->getRandNum())
			->setQuantity(1)
			->setUnitAmount(20)
			->setAccountCode($account->getAccounts()[0]->getCode());
		return $lineitem;
	}

	public function getBankAccount($xeroTenantId,$apiInstance)
	{
		// READ only ACTIVE
		$where = 'Status=="' . \XeroAPI\XeroPHP\Models\Accounting\Account::STATUS_ACTIVE .'" AND Type=="' .  \XeroAPI\XeroPHP\Models\Accounting\Account::BANK_ACCOUNT_TYPE_BANK . '"';
		$result = $apiInstance->getAccounts($xeroTenantId, null, $where); 

		return $result;
	}	


	public function getAccRecAccount($xeroTenantId,$apiInstance)
	{
		$where = 'Status=="' . XeroAPI\XeroPHP\Models\Accounting\Account::STATUS_ACTIVE .'" AND SystemAccount=="' .  XeroAPI\XeroPHP\Models\Accounting\Account::SYSTEM_ACCOUNT_DEBTORS . '"';
		$result = $apiInstance->getAccounts($xeroTenantId, null, $where);
		
		return $result;
	}	

	public function getAccountExpense($xeroTenantId,$apiInstance)
	{

		$where = 'Status=="' . XeroAPI\XeroPHP\Models\Accounting\Account::STATUS_ACTIVE .'" AND Type=="' .  XeroAPI\XeroPHP\Models\Accounting\Account::MODEL_CLASS_EXPENSE . '"';
		
		$result = $apiInstance->getAccounts($xeroTenantId, null, $where);
		
		return $result;
	}	

	public function getAccountRevenue($xeroTenantId,$apiInstance)
	{

		$where = 'Status=="' . XeroAPI\XeroPHP\Models\Accounting\Account::STATUS_ACTIVE .'" AND Type=="' .  XeroAPI\XeroPHP\Models\Accounting\Account::MODEL_CLASS_REVENUE . '"';
		
		$result = $apiInstance->getAccounts($xeroTenantId, null, $where);
		
		return $result;
	}	

	public function createInvoiceAccPay($xeroTenantId,$apiInstance,$returnObj=false)
	{

		$str = '';
		
		$lineitems = [];		
		array_push($lineitems, $this->getLineItem());

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();

		$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
		$contact->setContactId($contactId);

$invoice = new XeroAPI\XeroPHP\Models\Accounting\Invoice;

$invoice->setReference('Ref-' . $this->getRandNum())
	->setDueDate(new DateTime('2017-01-02'))
	->setContact($contact)
	->setLineItems($lineitems)
	->setStatus(XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_AUTHORISED)
	->setType(XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCPAY)
	->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE);
$result = $apiInstance->createInvoices($xeroTenantId,$invoice); 

		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function createInvoiceDraft($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';
		
		$lineitems = [];		
		array_push($lineitems, $this->getLineItem());

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();

//[Invoices:Create]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactId($contactId);

$invoice = new XeroAPI\XeroPHP\Models\Accounting\Invoice;
$invoice->setReference('Ref-' . $this->getRandNum())
	->setDueDate(new DateTime('2017-01-02'))
	->setContact($contact)
	->setLineItems($lineitems)
	->setStatus(XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_DRAFT)
	->setType(XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCPAY)
	->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE);
$result = $apiInstance->createInvoices($xeroTenantId,$invoice); 
//[/Invoices:Create]
		
		$str = $str ."Create Invoice total amount: " . $result->getInvoices()[0]->getTotal() . "<br>" ;

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}	

	public function createInvoiceAccRec($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$lineitems = [];		
		array_push($lineitems, $this->getLineItem());

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();

		$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
		$contact->setContactId($contactId);

		$invoice = new XeroAPI\XeroPHP\Models\Accounting\Invoice;

		$invoice->setReference('Ref-' . $this->getRandNum())
			->setDueDate(new DateTime('2017-01-02'))
			->setContact($contact)
			->setLineItems($lineitems)
			->setStatus(XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_AUTHORISED)
			->setType(XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCREC)
			->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE);
		$result = $apiInstance->createInvoices($xeroTenantId,$invoice); 
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}	
	
	public function getJournalLineCredit()
	{
		$journalline = new XeroAPI\XeroPHP\Models\Accounting\ManualJournalLine;
		$journalline->setLineAmount("20.00")
			->setAccountCode("400");
		return $journalline;
	}

	public function getJournalLineDebit()
	{
		$journalline = new XeroAPI\XeroPHP\Models\Accounting\ManualJournalLine;
		$journalline->setLineAmount("-20.00")
			->setAccountCode("620");
		return $journalline;
	}


	public function createCreditNoteAuthorised($xeroTenantId,$apiInstance)
	{

		$str = '';

		$lineitems = [];		
		array_push($lineitems, $this->getLineItem());

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
		$contact->setContactId($getContact->getContacts()[0]->getContactId());

		$creditnote = new XeroAPI\XeroPHP\Models\Accounting\CreditNote;

		$creditnote->setDate(new DateTime('2017-01-02'))
			->setContact($contact)
			->setLineItems($lineitems)
			->setStatus(XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_AUTHORISED)
			->setType(XeroAPI\XeroPHP\Models\Accounting\CreditNote::TYPE_ACCPAYCREDIT);
		$result = $apiInstance->createCreditNotes($xeroTenantId,$creditnote); 

		return $result;	
	}

	public function getTaxComponent($xeroTenantId,$apiInstance)
	{
		$taxcomponent = new \XeroPHP\Models\Accounting\TaxRate\TaxComponent($xeroTenantId,$apiInstance);
		$taxcomponent->setName('Tax-' . $this->getRandNum())
			->setRate(5);
		return $taxcomponent;
	}

}
?>
