<?php
//Library Included
require(dirname(__FILE__) . '/libs/init.php');
//Amount to be paid
$amount = $_POST['amount'];
$amount = round($amount,2);
$amount	= sprintf("%.2f", $amount);
$amount = preg_replace('/\D/', '', $amount);

//Set Your API Key Here. Here is a test api key. Replace your api key
$api_key = 'sk_test_clYlsULIRQsOp0J1mEkbCc5K'; 

if($_POST){
	\Stripe\Stripe::setApiKey($api_key);
	try {
		//Token Creation
		$token = \Stripe\Token::create([
			  'card' => [
			    'number' 			=> $_POST['card'],
			    'exp_month' 		=> $_POST['month'],
			    'exp_year' 			=> $_POST['year'],
			    'cvc' 				=> $_POST['cvc'],
			    'name' 				=> $_POST['cardHolder'],
			    'address_line1' 	=> $_POST['street'],
			    'address_city' 		=> $_POST['city'],
			    'address_zip' 		=> $_POST['postal'],
			    'address_state' 	=> $_POST['state'],

			  ]
			]);
		//Get payment method id using PaymentMethod API
			if ($token['id']) {
				$payment_method = \Stripe\PaymentMethod::create([
					'type'=>'card',
				  	'card' => [
					    'number' 	=> 	$_POST['card'],
					    'exp_month' => 	$_POST['month'],
					    'exp_year' 	=> 	$_POST['year'],
					    'cvc' 		=> 	$_POST['cvc'],
				  	],
				  	'billing_details' => [
					  	'address' 	=> [
						    'line1' 		=> 	$_POST['street'],
						    'city' 			=> 	$_POST['city'],
						    'postal_code' 	=> 	$_POST['postal'],
						    'state' 		=> 	$_POST['state']
				  	],
				  	'name' 	=> 	$_POST['cardHolder'],
				  	'email' =>	$_POST['email']

				  ],
				]);

				//Get 3d Secured your created token and payment method id using PaymentIntent API
				if ($payment_method['id']) {
					$PaymentIntent = \Stripe\PaymentIntent::create(
					 	[
					 		'amount' 				=> $amount, 
					 		'currency' 				=> 'usd', 
					 		'payment_method_types' 	=> ['card'],
					 		'payment_method' 		=> $payment_method['id'],
					 		'confirm' 				=> true,
					 		'description' 			=> $_POST['email'],
						]
					 );

					//Get Status
					if ($PaymentIntent['status'] == 'succeeded') {
						echo $status = 200;
					} else {
						echo $PaymentIntent['status'];
					}
				}
			}
	}
	//Cathc Exceptions and Error Handling 
	catch (\Stripe\Error\Card $e) {
		$body = $e->getJsonBody();
  		$err  = $body['error'];
  		echo $err['message'];

	}catch (\Stripe\Error\InvalidRequest $e) {
		$body = $e->getJsonBody();
  		$err  = $body['error'];
  		//If amount or any important field is empty 
  		if($err['code'] == "parameter_invalid_integer"){
  			echo "Please fill amount field";
  		} elseif ($err['code'] == "parameter_invalid_empty") {
  			echo "Pleasse fill all the card fields";
  		} else {
  			echo $err['message'];
  		}

	}catch (\Stripe\Error\Authentication $e) {
		$body = $e->getJsonBody();
  		$err  = $body['error'];
  		echo $err['message'];

	} catch (\Stripe\Error\ApiConnection $e) {
		$body = $e->getJsonBody();
  		$err  = $body['error'];
  		echo $err['message'];

	} catch (Exception $e) {
	  echo $status = "Something Went Wrong! Please try again later";
	}
}