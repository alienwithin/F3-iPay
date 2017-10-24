# F3-iPay
F3-iPay is a Fat Free Framework plugin that helps in easy implementation of iPay web Checkout.


## Quick Start Config
Add the following custom section to your project config if using the ini style configuration.

```ini
[IPAY]
vendorID=demo
hashkey=demo
call_back=yourCallBackURL
currency=KES
endpoint=sandbox
log=1
mpesa=1
airtel=1
equity=1
mobilebanking=1
debitcard=1
creditcard=1
mkoporahisi=0
saida=0
autopay=0
cst=1
crl=http
```

- vendorID - Vendor ID assigned by iPay if on test use demo
- hashkey - hashkey assigned by iPay if on test use demo
- call_back - The URL that iPay redirects your buyers to after they have Checked out
- currency - The currency in use e.g. KES or USD
- endpoint - API Endpoint, values can be 'sandbox' or 'production' which hydrates the parameter to determine if app is live or not.
- log - logs all API requests & responses to pesapal.log
- mpesa - Display Mpesa Mobile Money Channel (on or off). “on” by Default (i.e. mpesa=1)	
- airtel - Display Airtel Mobile Money Channel (on or off). “on” by Default (i.e. airtel=1)	
- equity - Display the Equity EazzyPay Channel (on or off). “on” by Default (i.e. equity=1)	
- mobilebanking - Display the Mobile Banking Channel (on or off). “off” by Default (i.e. mobilebanking=0)	
- debitcard - Display the Debit Card Channel (on or off). “off” by Default (i.e. debitcard=0)	
- creditcard - Display the Credit Card Channel (on or off). “off” by Default (i.e. creditcard=1)	
- mkoporahisi - Display Mkopo Rahisi Channel (on or off). “off” by Default(i.e. mkoporahisi=0)	
- saida - Display Saida Channel (on or off). “off” by Default (i.e. saida=0)	
- autopay - Push Data (on or off). “off” by Default (i.e. autopay=0) Set this parameter to 1 if you want iPay to silently trigger the callback. The CONFIRM button on the checkout page will not be present for mobile money and mobile banking NOTE Valid Callback Parameter must be provided. when this parameter is set iPay will send data to your server using GET request and the IPN should be run to verify this data.
- cst - The customer email notification flag of value 1 or 0. (Set to “1” By Default to allow customer to receive txn notifications from iPay for online txns)	
crl - Name of the cURL flag input field (1 character).
		crl=http for http/https call back
		crl=data_stream for a data stream of comma separated values
		crl=json for a json data stream.
		(Set to “http” By Default)

If you prefer you can also pass an array with above values when you instantiate the classes.

```php
// F3-iPay config
$iPayConfig = array(
'vendorID'=>'demo',
'hashkey'=>'demo',
'call_back'=>'yourCallBackURL',
'currency'=>'KES',
'endpoint'=>'sandbox',
'log'=>'1',
'mpesa'=>'1',
'airtel'=>'1',
'equity'=>'1',
'mobilebanking'=>'1',
'debitcard'=>'1',
'creditcard'=>'1',
'mkoporahisi'=>'0',
'saida'=>'0',
'autopay'=>'0',
'cst'=>'1',
'crl'=>'http'
);

// Instantiate the class with config
$iPay=new iPay($iPayConfig);
```


**Manual Install**
Copy the `lib/IPay.php` file into your `lib/` or your AUTOLOAD folder.  



## Quick Start
### iPay Checkout
The process is going to be a multistep process as below assuming a simple form based test: 
1. Create IPay Instance

```php
$iPay=new IPay;
```
2. Populate PesaPal mandatory variables to create the valid XML to post to pesapal
```php
/*Define IPay Mandatory Variables*/

$email=$f3->get('POST.email');
$telephone=$f3->get('POST.telephone');
$totalAmount = $f3->get('POST.Amount');
/*End Define IPay Mandatory Variables*/
//Create IPay Checkout URL and submit
//You can do some DB operations here based on the variables as you POST the XML
$content=$iPay->process_iPay_payment($orderID,$totalAmount,$email,$telephone,$p1="",$p2="",$p3="",$p4="");
//define area to load the generated Ipaycheckout form for submission using the F3 Hive
$f3->set('content',$content);
//Render on page
echo \Template::instance()->render('pesapal.html');
```

Your actual View i.e. ipay.html will be tagged a sample is as below: 
```html
<include href="header.html" />
{{@content | raw}}
<include href="footer.html" />
```

View the [sample application](https://github.com/alienwithin/F3-iPay/tree/master/sample-application) to understand implementation aspects of the gateway and test it. 

## License
F3-IPay is licensed under GPL v.3
