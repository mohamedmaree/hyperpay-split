# hyperpay Split
## Installation

You can install the package via [Composer](https://getcomposer.org).

```bash
composer require maree/hyperpay-split
```
Publish your hyperpaySplit.php config file with

```bash
php artisan vendor:publish --provider="maree\hyperpaySplit\HyperpaySplitServiceProvider" --tag="hyperpaySplit"
```
then change your hyperpaySplit config from config/hyperpaySplit.php file
```php
    "mode"              => "test" , //or live
    "email"             => "",
    "password"          => "",
    "config_id"         => "",
    "configuration_key" => "",
```
## Usage

```php
use maree\hyperpaySplit\HyperpaySplit;
//$type => iban || bank_account
//if type == iban must insert $swift_code
//in live mode  must insert $bank_iban_number
//$customerInfo = ['id' => '1' , 'name' => 'mohamed maree' ,'address1' => 'mehalla','address2'=>'cairo' ,'address3' => 'egypt' ]
//$amount = 1.0
//authorization => accessToken can be empty else if you created one and passed in that function
HyperpaySplit::sendTransferRequest($customerInfo = [],$amount = 1.00,$type = 'bank_account',$swift_code ='',$bank_iban_number = '',$authorization = '');  

```

## note 
- define (callback) the checkout return response url route with hyperpay split team EX: https://mysite.com/paymentresponse
- create route for response url 'paymentresponse' 
EX: Route::get('paymentresponse', 'PaymentsController@paymentresponse')->name('paymentresponse'); 
- create function for checkout response 'paymentresponse'
- use that function to check if payment failed or success

## inside 'paymentresponse' function use:
```php
use maree\hyperpaySplit\HyperpaySplit;
$response = HyperpaySplit::receiveTransferResponse();  

```
return response like: 
```php

[ 'key' => 'success' ,'transaction_id' => $uniqueId ,'responseData' => $result]

```
or 

```php

[ 'key' => 'fail' ,'transaction_id' => $uniqueId ,'responseData' => $result]

```

- note: you can use response from data to save transactions in database or update transaction status to success or fail. 

## note 
- if you want to create 'authorization' only one time and pass it to that function use that code:
```php
    $split_mode = config('hyperpaySplit.mode');
    if($split_mode == 'live'){
        $url       = config('hyperpaySplit.live_login_url');
    }else{
        $url       = config('hyperpaySplit.test_login_url');
    }
    $response =  Http::asForm()->post($url ,
                            [
                                'email'    => config('hyperpaySplit.email'),
                                'password' => config('hyperpaySplit.password')
                            ]);
    $array = json_decode($response->getBody()->getContents(), true);
    $authorization = $array['data']['accessToken']; 

```

- note: in test mode don't insert $bank_iban_number because we use fixed one SA4280000621608010034790








