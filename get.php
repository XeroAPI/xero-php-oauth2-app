<?php
    ini_set('display_errors', 'On');
	require __DIR__ . '/vendor/autoload.php';

	require_once('storage.php');
	require_once('example.php');

	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();
	$clientId = getenv('CLIENT_ID');
	$clientSecret = getenv('CLIENT_SECRET');
	$redirectUri = getenv('REDIRECT_URI');

	// Storage Classe uses sessions for storing token > extend to your DB of choice
	$storage = new StorageClass();

	// ALL methods are demonstrated using this class
	$ex = new ExampleClass();

	$xeroTenantId = (string)$storage->getSession()['tenant_id'];

	// Check if Access Token is expired
	// if so - refresh token
	if ($storage->getHasExpired()) {
		$provider = new \League\OAuth2\Client\Provider\GenericProvider([
			'clientId'                => $clientId,   
			'clientSecret'            => $clientSecret,
			'redirectUri'             => $redirectUri,
        	'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
        	'urlAccessToken'          => 'https://identity.xero.com/connect/token',
        	'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
		]);

	    $newAccessToken = $provider->getAccessToken('refresh_token', [
	        'refresh_token' => $storage->getRefreshToken()
	    ]);
	    // Save my token, expiration and refresh token
         // Save my token, expiration and refresh token
		 $storage->setToken(
            $newAccessToken->getToken(),
            $newAccessToken->getExpires(), 
            $xeroTenantId,
            $newAccessToken->getRefreshToken(),
            $newAccessToken->getValues()["id_token"] );
	}

	$config = XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$storage->getSession()['token'] );		  
	$accountingApi = new XeroAPI\XeroPHP\Api\AccountingApi(
	    new GuzzleHttp\Client(),
	    $config
	);

	$assetApi = new XeroAPI\XeroPHP\Api\AssetApi(
	    new GuzzleHttp\Client(),
	    $config
	);

	$identityApi = new XeroAPI\XeroPHP\Api\IdentityApi(
	    new GuzzleHttp\Client(),
	    $config
	);

	$projectApi = new XeroAPI\XeroPHP\Api\ProjectApi(
	    new GuzzleHttp\Client(),
	    $config
	);

	$payrollAuApi = new XeroAPI\XeroPHP\Api\PayrollAuApi(
	    new GuzzleHttp\Client(),
	    $config
	);

	$financeApi = new XeroAPI\XeroPHP\Api\FinanceApi(
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



				
				case "Connection":
				    switch($action)
					{
				    	case "Delete":
						echo $ex->deleteConnection($xeroTenantId,$identityApi);
						break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				break;

                case "Connections":
                    switch($action)
                    {
                        case "Read":
                            echo $ex->getConnections($identityApi);
                            break;
                        default:
                            echo $action . " action not supported in API";
                    }
                    break;

                case "Account":
				    switch($action)
					{
				    	case "Create":
						echo $ex->createAccount($xeroTenantId,$accountingApi);
						break;
				    	case "Read":
				        echo $ex->getAccount($xeroTenantId,$accountingApi);
				        break;
				        case "Update":	
				        echo $ex->updateAccount($xeroTenantId,$accountingApi);
				    	break;
				    	case "Delete":
				        echo $ex->deleteAccount($xeroTenantId,$accountingApi);
				    	break;
				    	case "Archive":
				        echo $ex->archiveAccount($xeroTenantId,$accountingApi);
				    	break;
				    	case "Attachment":
				        echo $ex->attachmentAccount($xeroTenantId,$accountingApi);
						break;
						case "AttachmentById":
						echo $ex->getAccountAttachmentById($xeroTenantId,$accountingApi);
						break;
				    	default:
					    echo $action . " action not supported in API";
				    }
			    break;

			    case "Accounts":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getAccounts($xeroTenantId,$accountingApi);
				        break;
				        default:
					    echo $action . " action not supported in API";
				    }
			    break;

				case "BankTransaction":
				    switch($action)
					{
				        case "Read":
				        echo $ex->getBankTransaction($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updateBankTransaction($xeroTenantId,$accountingApi);
				    	break;
				    	case "Delete":
				        echo $ex->deleteBankTransaction($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				break;

				case "BankTransactions":
				    switch($action)
					{
						case "Create":
						print_r($ex->createBankTransactions($xeroTenantId,$accountingApi));
						break;
				        case "Read":
				        echo $ex->getBankTransactions($xeroTenantId,$accountingApi);
				        break;
				        case "UpdateOrCreate":
				        echo $ex->updateOrCreateBankTransactions($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "BankTransfers":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createBankTransfer($xeroTenantId,$accountingApi);
				        break;
				        case "Read":
				        echo $ex->getBankTransfer($xeroTenantId,$accountingApi);
				        break;
				        default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "BrandingThemes":
				    switch($action)
					{
				        case "Read":
				        echo $ex->getBrandingTheme($xeroTenantId,$accountingApi);
				        break;
				        default:
					    echo $action . " action not supported in API";
				    }
				 break;
				 
				 case "Contact":
				    switch($action)
					{
						case "Read":
				        echo $ex->getContact($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updateContact($xeroTenantId,$accountingApi);
				    	break;
				    	case "Archive":
				        echo $ex->archiveContact($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Contacts":
				    switch($action)
					{
						case "Create":
						echo $ex->createContacts($xeroTenantId,$accountingApi);
						break;
				        case "Read":
				        echo $ex->getContact($xeroTenantId,$accountingApi);
				        break;
				        case "UpdateOrCreate":
				        echo $ex->updateOrCreateContacts($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "ContactGroups":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createContactGroup($xeroTenantId,$accountingApi);
				        break;
				        case "Read":
				        echo $ex->getContactGroup($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updateContactGroup($xeroTenantId,$accountingApi);
				    	break;
				    	case "Archive":
				        echo $ex->archiveContactGroup($xeroTenantId,$accountingApi);
				    	break;
				    	case "RemoveContact":
				        echo $ex->removeContactFromContactGroup($xeroTenantId,$accountingApi);
				    	break;
				    	case "AddContact":
				        echo $ex->createContactGroupContacts($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "CreditNotes":
				    switch($action)
					{
						case "Create":
						echo $ex->createCreditNotes($xeroTenantId,$accountingApi);
						break;
				        case "Read":
				        echo $ex->getCreditNote($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updateCreditNote($xeroTenantId,$accountingApi);
				    	break;
				    	case "Allocate":
				        echo $ex->allocateCreditNote($xeroTenantId,$accountingApi);
				    	break;
				    	case "Refund":
				        echo $ex->refundCreditNote($xeroTenantId,$accountingApi);
				    	break;
				    	case "Delete":
				        echo $ex->deleteCreditNote($xeroTenantId,$accountingApi);
				    	break;
				    	case "Void":
				        echo $ex->voidCreditNote($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Currencies":
				    switch($action)
					{
				        case "Create":
				        echo $ex->createCurrency($xeroTenantId,$accountingApi);
				        break;
				        case "Read":
				        echo $ex->getCurrency($xeroTenantId,$accountingApi);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Employees":
				    switch($action)
					{
						case "Create":
						echo $ex->createEmployees($xeroTenantId,$accountingApi);
						break;
				        case "Read":
				        echo $ex->getEmployee($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updateEmployee($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "ExpenseClaims":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createExpenseClaim($xeroTenantId,$accountingApi);
				        break;
				        case "Read":
				        echo $ex->getExpenseClaim($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updateExpenseClaim($xeroTenantId,$accountingApi);
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
						echo $ex->createInvoices($xeroTenantId,$accountingApi);
						break;
						case "UpdateOrCreate":
						echo $ex->updateOrCreateInvoices($xeroTenantId,$accountingApi);
						break;
				        case "Read":
				        echo $ex->getInvoice($xeroTenantId,$accountingApi);
						break;
						case "ReadPdf":
						echo $ex->getInvoiceAsPdf($xeroTenantId,$accountingApi);
						break;
				        case "Update":
				        echo $ex->updateInvoice($xeroTenantId,$accountingApi);
				    	break;
				    	case "Delete":
				        echo $ex->deleteInvoice($xeroTenantId,$accountingApi);
				    	break;
				    	case "Void":
				        echo $ex->voidInvoice($xeroTenantId,$accountingApi);
						break;
						default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "InvoiceReminders":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getInvoiceReminder($xeroTenantId,$accountingApi);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Items":
				    switch($action)
					{
				    	case "Create":
						echo $ex->createItems($xeroTenantId,$accountingApi);
						break;
				        case "Read":
				        echo $ex->getItem($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updateItem($xeroTenantId,$accountingApi);
				    	break;
				    	case "Delete":
				        echo $ex->deleteItem($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Journals":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getJournal($xeroTenantId,$accountingApi);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "LinkedTransactions":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createLinkedTransaction($xeroTenantId,$accountingApi);
						break;
				        case "Read":
				        echo $ex->getLinkedTransaction($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updateLinkedTransaction($xeroTenantId,$accountingApi);
				    	break;
				    	case "Delete":
				        echo $ex->deleteLinkedTransaction($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "ManualJournals":
				    switch($action)
					{
				    	case "Create":
						echo $ex->createManualJournals($xeroTenantId,$accountingApi);
						break;
				        case "Read":
				        echo $ex->getManualJournal($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updateManualJournal($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Organisations":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getOrganisation($xeroTenantId,$accountingApi);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Overpayments":
				    switch($action)
					{
				        case "Read":
				        echo $ex->getOverpayment($xeroTenantId,$accountingApi);
				        break;
				        case "Create":
				        echo $ex->createOverpayment($xeroTenantId,$accountingApi);
				        break;
				        case "Allocate":
						echo $ex->allocateOverpayments($xeroTenantId,$accountingApi);
						break;
				    	case "Refund":
				        echo $ex->refundOverpayment($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Payments":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createPayment($xeroTenantId,$accountingApi);
						break;
						case "CreateMulti":
						echo $ex->createPayments($xeroTenantId,$accountingApi);
						break;
				        case "Read":
				        echo $ex->getPayment($xeroTenantId,$accountingApi);
				        break;
				        case "Delete":
				        echo $ex->deletePayment($xeroTenantId,$accountingApi);
				        break;
				        default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Prepayments":
				    switch($action)
					{
				        case "Read":
				        echo $ex->getPrepayment($xeroTenantId,$accountingApi);
				        break;
				        case "Create":
				        echo $ex->createPrepayment($xeroTenantId,$accountingApi);
				        break;
				        case "Allocate":
				        echo $ex->allocatePrepayment($xeroTenantId,$accountingApi);
				    	break;
				    	case "Refund":
				        echo $ex->refundPrepayment($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "PurchaseOrders":
				    switch($action)
					{
				    	case "Create":
						echo $ex->createPurchaseOrders($xeroTenantId,$accountingApi);
						break;
				        case "Read":
				        echo $ex->getPurchaseOrder($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updatePurchaseOrder($xeroTenantId,$accountingApi);
				    	break;
				    	case "Delete":
				        echo $ex->deletePurchaseOrder($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Quote":
				    switch($action)
					{
				        case "Read":
				        echo $ex->getQuote($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updateQuote($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Quotes":
				    switch($action)
					{
				    	case "Create":
						echo $ex->createQuotes($xeroTenantId,$accountingApi);
						break;
				        case "Read":
				        echo $ex->getQuotes($xeroTenantId,$accountingApi);
				        break;
				        case "UpdateOrCreate":
				        echo $ex->updateOrCreateQuotes($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Receipts":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createReceipt($xeroTenantId,$accountingApi);
				        break;
				        case "Read":
				        echo $ex->getReceipt($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updateReceipt($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "RepeatingInvoices":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getRepeatingInvoice($xeroTenantId,$accountingApi);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Reports":
				    switch($action)
					{
				    	case "TenNinetyNine":
				        echo $ex->getTenNinetyNine($xeroTenantId,$accountingApi);
				        break;
				        case "AgedPayablesByContact":
				        echo $ex->getAgedPayablesByContact($xeroTenantId,$accountingApi);
				        break;
				        case "AgedReceivablesByContact":
				        echo $ex->getAgedReceivablesByContact($xeroTenantId,$accountingApi);
				        break;
				        case "BalanceSheet":
				        echo $ex->getBalanceSheet($xeroTenantId,$accountingApi);
				        break;
				        case "BankStatement":
				        echo $ex->getBankStatement($xeroTenantId,$accountingApi);
				        break;
				        case "BankSummary":
				        echo $ex->getBankSummary($xeroTenantId,$accountingApi);
				        break;
				        case "BudgetSummary":
				        echo $ex->getBudgetSummary($xeroTenantId,$accountingApi);
				        break;
				        case "ExecutiveSummary":
				        echo $ex->getExecutiveSummary($xeroTenantId,$accountingApi);
				        break;
				        case "ProfitAndLoss":
				        echo $ex->getProfitAndLoss($xeroTenantId,$accountingApi);
				        break;
				        case "TrialBalance":
				        echo $ex->getTrialBalance($xeroTenantId,$accountingApi);
				        break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "TaxRates":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createTaxRates($xeroTenantId,$accountingApi);
				        break;
				        case "Read":
				        echo $ex->getTaxRate($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updateTaxRate($xeroTenantId,$accountingApi);
				    	break;
				    	case "Delete":
				        echo $ex->deleteTaxRate($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "TrackingCategories":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createTrackingCategory($xeroTenantId,$accountingApi);
				        break;
				        case "Read":
				        echo $ex->getTrackingCategory($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updateTrackingCategory($xeroTenantId,$accountingApi);
				    	break;
				    	case "Delete":
				        echo $ex->deleteTrackingCategory($xeroTenantId,$accountingApi);
				    	break;
				    	case "Archive":
				        echo $ex->archiveTrackingCategory($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "TrackingOptions":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createTrackingOptions($xeroTenantId,$accountingApi);
				        break;
				        case "Read":
				        echo $ex->getTrackingOption($xeroTenantId,$accountingApi);
				        break;
				        case "Update":
				        echo $ex->updateTrackingOptions($xeroTenantId,$accountingApi);
				    	break;
				    	case "Delete":
				        echo $ex->deleteTrackingOptions($xeroTenantId,$accountingApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Users":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getUser($xeroTenantId,$accountingApi);
				        break;
				        default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Asset":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createAsset($xeroTenantId,$assetApi);
				        break;
				        case "Read":
				        echo $ex->getAsset($xeroTenantId,$assetApi);
				        break;
				        case "Update":
				        echo $ex->updateAsset($xeroTenantId,$assetApi);
				    	break;
				    	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Assets":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getAssets($xeroTenantId,$assetApi);
				        break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "AssetType":
				    switch($action)
					{
				    	case "Create":
				        echo $ex->createAssetType($xeroTenantId,$assetApi, $accountingApi);
				        break; 
				        default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "AssetTypes":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getAssetTypes($xeroTenantId,$assetApi);
				        break;
				        default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "AssetSettings":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getAssetSettings($xeroTenantId,$assetApi);
				        break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Project":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getProject($xeroTenantId,$projectApi);
						break;
						case "Create":
						echo $ex->createProject($xeroTenantId,$projectApi,$accountingApi);
						break;
						case "Update":
						echo $ex->updateProject($xeroTenantId,$projectApi,$accountingApi);
						break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "Projects":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getProjects($xeroTenantId,$projectApi);
				        break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "PayrollAuEmployee":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getPayrollAuEmployees($xeroTenantId,$payrollAuApi);
						break;
						case "Create":
						echo $ex->createPayrollAuEmployees($xeroTenantId,$payrollAuApi);
						break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "PayrollAuLeaveApplication":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getPayrollAuLeaveApplication($xeroTenantId,$payrollAuApi);
						break;
						case "Create":
						echo $ex->createPayrollAuLeaveApplications($xeroTenantId,$payrollAuApi);
						break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "AccountingActivityAccountUsage":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getAccountingActivityAccountUsage($xeroTenantId,$financeApi);
						break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "AccountingActivityLockHistory":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getAccountingActivityLockHistory($xeroTenantId,$financeApi);
						break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "AccountingActivityReportHistory":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getAccountingActivityReportHistory($xeroTenantId,$financeApi);
						break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;


				 case "AccountingActivityUserActivities":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getAccountingActivityUserActivities($xeroTenantId,$financeApi);
						break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "CashValidation":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getCashValidation($xeroTenantId,$financeApi);
						break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "FinancialStatementBalanceSheet":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getFinancialStatementBalanceSheet($xeroTenantId,$financeApi);
				        break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "FinancialStatementCashflow":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getFinancialStatementCashflow($xeroTenantId,$financeApi);
				        break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "FinancialStatementProfitAndLoss":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getFinancialStatementProfitAndLoss($xeroTenantId,$financeApi);
				        break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "FinancialStatementTrialBalance":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getFinancialStatementTrialBalance($xeroTenantId,$financeApi);
				        break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "FinancialStatementContactsRevenue":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getFinancialStatementContactsRevenue($xeroTenantId,$financeApi);
				        break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "FinancialStatementContactsExpense":
				    switch($action)
					{
				    	case "Read":
				        echo $ex->getFinancialStatementContactsExpense($xeroTenantId,$financeApi);
				        break;
				       	default:
					    echo $action . " action not supported in API";
				    }
				 break;

				 case "BankStatementAccounting":
					switch($action)
				{
						case "Read":
							echo $ex->getBankStatementAccounting($xeroTenantId,$financeApi,$accountingApi);
							break;
							 default:
						echo $action . " action not supported in API";
					}
			 break;

			}

			} catch (Exception $e) {
				var_dump($e);
                echo 'Exception when calling Xero API: ', $e->getMessage(), PHP_EOL;
            }
     
		?>
	</div>
	</body>
</html>