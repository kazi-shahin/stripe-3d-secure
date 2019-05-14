<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Stripe Payment Form</title>
	<link rel="stylesheet" href="css/bootstrap-min.css">
	<style type="text/css">
		.row-centered {
			margin-left: 9px;
			margin-right: 9px;
		}
	</style>
	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="js/bootstrap-min.js"></script>
</head>
<body>
	<div class="payment-failed col-md-12" id="payment-errors" style="display: none;">
		<div class="alert alert-danger col-md-6"> <strong>Error!</strong> <span class="payment-errors"></span> </div>
	</div>
	<div class="payment-success col-md-12" id="payment-success" style="display: none;">
		<div class="alert alert-success col-md-6"> <strong>Thanks!</strong> <span class="payment-succeeded">Your payment is successfull</span> </div>
	</div>
	<div class="payment-error hidden"></div>
	<form action="" method="POST" id="payment-form" class="form-horizontal">
		<div class="row row-centered">
		  	<div class="col-md-12 col-md-offset-2"></div>
		  	<div class="page-header">
		    	<h2 class="gdfg">Stripe Payment Form</h2>
			</div>
			<fieldset>
			 <!-- Form Name -->
				<legend>Billing Details</legend>
				  
				<!-- Street -->
				<div class="form-group">
				    <label class="col-sm-4 control-label" for="textinput">Street</label>
				    <div class="col-sm-6">
				    	<input type="text" name="street" placeholder="Street" class="address form-control">
				    </div>
				</div>
				<!-- City -->
				<div class="form-group">
					<label class="col-sm-4 control-label" for="textinput">City</label>
					<div class="col-sm-6">
						<input type="text" name="city" placeholder="City" class="city form-control">
					</div>
				</div>
				<!-- State -->
				<div class="form-group">
					<label class="col-sm-4 control-label" for="textinput">State</label>
					<div class="col-sm-6">
						<input type="text" name="state" maxlength="65" placeholder="State" class="state form-control">
					</div>
				</div>
				<!-- Postcal Code -->
				<div class="form-group">
					<label class="col-sm-4 control-label" for="textinput">Postal Code</label>
					<div class="col-sm-6">
						<input type="text" name="zip" maxlength="9" placeholder="Postal Code" class="zip form-control">
					</div>
				</div>

				<!-- Email -->
				<div class="form-group">
					<label class="col-sm-4 control-label" for="textinput">Email</label>
					<div class="col-sm-6">
						<input type="text" name="email" maxlength="65" placeholder="Email" class="email form-control">
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>Card Details</legend>
				
				<!-- Card Holder Name -->
				<div class="form-group">
					<label class="col-sm-4 control-label"  for="textinput">Card Holder's Name</label>
					<div class="col-sm-6">
						<input type="text" name="cardholdername" maxlength="70" placeholder="Card Holder Name" class="card-holder-name form-control">
					</div>
				</div>
				
				<!-- Card Number -->
				<div class="form-group">
					<label class="col-sm-4 control-label" for="textinput">Card Number</label>
					<div class="col-sm-6">
						<input type="text" id="cardnumber" maxlength="19" placeholder="Card Number" class="card-number form-control">
					</div>
				</div>
				
				<!-- Expiry-->
				<div class="form-group">
					<label class="col-sm-4 control-label" for="textinput">Card Expiry Date</label>
					<div class="col-sm-6">
						<div class="form-inline">
							<select name="select2" data-stripe="exp-month" class="card-expiry-month stripe-sensitive required form-control">
								<option value="01" selected="selected">01</option>
								<option value="02">02</option>
								<option value="03">03</option>
								<option value="04">04</option>
								<option value="05">05</option>
								<option value="06">06</option>
								<option value="07">07</option>
								<option value="08">08</option>
								<option value="09">09</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
							</select>
							<span> / </span>
							<select name="select2" data-stripe="exp-year" class="card-expiry-year stripe-sensitive required form-control">
							</select>
							<script type="text/javascript">
							var select = $(".card-expiry-year"),
							year = new Date().getFullYear();
							
							for (var i = 0; i < 12; i++) {
							select.append($("<option value='"+(i + year)+"' "+(i === 0 ? "selected" : "")+">"+(i + year)+"</option>"))
							}
							</script>
						</div>
					</div>
				</div>
				
				<!-- CVV -->
				<div class="form-group">
					<label class="col-sm-4 control-label" for="textinput">CVV/CVV2</label>
					<div class="col-sm-3">
						<input type="text" id="cvv" placeholder="CVV" maxlength="4" class="card-cvc form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="textinput">Amount</label>
					<div class="col-sm-2">
						<input type="text" id="amount" placeholder="amount" class="card-amount form-control">
					</div>
				</div>
				<!-- Submit -->
				<div class="control-group">
					<div class="controls">
						<center>
						<button class="btn btn-success" type="submit">Pay</button>
						</center>
					</div>
				</div>
			</fieldset>
		</div>

	</form>

	<script>
		jQuery(function($){
			//Amount on change
			$("#amount").on("change",function(){
				$(".amountInButton").text("");
				var amount = $("#amount").val();
				$("#amount").val(amount);
			});

			//Event on submit
			$("#payment-form").on("submit",function(event){
				event.preventDefault();
				var street 		= $(".address").val();
				var city 		= $(".city").val();
				var state 		= $(".state").val();
				var postal 		= $(".zip").val();
				var email 		= $(".email").val();
				var cardHolder	= $(".card-holder-name").val();
				var card		= $(".card-number").val();
				var month		= $(".card-expiry-month").val();
				var year		= $(".card-expiry-year").val();
				var cvc			= $(".card-cvc").val();
				var amount 		= $(".card-amount").val();
				var url			= 'api-action.php';

				$.ajax({
				type: "POST",
	                url: url,
	                data: {
	                	street:street,
	                	city:city,
	                	state:state,
	                	email:email,
	                	postal:postal,
	                	amount:amount,
	                	card:card,
	                	month:month,
	                	year:year,
	                	cvc:cvc,
	                	cardHolder:cardHolder
	                },
	                success: function (data) {
	                	if (data == 200) {
	                		$("html, body").animate({ scrollTop: 0 }, "slow");
	                		$("#payment-errors").css('display','none');
	                		$("#payment-success").fadeIn('slow');
	                	} else {
	                		$("html, body").animate({ scrollTop: 0 }, "slow");
	                		$("#payment-success").css('display','none');
	                		$(".payment-errors").text(data);
	                		$("#payment-errors").fadeIn('slow');
	                	}
	                },

	                async: false

			});
				
			});
		});
	</script>
</body>
</html>