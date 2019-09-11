<?php
class ExampleClass
{
	public $apiInstance;

	function __construct() {
   	}

   	public function init($arg) {
		$apiInstance = $arg;
   	}

	public function getAccount($xeroTenantId,$apiInstance,$returnObj=false)
	{

		$str = '';
//[Accounts:Read]
// READ ALL 
$result = $apiInstance->getAccounts($xeroTenantId); 						
// READ only ACTIVE
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

	public function createAccount($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Accounts:Create]
$account = new XeroAPI\XeroPHP\Models\Accounting\Account;
$account->setCode($this->getRandNum());
$account->setName("Foo" . $this->getRandNum());
$account->setType("EXPENSE");
$account->setDescription("Hello World");	
$result = $apiInstance->createAccount($xeroTenantId,$account); 
//[/Accounts:Create]
		
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
		$guid = $new->getAccounts()[0]->getAccountId();								
					
//[Accounts:Update]
$account = new XeroAPI\XeroPHP\Models\Accounting\Account;
$account->setStatus(NULL);
$account->setDescription("Goodbye World");	
$result = $apiInstance->updateAccount($xeroTenantId,$guid,$account);  
//[/Accounts:Update]

		$str = $str . "Update Account: " . $result->getAccounts()[0]->getName() . "<br>" ;

		return $str;
	}

	public function archiveAccount($xeroTenantId,$apiInstance)
	{
		$str = '';

		$new = $this->createAccount($xeroTenantId,$apiInstance,true);
		$guid = $new->getAccounts()[0]->getAccountId();								
		
//[Accounts:Archive]
$account = new XeroAPI\XeroPHP\Models\Accounting\Account;
$account->setStatus("ARCHIVED");	
$result = $apiInstance->updateAccount($xeroTenantId,$guid,$account);  
//[/Accounts:Archive]

		$str = $str . "Archive Account: " . $result->getAccounts()[0]->getName() . "<br>" ;

		return $str;
	}

	public function deleteAccount($xeroTenantId,$apiInstance)
	{
		$str = '';

		$new = $this->createAccount($xeroTenantId,$apiInstance,true);
		$guid = $new->getAccounts()[0]->getAccountId();								
		 				
//[Accounts:Delete]
$result = $apiInstance->deleteAccount($xeroTenantId,$guid);
//[/Accounts:Delete]

		$str = $str . "Deleted Account: " . $result->getAccounts()[0]->getName() . "<br>" ;
		return $str;
	}


	public function attachmentAccount($xeroTenantId,$apiInstance)
	{
		$str = '';

		$account = $this->getAccount($xeroTenantId,$apiInstance,true);
//[Accounts:Attachment]
$guid = $account->getAccounts()[2]->getAccountId();
		
$filename = "./helo-heros.jpg";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
fclose($handle);

$result = $apiInstance->createAccountAttachmentByFileName($xeroTenantId,$guid,"helo-heros.jpg",$contents);
//[/Accounts:Attachment]
		$str =  "Account (". $result->getAttachments()[0]->getFileName() .") attachment url:";
		$str = $str . $result->getAttachments()[0]->getUrl();

		return $str;
	}


	public function getBankTransaction($xeroTenantId,$apiInstance)
	{	
		$str = '';
//[BankTransactions:Read]
// READ ALL
$result = $apiInstance->getBankTransactions($xeroTenantId); 						
// READ only ACTIVE
$where = 'Status=="AUTHORISED"';
$result2 = $apiInstance->getBankTransactions($xeroTenantId, null, $where); 
//[/BankTransactions:Read]

		$str = $str . "Get BankTransaction total: " . count($result->getBankTransactions()) . "<br>";
		$str = $str . "Get ACTIVE BankTransaction total: " . count($result2->getBankTransactions()) . "<br>";
		
		return $str;
	}

	public function createBankTransaction($xeroTenantId,$apiInstance,$returnObj=false)
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

$banktransaction = new XeroAPI\XeroPHP\Models\Accounting\BankTransaction;
$banktransaction->setReference('Ref-' . $this->getRandNum())
	->setDate(new DateTime('2017-01-02'))
	->setLineItems($lineitems)
	->setType("RECEIVE")
	->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE)
	->setBankAccount($bankAccount)
	->setContact($contact);

