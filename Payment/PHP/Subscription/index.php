<?php
    session_start();
    //get the session variables
    $accessToken = $_SESSION["payment_access_token"];
    $SubRequest = $_REQUEST[newSubscription];
    $SubDetailsRequest = $_REQUEST[getSubscriptionDetails];
    $TrxCommitRequest = $_REQUEST[commitTransaction];
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <title>Creating new Payment Transaction</title>
</head>

<body>
<img src="http://developer.att.com/developer/images/att.gif" />

<?php
//check whether the access token is available,else fetch it from the given url.
    if($accessToken == null || $accessToken == '') {
        print '<br/><a href="oauth.php">Fetch Access Token</a><br/>';
    }
?>
<form name="newTransaction" method="post">
    Access Token <input type="text" name="access_token" value="<?php echo $accessToken ?>" size=40/><br/>
    Amount <input type="text" name="amount" value="0.05" /><br />
    Auto Commit <input type="text" name="autoCommit" value="false" /><br />
    Category <input type="text" name="category" value="1" /><br />
    Channel <input type="text" name="channel" value="MOBILE_WEB" /><br />
    Currency <input type="text" name="currency" value="USD" /><br />
    Description <input type="text" name="description" value="ProductByMe" /><br />
    Transaction ID <input type="text" name="extTrxID" value="Transaction151" /><br />
    App ID <input type="text" name="appID" value="" /><br />
    Cancel Redirect Url <input type="text" name="cancelUrl" value="http://localhost:8080/Transaction/index.jsp?action=UserCancelled" size=60/><br />
    Fulfillment Url <input type="text" name="fulfillUrl" value="http://localhost:8080/Transaction/index.jsp?action=UserConfirmed" size=60/><br />
    Product ID <input type="text" name="productID" value="Product252" /><br />
    PurhcaseOnNoActiveSubscription <input type="text" name="purchaseNoSub" value="false" /><br />
    Status Url <input type="text" name="statusUrl" value="http://localhost:8080/Transaction/index.jsp?action=Status" size=60/><br />
    Merchant Subscription ID List <input type="text" name="merchantSubscriptionIdList" value="MySubscription3" /><br />
    Subscription Recurring Number <input type="text" name="subscriptionRecurringNumber" value="3" /><br />
    Subscription Recurring Period <input type="text" name="subscriptionRecurringPeriod" value="MONTHLY" /><br />
    Subscription Recurring Period Amount <input type="text" name="subscriptionRecurringPeriodAmount" value="1" /><br />
    <input type="submit" name="newSubscription" value="Click to make new subscription" />
</form>

