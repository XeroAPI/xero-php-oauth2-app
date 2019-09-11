<?php
    ini_set('display_errors', 'On');
	require __DIR__ . '/vendor/autoload.php';

	require_once('storage.php');
	require_once('example.php');

	// Storage Classe uses sessions for storing token > extend to your DB of choice
	$storage = new StorageClass();

	// ALL methods are demonstrated using this class
	$ex = new ExampleClass();

	$xeroTenantId = (string)$storage->getSession()['tenant_id'];

	// Check if Access Token is expired
	// if so - refresh token
	if ($storage->getHasExpired()) {
		$provider = new \League\OAuth2\Client\Provider\GenericProvider([
          'clientId'                => '__YOUR_CLIENT_ID__',   
          'clientSecret'            => '__YOUR_CLIENT_SECRET__',
          'redirectUri'             => 'http://localhost:8888/xero-php-oauth2-app/callback.php',
          'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
          'urlAccessToken'          => 'https://identity.xero.com/connect/token',
          'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
		]);

	    $newAccessToken = $provider->getAccessToken('refresh_token', [
	        'refresh_token' => $storage->getRefreshToken()
	    ]);
	    // Save my token, expiration and refresh token
        $storage->setToken(
            $newAccessToken->getToken(),
            $newAccessToken->getExpires(), 
            $xeroTenantId,
            $newAccessToken->getRefreshToken());
	}

	$config = XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$storage->getSession()['token'] );
	
	$config->setHost("https://api.xero.com/api.xro/2.0");        
	
	$apiInstance = new XeroAPI\XeroPHP\Api\AccountingApi(
	    new GuzzleHttp\Client(),
	    $config
	);

	if (isset($_POST["endpoint"]) ) {
		$endpoint = htmlspecialchars($_POST["endpoint"]);
	} else {
		$endpoint = "Accounts";
	}

	if (isset($_POST["action"]) ) {
		$action = htmlspecialchars($_POST["action"]);
	} else {
		$action = "none";
	}

	// Parse the example.php file to find matching endpoint/method combination
	// and display the code that was just executed on the screen.
	$file = file_get_contents('./example.php', true);

	$parsed = get_string_between($file, '//[' . $endpoint . ':' . $action . ']', '//[/' . $endpoint . ':' . $action . ']');
	$parsed = str_replace(["\r\n", "\r", "\n"], "<br/>", $parsed);

	function get_string_between($string, $start, $end){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return '';
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}
?>
<html>
<head>
	<title>xero-php-oauth2-app</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="http://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.11/handlebars.min.js"  crossorigin="anonymous"></script>
	<script src="xero-sdk-ui/xero.js"  crossorigin="anonymous"></script>
	<script type="text/javascript">
	   	document.addEventListener("DOMContentLoaded", function() {
			loadGet("xero-php sample app","disconnect.php","get.php","<?php echo($endpoint) ?>", "<?php echo($action) ?>");
		});
   	</script>
