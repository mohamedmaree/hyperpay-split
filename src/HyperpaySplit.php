<?php
namespace maree\hyperpaySplit;

use Illuminate\Support\Facades\Http;

class HyperpaySplit {

    //$type => iban || bank_account
    //if type == iban must insert $swift_code
    //in live mode  must insert $bank_iban_number
    //$customerInfo = ['id' => '1' , 'name' => 'mohamed maree' ,'address1' => 'mehalla','address2'=>'cairo' ,'address3' => 'egypt' ]
    //$amount = 1.0
    //authorization => accessToken can be empty else if you created one and passed in that function

    public static function sendTransferRequest($customerInfo = [],$amount = 1.00,$type = 'bank_account',$swift_code ='',$bank_iban_number = '',$authorization = '') {

        $split_mode = config('hyperpaySplit.mode');
        if($split_mode == 'live'){
            $url       = config('hyperpaySplit.live_login_url');
            $order_url = config('hyperpaySplit.live_order_url');
            $accountId = $bank_iban_number;
        }else{
            $url       = config('hyperpaySplit.test_login_url');
            $order_url = config('hyperpaySplit.test_order_url');
            $accountId = config('hyperpaySplit.test_account_id');            
        }
        /******** start  create accessToken ******/
        if($authorization == ''){
            $response =  Http::asForm()->post($url ,
                                    [
                                        'email'    => config('hyperpaySplit.email'),
                                        'password' => config('hyperpaySplit.password')
                                    ]);
            $array = json_decode($response->getBody()->getContents(), true);
            if($array['status'] == false){
                return [ 'status' => $array['status'] ,'data' => json_encode($array['data']) ,'message' => $array['message'] , 'errors' => $array['errors'] ];
            }
            $authorization = $array['data']['accessToken'];
        }
        /******** end create accessToken ******/

        $myBody['merchantTransactionId'] = rand(111111,999999).$customerInfo['id'];
        $myBody['transferOption'] = "0";
        $myBody['configId']       = config('hyperpaySplit.config_id');
        if($type == 'iban'){
            $arr[]=[
                "name"                      => $customerInfo['name'],
                "accountId"                 => $accountId,
                "debitCurrency"             => config('hyperpaySplit.currency'),
                "bankIdBIC"                 => $swift_code,
                "transferAmount"            => number_format((float)$amount, 2, '.', ''),
                "transferCurrency"          => config('hyperpaySplit.currency'),
                "payoutBeneficiaryAddress1" => ($customerInfo['address1'])??'',
                "payoutBeneficiaryAddress2" => ($customerInfo['address2'])??'',
                "payoutBeneficiaryAddress3" => ($customerInfo['address3'])??''
            ];

        }else{
            $arr[]=[
                "name"                      => $customerInfo['name'],
                "accountId"                 => $accountId,
                "debitCurrency"             => config('hyperpaySplit.currency'),
                "transferAmount"            => number_format((float)$amount, 2, '.', ''),
                "transferCurrency"          => config('hyperpaySplit.currency'),
                "payoutBeneficiaryAddress1" => ($customerInfo['address1'])??'',
                "payoutBeneficiaryAddress2" => ($customerInfo['address2'])??'',
                "payoutBeneficiaryAddress3" => ($customerInfo['address3'])??''
            ];
        }
        $myBody['beneficiary'] = $arr;
        
        //create transfer request
        $response = Http::asForm()->post($order_url , [
            'headers'     => ['Authorization'=>'Bearer '.$authorization],
            'form_params' => $myBody
        ]);
        $responseResult = json_decode($response->getBody()->getContents(), true);
        return [ 'transaction_id' => isset($responseResult) ? $responseResult['data']['uniqueId'] : '' ,'responseData' => $responseResult];
    }
    

    public static function receiveTransferResponse(){
        $http_body = file_get_contents('php://input');
        $notification_key_from_configration = config('hyperpaySplit.configuration_key');
        
        $headers                   = getallheaders();
        $iv_from_http_header       = ($headers['X-Initialization-Vector'])??'';
        $auth_tag_from_http_header = ($headers['X-Authentication-Tag'])??'';
        $http                      = json_decode($http_body);
        $body                      = ($http->encryptedBody)??'';

        $key         = hex2bin($notification_key_from_configration);
        $iv          = hex2bin($iv_from_http_header);
        $auth_tag    = hex2bin($auth_tag_from_http_header);
        $cipher_text = hex2bin($body);
        $result      = openssl_decrypt($cipher_text, "aes-256-gcm", $key, OPENSSL_RAW_DATA, $iv, $auth_tag);
        if($result = json_decode($result)){
            $uniqueId = ($result->data->transactions[0]->uniqueId)??'';
            if($result->status == true){
                return [ 'key' => 'success' ,'transaction_id' => $uniqueId ,'responseData' => $result];
            }else{
                return [ 'key' => 'fail' ,'transaction_id' => $uniqueId ,'responseData' => $result];
            }

        }else{
            return ['key' => 'fail','transaction_id' => '' ,'responseData' => $result];
        }
    }


}