# F3-IPAY Sample Implementation

To use this sample application update the config.ini parameters with your environment parameters i.e. 

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

If being placed in a sub folder also update the RewriteBase line of the .htaccess file e.g. if installed in a sub folder called iPay line 8 of the htaccess will be: 

```
RewriteBase /iPay
```

The application takes a 3 step- process to checkout: 
* Load shopping cart , currently initialized using the basket function of F3; basically this means add all items needed to cart. 
* Fill in mandatory buyer information amount is held from the cart so no need to re-fill it here. 
* Post the transaction to IPay for checkout 


To use in production change the endpoint to production; additionally update the keys and call back URL.

## Checkout Process
1. Select and add items to cart
This helps calculate the total amount
![Select and add Items to cart](https://github.com/alienwithin/F3-iPay/raw/master/sample-application/1-choose-products-to-buy.PNG "IPay Integration in FatFree")

2. Fill in Buyer information

![Fill in buyer information that is mandatory](https://github.com/alienwithin/F3-iPay/raw/master/sample-application/2-fill-in-buyer-information.PNG "IPay Integration in FatFree")

3. Pay using preferred method on iPay

![Pay and confirm transaction on IPay](https://github.com/alienwithin/F3-iPay/raw/master/sample-application/3-checkout-using-ipay.PNG "Ipay Integration in FatFree")