</head>
<body>

	<div id="req" class="container"></div>
	<div id="res" class="container">	
		<h3><?php echo($endpoint);?></h3>
		<hr>

		<strong>Code</strong><br>
		<pre><?php echo($parsed);?></pre>
		<hr>
		<strong>Result</strong><br>

		<?php
			try {
			switch($endpoint)
			{
			    case "Accounts":
				    switch($action)
					{
				    	case "Create":
						echo $ex->createAccount($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getAccount($xeroTenantId,$apiInstance);
				        break;
				        case "Update":	
				        echo $ex->updateAccount($xeroTenantId,$apiInstance);
				    	break;
				    	case "Delete":
				        echo $ex->deleteAccount($xeroTenantId,$apiInstance);
				    	break;
				    	case "Archive":
				        echo $ex->archiveAccount($xeroTenantId,$apiInstance);
				    	break;
				    	case "Attachment":
				        echo $ex->attachmentAccount($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
			    break;

			    case "BankTransactions":
				    switch($action)
					{
				    	case "Create":
				        print_r($ex->createBankTransaction($xeroTenantId,$apiInstance));
				        break;
				        case "Read":
				        echo $ex->getBankTransaction($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updateBankTransaction($xeroTenantId,$apiInstance);
				    	break;
				    	case "Delete":
				        echo $ex->deleteBankTransaction($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "BankTransfers":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createBankTransfer($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getBankTransfer($xeroTenantId,$apiInstance);
				        break;
				        default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "BrandingThemes":
				    switch($action)
					{
				        case "Read":
				        echo $ex->getBrandingTheme($xeroTenantId,$apiInstance);
				        break;
				        default:
					    echo $action . " action not supported in API";
				    }
				 break;
				 
				 case "Contacts":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createContact($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getContact($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updateContact($xeroTenantId,$apiInstance);
				    	break;
				    	case "Archive":
				        echo $ex->archiveContact($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "ContactGroups":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createContactGroup($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getContactGroup($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updateContactGroup($xeroTenantId,$apiInstance);
				    	break;
				    	case "Archive":
				        echo $ex->archiveContactGroup($xeroTenantId,$apiInstance);
				    	break;
				    	case "RemoveContact":
				        echo $ex->removeContactFromContactGroup($xeroTenantId,$apiInstance);
				    	break;
				    	case "AddContact":
				        echo $ex->createContactGroupContacts($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "CreditNotes":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createCreditNote($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getCreditNote($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updateCreditNote($xeroTenantId,$apiInstance);
				    	break;
				    	case "Allocate":
				        echo $ex->allocateCreditNote($xeroTenantId,$apiInstance);
				    	break;
				    	case "Refund":
				        echo $ex->refundCreditNote($xeroTenantId,$apiInstance);
				    	break;
				    	case "Delete":
				        echo $ex->deleteCreditNote($xeroTenantId,$apiInstance);
				    	break;
				    	case "Void":
				        echo $ex->voidCreditNote($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Currencies":
				    switch($action)
					{
				        case "Create":
				        echo $ex->createCurrency($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getCurrency($xeroTenantId,$apiInstance);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Employees":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createEmployee($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getEmployee($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updateEmployee($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "ExpenseClaims":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createExpenseClaim($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getExpenseClaim($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updateExpenseClaim($xeroTenantId,$apiInstance);
				        //echo $action . " action is supported in API but not SDK (no setStatus)";
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Invoices":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createInvoice($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getInvoice($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updateInvoice($xeroTenantId,$apiInstance);
				    	break;
				    	case "Delete":
				        echo $ex->deleteInvoice($xeroTenantId,$apiInstance);
				    	break;
				    	case "Void":
				        echo $ex->voidInvoice($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "InvoiceReminders":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getInvoiceReminder($xeroTenantId,$apiInstance);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Items":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createItem($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getItem($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updateItem($xeroTenantId,$apiInstance);
				    	break;
				    	case "Delete":
				        echo $ex->deleteItem($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Journals":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getJournal($xeroTenantId,$apiInstance);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "LinkedTransactions":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createLinkedTransaction($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getLinkedTransaction($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updateLinkedTransaction($xeroTenantId,$apiInstance);
				    	break;
				    	case "Delete":
				        echo $ex->deleteLinkedTransaction($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "ManualJournals":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createManualJournal($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getManualJournal($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updateManualJournal($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Organisations":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getOrganisation($xeroTenantId,$apiInstance);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Overpayments":
				    switch($action)
					{
				        case "Read":
				        echo $ex->getOverpayment($xeroTenantId,$apiInstance);
				        break;
				        case "Create":
				        echo $ex->createOverpayment($xeroTenantId,$apiInstance);
				        break;
				        case "Allocate":
				        echo $ex->allocateOverpayment($xeroTenantId,$apiInstance);
				    	break;
				    	case "Refund":
				        echo $ex->refundOverpayment($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Payments":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createPayment($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getPayment($xeroTenantId,$apiInstance);
				        break;
				        case "Delete":
				        echo $ex->deletePayment($xeroTenantId,$apiInstance);
				        break;
				        default:
					    echo $action . " action not supported in API";
				    }
				 break;


				 case "Prepayments":
				    switch($action)
					{
				        case "Read":
				        echo $ex->getPrepayment($xeroTenantId,$apiInstance);
				        break;
				        case "Create":
				        echo $ex->createPrepayment($xeroTenantId,$apiInstance);
				        break;
				        case "Allocate":
				        echo $ex->allocatePrepayment($xeroTenantId,$apiInstance);
				    	break;
				    	case "Refund":
				        echo $ex->refundPrepayment($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "PurchaseOrders":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createPurchaseOrder($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getPurchaseOrder($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updatePurchaseOrder($xeroTenantId,$apiInstance);
				    	break;
				    	case "Delete":
				        echo $ex->deletePurchaseOrder($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Receipts":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createReceipt($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getReceipt($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updateReceipt($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "RepeatingInvoices":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getRepeatingInvoice($xeroTenantId,$apiInstance);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Reports":
				    switch($action)
					{
				    	case "TenNinetyNine":
				        echo $ex->getTenNinetyNine($xeroTenantId,$apiInstance);
				        break;
				        case "AgedPayablesByContact":
				        echo $ex->getAgedPayablesByContact($xeroTenantId,$apiInstance);
				        break;
				        case "AgedReceivablesByContact":
				        echo $ex->getAgedReceivablesByContact($xeroTenantId,$apiInstance);
				        break;
				        case "BalanceSheet":
				        echo $ex->getBalanceSheet($xeroTenantId,$apiInstance);
				        break;
				        case "BankStatement":
				        echo $ex->getBankStatement($xeroTenantId,$apiInstance);
				        break;
				        case "BankSummary":
				        echo $ex->getBankSummary($xeroTenantId,$apiInstance);
				        break;
				        case "BudgetSummary":
				        echo $ex->getBudgetSummary($xeroTenantId,$apiInstance);
				        break;
				        case "ExecutiveSummary":
				        echo $ex->getExecutiveSummary($xeroTenantId,$apiInstance);
				        break;
				        case "ProfitAndLoss":
				        echo $ex->getProfitAndLoss($xeroTenantId,$apiInstance);
				        break;
				        case "TrialBalance":
				        echo $ex->getTrialBalance($xeroTenantId,$apiInstance);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "TaxRates":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createTaxRate($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getTaxRate($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updateTaxRate($xeroTenantId,$apiInstance);
				    	break;
				    	case "Delete":
				        echo $ex->deleteTaxRate($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "TrackingCategories":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createTrackingCategory($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getTrackingCategory($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updateTrackingCategory($xeroTenantId,$apiInstance);
				    	break;
				    	case "Delete":
				        echo $ex->deleteTrackingCategory($xeroTenantId,$apiInstance);
				    	break;
				    	case "Archive":
				        echo $ex->archiveTrackingCategory($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "TrackingOptions":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createTrackingOptions($xeroTenantId,$apiInstance);
				        break;
				        case "Read":
				        echo $ex->getTrackingOption($xeroTenantId,$apiInstance);
				        break;
				        case "Update":
				        echo $ex->updateTrackingOptions($xeroTenantId,$apiInstance);
				    	break;
				    	case "Delete":
				        echo $ex->deleteTrackingOptions($xeroTenantId,$apiInstance);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Users":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getUser($xeroTenantId,$apiInstance);
				        break;
				        default:
					    echo $action . " action not supported in API";
				    }
				 break;
			}

			} catch (Exception $e) {
                echo 'Exception when calling AccountingApi: ', $e->getMessage(), PHP_EOL;
            }
     
		?>
	</div>
	</body>
</html>