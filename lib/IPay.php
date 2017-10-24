<?php
/**
*	@Package: FatFree IPay Payment Gateway
*	@Description: Allows use of kenyan payment processor Ipay - https://ipayafrica.com/api/
*	@Version: 1.0.1
*	@Author: Munir Njiru
*	@Author URI: https://www.alien-within.com
*	@License: GPLv3
*	@License URI: http://www.gnu.org/licenses/gpl-3.0.html
*
*	Copyright 2017  Munir Njiru  (email : munir@alien-within.com)
*/
class IPay
{
	protected $f3;
	private   $iPaySettings = array();
    public    $live;
    public    $line_items = array();
    public    $item_counter = 0;
    public    $item_total = 0;
    public    $logger;
	public	  $crl;
	
	/**
     *    Class constructor
     *    Defines iPay Settings in configuration file
     *    @param  $options array
     */
    function __construct($options = null)
    {
        $f3 = Base::instance();
        @session_start();
        $f3->sync('SESSION');
        if ($options == null)
            if ($f3->exists('IPAY'))
                $options = $f3->get('IPAY');
            else
                $f3->error(500, 'No configuration options set for iPay on Fat Free Framework');
		if ($options['endpoint'] == "production") {
            $this->live = '1';
        } else {
            $this->live = '0';
        }
		if ($options['crl']=="http"){
			$this->crl= '0';
		}elseif ($options['crl']=="data_stream"){
			$this->crl= '1';
		}
		else{
			$this->crl= '2';
		}
		$this->iPaySettings['vendorID'] = $options['vendorID'];
        $this->iPaySettings['hashkey'] = $options['hashkey'];
		$this->iPaySettings['callback'] = $options['call_back'];
		$this->iPaySettings['currency'] = $options['currency'];
		$this->iPaySettings['customer_email_notification'] = $options['cst'];
		//Define Channel status in requests
		$this->iPaySettings['mpesa'] = $options['mpesa'];
		$this->iPaySettings['airtel'] = $options['airtel'];
		$this->iPaySettings['equity'] = $options['equity'];
		$this->iPaySettings['mobilebanking'] = $options['mobilebanking'];
		$this->iPaySettings['debitcard'] = $options['debitcard'];
		$this->iPaySettings['creditcard'] = $options['creditcard'];
		$this->iPaySettings['mkoporahisi'] = $options['mkoporahisi'];
		$this->iPaySettings['saida'] = $options['saida'];
		$this->iPaySettings['autopay'] = $options['autopay'];
		//End define Channel status in requests
        if ($options['log']) {
            $this->logger = new Log('iPay.log');
        }
	}
	/**
     * Build array of line items & calculating item total.
     * @param $item_name string
     * @param $item_quantity integer
     * @param $item_price string
     */
    function setLineItem($item_name, $item_quantity = 1, $item_price)
    {
        $i = $this->item_counter++;
        $this->line_items["L_PAYMENTREQUEST_0_NAME$i"] = $item_name;
        $this->line_items["L_PAYMENTREQUEST_0_QTY$i"] = $item_quantity;
        $this->line_items["L_PAYMENTREQUEST_0_AMOUNT$i"] = $item_price;
        $this->item_total += ($item_quantity * $item_price);
    }
	/**
     * Create iPay Payment Session.
     * @param $orderID string
     * @param $totalAmount integer
     * @param $email string
	 * @param $telephone string
	 * @param $p1 string
	 * @param $p2 string
	 * @param $p3 string
	 * @param $p4 string
     */
	function process_iPay_payment($orderID,$totalAmount,$email,$telephone,$p1="",$p2="",$p3="",$p4=""){
		$web = \Web::instance();
		//initialize iPay Variables from settings
		$getLiveStatus = $this->live;
		$getVendorID = $this->iPaySettings['vendorID'];
		$getHashkey = $this->iPaySettings['hashkey'];
		$getCallBack = $this->iPaySettings['callback'];
		$getCurrency = $this->iPaySettings['currency'];
		$getCst = $this->iPaySettings['customer_email_notification'];
		$getMpesa = $this->iPaySettings['mpesa'];
		$getAirtel = $this->iPaySettings['airtel'];
		$getEquity = $this->iPaySettings['equity'];
		$getMobileBanking = $this->iPaySettings['mobilebanking'];
		$getDebitCard = $this->iPaySettings['debitcard'];
		$getCreditCard = $this->iPaySettings['creditcard'];
		$getMkopoRahisi = $this->iPaySettings['mkoporahisi'];
		$getSaida = $this->iPaySettings['saida'];
		$getAutopay = $this->iPaySettings['autopay'];
		$getCrl= $this->crl;
		$invoiceNumber=$orderID;
		//End initialize iPay Variables from settings
		//create iPay concatenated data string
		$datastring =  $getLiveStatus.$orderID.$invoiceNumber.$totalAmount.$telephone.$email.$getVendorID.$getCurrency.$p1.$p2.$p3.$p4.$getCallBack.$getCst.$getCrl;
		//generate hash value for hsh parameter using the datastring
		$finalhashValue = hash_hmac("sha1", $datastring, $getHashkey);
		//generate iPay payment form and redirect to the site immediately to start checkout
		$ipay_endpoint="https://payments.ipayafrica.com/v3/ke";		
		$sendtoIpay ='
		<script>
			window.onload = function(){
			  document.forms[\'ipaysend\'].submit();
			}</script>
		<form name="ipaysend" method="post" action="'.$ipay_endpoint.'">
				 <input name="live" type="hidden" value="'.$getLiveStatus.'"></br>
				 <input name="mpesa" type="hidden" value="'.$getMpesa.'"></br>
				 <input name="airtel" type="hidden" value="'.$getAirtel.'"></br>
				 <input name="equity" type="hidden" value="'.$getEquity.'"></br>
				 <input name="mobilebanking" type="hidden" value="'.$getMobileBanking.'"></br>
				 <input name="debitcard" type="hidden" value="'.$getDebitCard.'"></br>
				 <input name="creditcard" type="hidden" value="'.$getCreditCard.'"></br>
				 <input name="mkoporahisi" type="hidden" value="'.$getMkopoRahisi.'"></br>
				 <input name="saida" type="hidden" value="'.$getSaida.'"></br>
				 <input name="autopay" type="hidden" value="'.$getAutopay.'"></br>
				 <input name="oid" type="hidden" value="'.$orderID.'"></br>
				 <input name="inv" type="hidden" value="'.$invoiceNumber.'"></br>
				 <input name="ttl" type="hidden" value="'.$totalAmount.'"></br>
				 <input name="tel" type="hidden" value="'.$telephone.'"></br>
				 <input name="eml" type="hidden" value="'.$email.'"></br>
				 <input name="vid" type="hidden" value="'.$getVendorID.'"></br>
				 <input name="curr" type="hidden" value="'.$getCurrency.'"></br>
				 <input name="p1" type="hidden" value="'.$p1.'"></br>
				 <input name="p2" type="hidden" value="'.$p2.'"></br>
				 <input name="p3" type="hidden" value="'.$p3.'"></br>
				 <input name="p4" type="hidden" value="'.$p4.'"></br>
				 <input name="cbk" type="hidden" value="'.$getCallBack.'"></br>
				 <input name="cst" type="hidden" value="'.$getCst.'"></br>
				 <input name="crl" type="hidden" value="'.$getCrl.'"></br>
				 <input name="hsh" type="hidden" value="'.$finalhashValue.'"
				 <input name="submit" type="submit" value="submit">
				 </form>'
				 ;

		return $sendtoIpay;
	}
	
	/**
     * Copy basket() to iPay Checkout
     * Transfer your basket details to the iPay Checkout
     * Returns a total value of items
     * @param  $basket object
     * @param  $name string
     * @param  $amount string
     */
    function copyBasket($basket, $name = 'name', $quantity = 'qty', $amount = 'amount')
    {
        $totalamount = 0;
        foreach ($basket as $lineitem) {

            if (empty($lineitem->{$quantity})) {
                $lineitem->{$quantity} = 1;
            }

            $this->setLineItem($lineitem->{$name}, $lineitem->{$quantity}, $lineitem->{$amount});
            $totalamount += $lineitem->{$amount} * $lineitem->{$quantity};
        }

        return $totalamount;
    }
	
}