<?php

// Kickstart the framework
$f3=require('lib/base.php');

// Load configuration
$f3->config('config.ini');

/*Home page loads the basket and prepopulates Items
Ideally this is where you would have the logic for basket for users to select items and add to cart.
In this case we have just initialized as it is a demo
*/
$f3->route('GET /',
	function($f3) {
		$basket = new \Basket();
        $basket->drop();

        // add item
        $basket->set('name', 'Kahawa');
        $basket->set('amount', '15.00');
        $basket->set('qty', '2');
        $basket->save();
        $basket->reset();
        // add item
        $basket->set('name', 'Mafuta');
        $basket->set('amount', '100.00');
        $basket->set('qty', '1');
        $basket->save();
        $basket->reset();

        $cart = $basket->find();
        foreach ($cart as $item) {
            $subtotal += $item['amount'] * $item['qty'];
            $itemcount+=$item['qty'];
        }
        $f3->set('itemcount', $itemcount);
        $f3->set('cartitems', $cart);
        $f3->set('subtotal', sprintf("%01.2f", $subtotal));
        echo \Template::instance()->render('choose.html');
	}
);
/*
Here we display the cart summary as the checkout process begins. 
We get the buyer/billing information and number of items in basket
*/
$f3->route('GET|POST /checkout',
    function ($f3) {
        $basket = new \Basket();
        $f3->set('itemcount', $basket->count());
        echo \Template::instance()->render('checkout.html');
    }
);
/*
We then perform the checkout finalization by sending the required items to iPay;
*/
$f3->route('GET|POST /ipay',
	function ($f3) {
		$basket = new \Basket();
        $cartitems = $basket->find();
		$iPay= new IPay;
		/*Define iPay Mandatory Variables*/
		$orderID=generateIPayTransactionID();
		$email=$f3->get('POST.email');
		$telephone=$f3->get('POST.telephone');
		$subtotal = $iPay->copyBasket($cartitems);
		/*End Define iPay Mandatory Variables*/
		//Generate request and post to iPay
		$content=$iPay->process_iPay_payment($orderID,$subtotal,$email,$telephone,$p1="",$p2="",$p3="",$p4="");
		$f3->set('content',$content);
		//Render on page
		echo \Template::instance()->render('ipay.html');
	}
);
/*iPay responds to our call back URL*/
$f3->route('GET|POST /thankyou',
    function ($f3) {
			//perform DB operations update to PENDING after getting a tracking ID
			echo \Template::instance()->render('thanks.html');
    }
);

/*
Simple function used to generate alphanumeric transaction IDs
*/
function generateIPayTransactionID($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}
$f3->run();
