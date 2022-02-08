
Handlebars.getTemplate = function(name) {
    if (Handlebars.templates === undefined || Handlebars.templates[name] === undefined) {
        $.ajax({
            url : 'xero-sdk-ui/templates/' + name + '.handlebars',
            success : function(data) {
                if (Handlebars.templates === undefined) {
                    Handlebars.templates = {};
                }
                Handlebars.templates[name] = Handlebars.compile(data);
            },
            async : false
        });
    }
    return Handlebars.templates[name];
};

var endpoint = [
    {name: "---IDENTITY---",action:[{name: ""}]},
    {name: "Connection",action:[{name: "Delete"}]},
    {name: "Connections",action:[{name: "Read"}]},
    {name: "---ACCOUNTING---",action:[{name: ""}]},
    {name: "Account",action:[{name: "Create"},{name: "Read"},{name: "Update"},{name: "Delete"},{name: "Archive"},{name: "Attachment"},{name: "AttachmentById"} ]},
    {name: "Accounts",action:[{name: "Read"}]},
    {name: "BatchPayments",action:[{name: "Read"},{name: "Create"}]},
    {name: "BankTransaction",action:[{name: "Read"},{name: "Update"},{name: "Delete"}]},
    {name: "BankTransactions",action:[{name: "Create"},{name: "Read"},{name: "UpdateOrCreate"}]},
    {name: "BankTransfers",action:[{name: "Create"},{name: "Read"}]},
    {name: "BrandingThemes",action:[{name: "Read"}]},
    {name: "Contact",action:[{name: "Read"},{name: "Update"},{name: "Archive"}]},
    {name: "Contacts",action:[{name: "Create"},{name: "Read"},{name: "UpdateOrCreate"}]},
    {name: "ContactGroups",action:[{name: "Create"},{name: "Read"},{name: "Update"},{name: "Archive"},{name:"AddContact"},{name:"RemoveContact"}]},
    {name: "CreditNotes",action:[{name: "Create"},{name: "CreateMulti"},{name: "Read"},{name: "Update"},{name: "Delete"},{name: "Allocate"},{name: "Refund"},{name: "Void"}]},
    {name: "Currencies",action:[{name: "Create"},{name: "Read"}]},
    {name: "Employees",action:[{name: "Create"},{name: "CreateMulti"},{name: "Read"},{name: "Update"}]},
    {name: "ExpenseClaims",action:[{name: "Create"},{name: "Read"},{name: "Update"}]},
    {name: "Invoices",action:[{name: "Create"},{name: "UpdateOrCreate"},{name: "ReadPdf"},{name: "Read"},{name: "Update"},{name: "Delete"},{name: "Void"}]},
    {name: "InvoiceReminders",action:[{name: "Read"}]},
    {name: "Items",action:[{name: "Create"},{name: "CreateMulti"},{name: "Read"},{name: "Update"},{name: "Delete"}]},
    {name: "Journals",action:[{name: "Read"}]},
    {name: "LinkedTransactions",action:[{name: "Create"},{name: "Read"},{name: "Update"},{name: "Delete"}]},
    {name: "ManualJournals",action:[{name: "Create"},{name: "CreateMulti"},{name: "Read"},{name: "Update"}]},
    {name: "Organisations",action:[{name: "Read"}]},
    {name: "Overpayments",action:[{name: "Create"},{name: "Read"},{name: "Allocate"},{name: "AllocateMulti"},{name: "Refund"}]},
    {name: "Payments",action:[{name: "Create"},{name: "CreateMulti"},{name: "Read"},{name: "Delete"}]},
    {name: "Prepayments",action:[{name: "Create"},{name: "Read"},{name: "Allocate"},{name: "Refund"}]},
    {name: "PurchaseOrders",action:[{name: "Create"},{name: "Read"},{name: "Update"},{name: "Delete"}]},
    {name: "Quote",action:[{name: "Update"},{name: "Read"}]},
    {name: "Quotes",action:[{name: "Create"},{name: "UpdateOrCreate"},{name: "Read"}]},
    {name: "Receipts",action:[{name: "Create"},{name: "Read"},{name: "Update"}]},
    {name: "RepeatingInvoices",action:[{name: "Read"}]},
    {name: "Reports",action:[{name: "TenNinetyNine"},{name: "AgedPayablesByContact"},{name: "AgedReceivablesByContact"},{name: "BalanceSheet"},{name: "BankSummary"},{name: "BudgetSummary"},{name: "ExecutiveSummary"},{name: "ProfitAndLoss"},{name: "TrialBalance"}]},
    {name: "TaxRates",action:[{name: "Create"},{name: "Read"},{name: "Update"}]},
    {name: "TrackingCategories",action:[{name: "Create"},{name: "Read"},{name: "Update"},{name: "Delete"},{name: "Archive"}]},
    {name: "TrackingOptions",action:[{name: "Create"},{name: "Update"},{name: "Delete"}]},
    {name: "Users",action:[{name: "Read"}]},
    {name: "---FIXED ASSETS---",action:[{name: ""}]},
    {name: "Asset",action:[{name: "Read"},{name: "Create"},{name: "Update"}]},
    {name: "Assets",action:[{name: "Read"}]},
    {name: "AssetType",action:[{name: "Create"}]},
    {name: "AssetTypes",action:[{name: "Read"}]},
    {name: "AssetSettings",action:[{name: "Read"}]},
    {name: "---PROJECTS---",action:[{name: ""}]},
    {name: "Project",action:[{name: "Create"},{name: "Read"},{name: "Update"}]},
    {name: "Projects",action:[{name: "Read"}]},
    {name: "---PAYROLL AU---",action:[{name: ""}]},
    {name: "PayrollAuEmployee",action:[{name: "Create"},{name: "Read"},{name: "Update"}]},
    {name: "PayrollAuLeaveApplication",action:[{name: "Create"}]},
    {name: "---FINANCE---",action:[{name: ""}]},
    {name: "AccountingActivityAccountUsage",action:[{name: "Read"}]},
    {name: "AccountingActivityLockHistory",action:[{name: "Read"}]},
    {name: "AccountingActivityReportHistory",action:[{name: "Read"}]},
    {name: "AccountingActivityUserActivities",action:[{name: "Read"}]},
    {name: "CashValidation",action:[{name: "Read"}]},
    {name: "FinancialStatementBalanceSheet",action:[{name: "Read"}]},
    {name: "FinancialStatementCashflow",action:[{name: "Read"}]},
    {name: "FinancialStatementProfitAndLoss",action:[{name: "Read"}]},
    {name: "FinancialStatementTrialBalance",action:[{name: "Read"}]},
    {name: "FinancialStatementContactsRevenue",action:[{name: "Read"}]},
    {name: "FinancialStatementContactsExpense",action:[{name: "Read"}]}
];
    