$result = $apiInstance->createBankTransaction($xeroTenantId, $banktransaction); 
//[/BankTransactions:Create]

		$str = $str ."Create Bank Transaction: " . $result->getBankTransactions()[0]->getReference();	
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
		
	}

	public function updateBankTransaction($xeroTenantId,$apiInstance)
	{
		$str = '';

		$new = $this->createBankTransaction($xeroTenantId,$apiInstance,true);
		$banktransactionId = $new->getBankTransactions()[0]->getBankTransactionId();

//[BankTransactions:Update]
$banktransaction = new XeroAPI\XeroPHP\Models\Accounting\BankTransaction;
$banktransaction->setReference("Goodbye World");
$result = $apiInstance->updateBankTransaction($xeroTenantId,$banktransactionId,$banktransaction);
//[/BankTransactions:Update]

		$str = $str . "Updated Bank Transaction: " . $result->getBankTransactions()[0]->getReference();

		return $str;
	}


	public function deleteBankTransaction($xeroTenantId,$apiInstance)
	{
		$account = $this->getBankAccount($xeroTenantId,$apiInstance,true);

		if (count((array)$account)) {
			$str = '';
			
			$new = $this->createBankTransaction($xeroTenantId,$apiInstance,true);
			$banktransactionId = $new->getBankTransactions()[0]->getBankTransactionId();

//[BankTransactions:Delete]
$banktransaction = new XeroAPI\XeroPHP\Models\Accounting\BankTransaction;
$banktransaction->setStatus(XeroAPI\XeroPHP\Models\Accounting\BankTransaction::STATUS_DELETED);
$result = $apiInstance->updateBankTransaction($xeroTenantId,$banktransactionId,$banktransaction);  
//[/BankTransactions:Delete]

			$str = $str . "Deleted Bank Transaction";

		} else {
			$str = $str . "No Bank Account Found - can't work with Transactions without it.";
		}
	
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

//[Contacts:Read]
// READ ALL 
$result = $apiInstance->getContacts($xeroTenantId); 		

// READ only ACTIVE
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

	public function createContact($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Contacts:Create]
$person = new XeroAPI\XeroPHP\Models\Accounting\ContactPerson;
$person->setFirstName("John")
	->setLastName("Smith")
	->setEmailAddress("john.smith@24locks.com")
	->setIncludeInEmails(true);

$persons = [];		
array_push($persons, $person);

$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setName('FooBar' . $this->getRandNum())
	->setFirstName("Foo" . $this->getRandNum())
	->setLastName("Bar" . $this->getRandNum())
	->setEmailAddress("ben.bowden@24locks.com")
	->setContactPersons($persons);	
$result = $apiInstance->createContact($xeroTenantId,$contact); 
//[/Contacts:Create]
		
		$str = $str ."Create Contact: " . $result->getContacts()[0]->getName() . "<br>";
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}	
	}

	public function updateContact($xeroTenantId,$apiInstance)
	{
		$str = '';
		
		$new = $this->createContact($xeroTenantId,$apiInstance,true);
		$contactId = $new->getContacts()[0]->getContactId();								
					
//[Contacts:Update]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setName("Goodbye" . $this->getRandNum());	
$result = $apiInstance->updateContact($xeroTenantId,$contactId,$contact);  
//[/Contacts:Update]

		$str = $str . "Update Contacts: " . $result->getContacts()[0]->getName() . "<br>" ;

		return $str;
	}
	
	public function archiveContact($xeroTenantId,$apiInstance)
	{
		$str = '';

		$new = $this->createContact($xeroTenantId,$apiInstance,true);
		$contactId = $new->getContacts()[0]->getContactId();								
					
//[Contacts:Archive]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactStatus(\XeroAPI\XeroPHP\Models\Accounting\Contact::CONTACT_STATUS_ARCHIVED);	
$result = $apiInstance->updateContact($xeroTenantId,$contactId,$contact);  
//[/Contacts:Archive]

		$str = $str . "Archive Contacts: " . $result->getContacts()[0]->getName() . "<br>" ;

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

$result = $apiInstance->createContactGroup($xeroTenantId,$contactgroup); 
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

		$new = $this->createContactGroup($xeroTenantId,$apiInstance,true);
		$contactgroupId = $new->getContactGroups()[0]->getContactGroupID();

//[ContactGroups:Archive]
$contactgroup = new XeroAPI\XeroPHP\Models\Accounting\ContactGroup;
$contactgroup->setStatus(XeroAPI\XeroPHP\Models\Accounting\ContactGroup::STATUS_DELETED);
$result = $apiInstance->updateContactGroup($xeroTenantId,$contactgroupId,$contactgroup);  
//[/ContactGroups:Archive]
		
		$str = $str . "Set Status to DELETE for ContactGroup: " . $new->getContactGroups()[0]->getName() . "<br>" ;

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

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();
		$getContactGroup = $this->getContactGroup($xeroTenantId,$apiInstance,true);
		$contactgroupId = $getContactGroup->getContactGroups()[0]->getContactGroupID();

//[ContactGroups:Remove]
$result = $apiInstance->deleteContactGroupContact($xeroTenantId,$contactgroupId,$contactId);  
//[/ContactGroups:Remove]

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


	public function createCreditNote($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$lineitems = [];		
		array_push($lineitems, $this->getLineItem());

		$getContact = $this->getContact($xeroTenantId,$apiInstance,true);
		$contactId = $getContact->getContacts()[0]->getContactId();
		
//[CreditNotes:Create]
$contact = new XeroAPI\XeroPHP\Models\Accounting\Contact;
$contact->setContactId($contactId);

$creditnote = new XeroAPI\XeroPHP\Models\Accounting\CreditNote;

$creditnote->setDate(new DateTime('2017-01-02'))
	->setContact($contact)
	->setLineItems($lineitems)
	->setType(XeroAPI\XeroPHP\Models\Accounting\CreditNote::TYPE_ACCPAYCREDIT);
$result = $apiInstance->createCreditNote($xeroTenantId,$creditnote); 
//[/CreditNotes:Create]
		
		$str = $str ."Create CreditNote: " . $result->getCreditNotes()[0]->getTotal() . "<br>" ;

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateCreditNote($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$new = $this->createCreditNote($xeroTenantId,$apiInstance,true);
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

		$new = $this->createCreditNote($xeroTenantId,$apiInstance,true);
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
$currency->setCode(XeroAPI\XeroPHP\Models\Accounting\CurrencyCode::CAD)
		 ->setDescription("Canadian Dollar");
		
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


	public function createEmployee($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Employees:Create]
$employee = new XeroAPI\XeroPHP\Models\Accounting\Employee;

$employee->setFirstName('Sid-' . $this->getRandNum())
	->setLastName("Maestre - " . $this->getRandNum());
$result = $apiInstance->createEmployee($xeroTenantId,$employee); 
//[/Employees:Create]
		
		$str = $str . "Create a new Employee: " . $result->getEmployees()[0]->getFirstName() ."<br>" ;

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

	public function createInvoice($xeroTenantId,$apiInstance,$returnObj=false)
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
	->setStatus(XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_AUTHORISED)
	->setType(XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCPAY)
	->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE);
$result = $apiInstance->createInvoice($xeroTenantId,$invoice); 
//[/Invoices:Create]
		
		$str = $str ."Create Invoice total amount: " . $result->getInvoices()[0]->getTotal() . "<br>" ;

		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}	

	public function updateInvoice($xeroTenantId,$apiInstance)
	{
		$str = '';
		$new = $this->createInvoice($xeroTenantId,$apiInstance,true);
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

		$new = $this->createInvoice($xeroTenantId,$apiInstance,true);
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

	public function createItem($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

//[Items:Create]
$item = new XeroAPI\XeroPHP\Models\Accounting\Item;

$item->setName('My Item-' . $this->getRandNum())
	->setCode($this->getRandNum())
	->setDescription("This is my Item description.")
	->setIsTrackedAsInventory(false);
$result = $apiInstance->createItem($xeroTenantId,$item); 
//[/Items:Create]
		
		$str = $str . "Create item: " . $result->getItems()[0]->getName() . "<br>" ;
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateItem($xeroTenantId,$apiInstance)
	{
		$str = '';
	
		$new = $this->createItem($xeroTenantId,$apiInstance,true);
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

$linkedtransactions = new XeroAPI\XeroPHP\Models\Accounting\LinkedTransactions;
$arr_linkedtransactions = [];
array_push($arr_linkedtransactions, $linkedtransaction);
$linkedtransactions->setLinkedTransactions($arr_linkedtransactions);

$result = $apiInstance->createLinkedTransaction($xeroTenantId,$linkedtransactions); 	
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

	public function createManualJournal($xeroTenantId,$apiInstance,$returnObj=false)
	{
		$str = '';

		$credit = $this->getJournalLineCredit();
		$debit = $this->getJournalLineDebit();

//[ManualJournals:Create]
$manualjournal = new XeroAPI\XeroPHP\Models\Accounting\ManualJournal;

$arr_journallines = [];
array_push($arr_journallines, $credit);
array_push($arr_journallines, $debit);

$manualjournal->setNarration('MJ from SDK -' . $this->getRandNum())
              ->setJournalLines($arr_journallines);

$manualjournals = new XeroAPI\XeroPHP\Models\Accounting\ManualJournals;
$arr_manualjournals = [];
array_push($arr_manualjournals, $manualjournal);
$manualjournals->setManualJournals($arr_manualjournals);

$result = $apiInstance->createManualJournal($xeroTenantId,$manualjournals); 
//[/ManualJournals:Create]
		
		$str = $str . "Create ManualJournal: " . $result->getManualJournals()[0]->getNarration() . "<br>" ;
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}

	public function updateManualJournal($xeroTenantId,$apiInstance)
	{
		$str = '';
		
		$new = $this->createManualJournal($xeroTenantId,$apiInstance,true);
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

$result = $apiInstance->createBankTransaction($xeroTenantId,$overpayment); 
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

	public function allocateOverpayment($xeroTenantId,$apiInstance)
	{
		$str = '';

		$invNew = $this->createInvoiceAccRec($xeroTenantId,$apiInstance,true);
		$invoiceId = $invNew->getInvoices()[0]->getInvoiceID();
		$overpaymentNew = $this->createOverpayment($xeroTenantId,$apiInstance,true);
		$overpaymentId = $overpaymentNew->getBankTransactions()[0]->getOverpaymentId();

//[Overpayments:Allocate]
$invoice = new XeroAPI\XeroPHP\Models\Accounting\Invoice;
$invoice->setInvoiceID($invoiceId);

$allocation = new XeroAPI\XeroPHP\Models\Accounting\Allocation;
$allocation->setInvoice($invoice)
	->setAmount("1.00")
	->setDate(new DateTime('2019-08-02'));
$arr_allocation = [];		
array_push($arr_allocation, $allocation);

$allocations = new XeroAPI\XeroPHP\Models\Accounting\Allocations;	
$allocations->setAllocations($arr_allocation);

$result = $apiInstance->createOverpaymentAllocation($xeroTenantId,$overpaymentId,$allocations);
//[/Overpayments:Allocate]
		
		//$str = $str . "Allocate Overpayment: " . $overpayment["OverpaymentID"] . "<br>" ;
	
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

$result = $apiInstance->createBankTransaction($xeroTenantId,$prepayment); 
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


	public function createPurchaseOrder($xeroTenantId,$apiInstance,$returnObj=false)
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

$purchaseorder = new XeroAPI\XeroPHP\Models\Accounting\PurchaseOrder;
$purchaseorder->setReference('Ref original -' . $this->getRandNum())
	->setContact($contact)
	->setLineItems($lineitems);

$result = $apiInstance->createPurchaseOrder($xeroTenantId,$purchaseorder);
//[/PurchaseOrders:Create]
		
		$str = $str . "Created PurchaseOrder Number: " . $result->getPurchaseOrders()[0]->getPurchaseOrderNumber() . "<br>" ;
		
		if($returnObj) {
			return $result;
		} else {
			return $str;
		}
	}


	public function updatePurchaseOrder($xeroTenantId,$apiInstance)
	{
		$str = '';
	
		$new = $this->createPurchaseOrder($xeroTenantId,$apiInstance,true);
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
	
		$new = $this->createPurchaseOrder($xeroTenantId,$apiInstance,true);
		$purchaseorderId = $new->getPurchaseOrders()[0]->getPurchaseOrderID();

//[PurchaseOrders:Delete]
$purchaseorder = new XeroAPI\XeroPHP\Models\Accounting\PurchaseOrder;
$purchaseorder->setStatus(XeroAPI\XeroPHP\Models\Accounting\PurchaseOrder::STATUS_DELETED);
$result = $apiInstance->updatePurchaseOrder($xeroTenantId,$purchaseorderId,$purchaseorder);
//[/PurchaseOrders:Delete]

		$str = $str . "Deleted PurchaseOrder: " . $result->getPurchaseOrders()[0]->getReference() . "<br>";
		
		return $str;
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

	public function createTaxRate($xeroTenantId,$apiInstance,$returnObj=false)
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

$result = $apiInstance->createTaxRate($xeroTenantId,$taxrate); 
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
	
		$newTaxRate = $this->createTaxRate($xeroTenantId,$apiInstance,true);
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

	// HELPERS
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
$result = $apiInstance->createInvoice($xeroTenantId,$invoice); 

		
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
$result = $apiInstance->createInvoice($xeroTenantId,$invoice); 
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
		$result = $apiInstance->createInvoice($xeroTenantId,$invoice); 
		
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
		$result = $apiInstance->createCreditNote($xeroTenantId,$creditnote); 

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