<?php
//if the user submitted the newSubscription  button,
//then get the values and try to send the payment transaction. 
	if ($SubRequest != null) {
	
	$amount = $_POST['amount'];
	$autoCommit = $_POST['autoCommit'];
	$category = $_POST['category'];
	$channel = $_POST['channel'];
	$currency = $_POST['currency'];
	$description = $_POST['description'];
	$extTrxID = $_POST['extTrxID'];
	$appID = $_POST['appID'];
	$cancelUrl = $_POST['cancelUrl'];
	$fulfillUrl = $_POST['fulfillUrl'];
	$productID = $_POST['productID'];
	$purchaseNoSub = $_POST['purchaseNoSub'];
	$statusUrl = $_POST['statusUrl'];
	$merchantSubscriptionIdList = $_POST['merchantSubscriptionIdList'];
	$subscriptionRecurringNumber = $_POST['subscriptionRecurringNumber'];
	$subscriptionRecurringPeriod = $_POST['subscriptionRecurringPeriod'];
	$subscriptionRecurringPeriodAmount = $_POST['subscriptionRecurringPeriodAmount'];

	//post data
	$sub_RequestBody = '{"amount":$amount,"category":$category,"channel":'.$channel;
	$sub_RequestBody .= ',"currency":'.$currency.',"description":'.$description;
	$sub_RequestBody .= ',"externalMerchantTransactionID":'.$extTrxID.',"merchantApplicationID":'.$appID;
	$sub_RequestBody .= ',"merchantCancelRedirectUrl":'.$cancelUrl;
	$sub_RequestBody .= ',"merchantFulfillmentRedirectUrl"'.$fulfillUrl.',"merchantProductID":'.$productID;
	$sub_RequestBody .= ',"purchaseOnNoActiveSubscription":'.$purchaseNoSub;
	$sub_RequestBody .= ',"transactionStatusCallbackUrl":'.$statusUrl;
	$sub_RequestBody .= ',"merchantSubscriptionIdList":'.$merchantSubscriptionIdList;
	$sub_RequestBody .= ',"subscriptionRecurringNumber":'.$subscriptionRecurringNumber;
	$sub_RequestBody .= ',"subscriptionRecurringPeriod":'.$subscriptionRecurringPeriod;
	$sub_RequestBody .= ',"subscriptionRecurringPeriodAmount":'.$subscriptionRecurringPeriodAmount;
	$sub_RequestBody .= ',"autoCommit":'.$autoCommit.'"}';
       
	//print "<p>sendSMS_RequestBody : $sendSMS_RequestBody </p>";
	//url for new subscription
        $trx_Url = "httpS://beta-api.att.com/1/payments/transactions?access_token=".$accessToken;
	//http header values
	$trx_headers = array(
	'Content-Type: application/json'
	);
	//print "<p>URL to POST SendSMS:</br>$sendSMS_Url</p>";
	//print "<p>RequestBody to POST SendSMS:</br>$sendSMS_RequestBody</p>";
	$paymentSub = curl_init();
	curl_setopt($paymentSub, CURLOPT_URL, $trx_Url);
	curl_setopt($paymentSub, CURLOPT_POST, 1);
	curl_setopt($paymentSub, CURLOPT_HEADER, 0);
	curl_setopt($paymentSub, CURLINFO_HEADER_OUT, 0);
	curl_setopt($paymentSub, CURLOPT_HTTPHEADER, $trx_headers);
	curl_setopt($paymentSub, CURLOPT_POSTFIELDS, $sub_RequestBody);
	curl_setopt($paymentSub, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($paymentSub, CURLOPT_SSL_VERIFYPEER, false);
	$paymentSub_response = curl_exec($paymentSub);
	/*curl_close ($paymentSub);
	$jsonObj = json_decode($paymentSub_response);
	$redirectUrl = $jsonObj->{'redirectUrl'};
	header("location:$redirectUrl");//redirect to the respose redirectUrl*/
	

	$responseCode=curl_getinfo($paymentSub,CURLINFO_HTTP_CODE);

	//if the url is successful,fetch the redirect url and redirect it.
	//else display the error.

        if($responseCode==200)
        {
            $jsonObj = json_decode($paymentSub_response);
	    	$redirectUrl = $jsonObj->{'redirectUrl'};
	    	header("location:$redirectUrl");
        }
        else{
            echo curl_error($paymentSub);
        }
        curl_close($paymentSub);
	

}
?>
<hr>
    <?php //form for get subscription details ?>
<form name="getSubscriptionDetails" action="" method="get">
    Subscription ID <input type="text" name="merchantSubscriptionIdList" value="<%=merchantSubscriptionIdList%>" size=40/><br />
    Access Token <input type="text" name="access_token" value="<%=accessToken%>" size=40/><br>
    <input type="submit" name="getSubscriptionDetails" value="Get Subscription Details" /></form><br>

<?php if ($SubDetailsRequest != null) {
        $TxnId = $_GET['trxID'];
	//url to get subscription details
	$getSubscriptionDetails_Url = "https://beta-api.att.com/1/payments/subscriptions/";
	$getSubscriptionDetails_Url .= $TxnId;
	$getSubscriptionDetails_Url .= "?access_token=".$accessToken;
	//http header values
	$getSubscriptionDetails_headers = array(
		'Content-Type: application/x-www-form-urlencoded'
	);
	
	// print "<p>URL to get SMS Delivery Status:</br>$getSMSDelStatus_Url</p>";

	$getSubscriptionDetails = curl_init();
	
	curl_setopt($getSubscriptionDetails, CURLOPT_URL, $getSubscriptionDetails_Url);
	curl_setopt($getSubscriptionDetails, CURLOPT_HTTPGET, 1);
	curl_setopt($getSubscriptionDetails, CURLOPT_HEADER, 0);
	curl_setopt($getSubscriptionDetails, CURLINFO_HEADER_OUT, 0);
	curl_setopt($getSubscriptionDetails, CURLOPT_HTTPHEADER, $getSubscriptionDetails_headers);
	curl_setopt($getSubscriptionDetails, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($getSubscriptionDetails, CURLOPT_SSL_VERIFYPEER, false);
        $getSubscriptionDetails_response = curl_exec($getSubscriptionDetails);
	
	$responseCode=curl_getinfo($getSubscriptionDetails,CURLINFO_HTTP_CODE);
	//if the request is successful, display the subscription details response.else display the error
        if($responseCode==200)
        {
            echo "Subscription details : ".$getSubscriptionDetails_response;
        }
        else{
            echo curl_error($getSubscriptionDetails);
        }
        curl_close($getSubscriptionDetails);
}
?>
 <?php //form for commit transaction ?> 
<form name="commitTransaction" action="" get="post">
    Transaction ID <input type="text" name="trxID" value="<%=trxID%>" size=40/><br />
    Access Token <input type="text" name="access_token" value="<?php echo $accessToken ?>" size=40/><br>
    <input type="submit" name="commitTransaction" value="Commit Transaction" /></form><br>

<?php if ($TrxCommitRequest != null){

 
	$TxnId = $_GET["trxID"];
	//url to commit transaction
	$getPaymentCommit_Url = "https://beta-api.att.com/1/payments/transactions/";
	$getPaymentCommit_Url .= $TxnId;
	$getPaymentCommit_Url .= "?access_token=".$accessToken;
	//http header values
	$getPaymentCommit_headers = array(
		'Content-Type: application/json'
	);
	//post data
        $sendPaymentCommit_RequestBody = '{"transactionStatus":"COMMITTED"}';

	// print "<p>URL to get SMS Delivery Status:</br>$getSMSDelStatus_Url</p>";

	$getPaymentCommit = curl_init();
	
	curl_setopt($getPaymentCommit, CURLOPT_URL, $getPaymentCommit_Url);
	curl_setopt($getPaymentCommit, CURLOPT_POST, 1);
	curl_setopt($getPaymentCommit, CURLOPT_HEADER, 0);
	curl_setopt($getPaymentCommit, CURLINFO_HEADER_OUT, 0);
	curl_setopt($getPaymentCommit, CURLOPT_HTTPHEADER, $getPaymentCommit_headers);
	curl_setopt($getPaymentCommit, CURLOPT_POSTFIELDS, $sendPaymentCommit_RequestBody);
	curl_setopt($getPaymentCommit, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($getPaymentCommit, CURLOPT_SSL_VERIFYPEER, false);
        $getPaymentCommit_response = curl_exec($getPaymentCommit);
	
	//Error Catching	
        $responseCode=curl_getinfo($getPaymentCommit,CURLINFO_HTTP_CODE);
	//if the request is successful, display the commit response.else display the error
        if($responseCode==200)
        {
            echo "Status for Commit Transaction : ".$getPaymentCommit_response;
        }
        else{
            echo curl_error($getPaymentCommit);
        }
        curl_close($getPaymentCommit);
}
?>

<?php //get the redirect response action ?>
<?php if ($_GET["action"] == "UserConfirmed"){

 
	echo " User has confirmed AOC ";
}
?>

<?php if ($_GET["action"] == "UserCancelled"){

 
	echo " User has cancelled AOC ";
}
?>

<?php if ($_GET["action"] == "Status"){

 
	echo " Status callback URL has been invoked";
}
?>
</body>
</html>