function populateAction(currEndpoint,currAction) {
    for (var i = 0; i < endpoint.length; i++){
      if (endpoint[i].name == currEndpoint){
        temp = endpoint[i].action;
      }
    }
    $("#action").children().remove();

    for (var i = 0; i < temp.length; i++){
        if (temp[i].name == currAction)
        {
            var selected = 'selected="true"';
        } else {
            var selected = '';
        }

        $("#action").append('<option ' + selected + ' value="' + temp[i].name + '">' + temp[i].name + '</option>');
    }
}

function setArray(arr,match) {
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        if (arr[i].name === match) {
            arr[i].selected = "selected";
        }
    }
}

function loadGet(appName,logoutUrl,refreshUrl,currEndpoint,currAction) 
{

    setArray(endpoint,currEndpoint);     
   
    var template = Handlebars.getTemplate('container');
    var data = {name: appName,logoutUrl: logoutUrl, refreshUrl: refreshUrl};
    var html = template(data);
    document.querySelector("#req").innerHTML = html;    
    
    var template = Handlebars.getTemplate('options');
    var html = template(endpoint);
    document.querySelector("#endpoint").innerHTML = html;
 
    var action = [{name: "Create"},{name: "Read"},{name: "Update"},{name: "Delete"}];
   
    var html = template(action);
    document.querySelector("#action").innerHTML = html;

    populateAction(currEndpoint,currAction);

    $("#endpoint").on("change",function(){
        if(currAction != $("#action").val()) {
            currAction = $("#action").val();   
        }
        populateAction($("#endpoint").val(),currAction);
    });
}

function loadIndex(appName,requestTokenUrl) 
{
    var template = Handlebars.getTemplate('index');
    var data = {name: appName,requestTokenUrl: requestTokenUrl};
    var html = template(data);
    document.querySelector("#req").innerHTML = html;    
}
