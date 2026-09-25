<?php
namespace App\Helpers;
use CommonHelper;
use App\Models\MApiLog;
use Illuminate\Support\Facades\DB;

class CipherpayHelper {
    /**
     * @param String $permissions
     * 
     * @return boolean
     */
    protected static $mainurl, $key, $partnerid, $headerJson, $publicKey, $publicKeyHeader,$aesKey,$aesIv,$partnerToken;
    
    public function __construct()
    {
        self::$mainurl = "https://api.cipherpay.in/api/v2/";
        //self::$key = "JDJ5JDEwJDJILjIyUW0uQ005SUZJRDA0RldPOC5UYzI5bzRwR2s3aGhkZU9ZR0taLjFDem03OFk5dE82Q1AwMDMzNw=="; // token
        self::$partnerid = "20221129"; // 2022XXXX
        self::$headerJson = '{ partnerId":"CP00436","headerToken":"cX1WmDXAW0-8X0FxDpjwF-eORAo-RM2XP-OMMbBWC2Ab"}'; // header json
        $headerfp = fopen(base_path("cert/header_public_key.pem"), "r");
        $headerpublickey = fread($headerfp, 8192);
        fclose($headerfp);
        $bodyfp = fopen(base_path("cert/body_public_key.pem"), "r");
        $bodypublickey = fread($bodyfp, 8192);
        //dd($bodypublickey);
        fclose($bodyfp);
        self::$publicKey = $bodypublickey; // body key
        self::$aesKey = '';
        self::$aesIv = '';
        self::$publicKeyHeader = $headerpublickey; // header key

        self::$partnerToken = 'Q1AwMDQzNjokMnkkMTIkOEtyL0VCaDgwVFNyNDEyVzBwc2tmdVhnQS9yV3VtV3FRbGc2WDJ4b1pqVVEvMkN6NTBiWUM='; // partner Token
    }

    public static function getjwttoken()
    {
        $reqId = rand(111111, 999999);
        $tokendata = array(
            "timestamp" => date('Y-m-d H:i:s'),
            "partnerId" => self::$partnerid,
            "reqId" => $reqId,
        );
        $header = array(
            'alg' => 'HS256',
            'typ' => 'JWT'
        );

        $secret = self::$partnerToken;
       // dd($secret);
        return self::generateJwt($header, $tokendata, $secret);
    }

    public static function generateJwt($header, $payload, $secret)
    {
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $secret, true);
        $signatureEncoded = self::base64UrlEncode($signature);
        //dd($headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded);
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
        
    }

    public static function base64UrlEncode($data)
    {
        $urlSafeData = strtr(base64_encode($data), '+/', '-_');
        return rtrim($urlSafeData, '=');
    }

    public static function payoutTransafer($orderData, $paymentMode = 'BANK_ACCOUNT')
    { 
        $reqData = array(
                "debitAccountNumber" => "924020010610232",
              "mode"=>strtoupper($orderData['mode']),
              "remarks"=> "Vendor Payout",
              "amount"=> $orderData['amount'],
              "type"=> "vendor",
              "bene_name"=> $orderData['name'],
              "bene_mobile"=> $orderData['phone'],
              "bene_email"=> $orderData['email'],
              "bene_acc"=> $orderData['account_number'],
              "bene_ifsc"=> $orderData['account_ifsc'],//strtoupper($post->ifsc),
              "bene_acc_type"=> "Saving",
              "refid"=> $orderData['order_ref_id'],
              "bene_bank_name"=> 'YES Bank'
            
        );
        $request = array(
            "method" => "POST",
            "url" => "cb/singlepayout",
            "parameter" => $reqData
        );
        
        $res = self::hit($request);
        
        
        $output = json_encode($res);
        $response['data'] = json_decode($output);
        //$response['data'] = json_decode($result['response']);

        return $response;
    }
    
    public static function addBene($param)
    {
        $request = array(
            "method" => "POST",
            "url" => "cb/add-beneficiary",
            "parameter" => $param
        );
        
        $res = self::hit($request);
        
        
        $output = json_encode($res);
        $response['data'] = json_decode($output);
        //$response['data'] = json_decode($result['response']);

        return $response;
        
    }
    public static function hit($reqData)
    {
        $url = self::$mainurl . $reqData['url'];
        //dd($url);
        $num = time();
        $reqData['jwt'] = self::getjwttoken();

        if (!empty($reqData['parameter'])) {
            $parameter = json_encode($reqData['parameter']);
        } else {
            $parameter = "";
        }
        $salt = bin2hex(openssl_random_pseudo_bytes(8));

        $data = self::generateAesKey($salt);
        $key = $data[0];
        $iv = $data[1];
        $cipher = 'aes-128-cbc';

        if ($parameter != "") {
            $encrypted = openssl_encrypt(json_encode($parameter), $cipher, $key, OPENSSL_RAW_DATA, $iv);
            $encrypted = base64_encode($encrypted);
        }

        $encryptedSalt = self::rsaEncrypt($salt, self::$publicKey);

        $encryptedHeader = self::rsaEncrypt(self::$headerJson, self::$publicKeyHeader);

        $request = [
            'Auth' => $encryptedHeader,
            'Key' => $encryptedSalt,
            'payload' => $parameter ? ['requestData' => $encrypted] : null,
        ];

        $info = $request;
        $header = array(
                "Token: " . $reqData['jwt'],
                "Auth: " . $info['Auth'],
                "Key:" . $info['Key'],
                "cache-control: no-cache",
                "content-type: application/json",
                "User-Agent: APIAGENT/7.29.2"
            );
        $response = CommonHelper::curl($url, "POST", json_encode($info['payload']) , $header, 'yes','cipherpay');
        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => $url,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 30,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => $reqData['method'],
        //     CURLOPT_POSTFIELDS => json_encode($info['payload']),
        //     CURLOPT_HTTPHEADER => $header
        // ));
        // $response = curl_exec($curl);
        \Log::info('Cipherpay Request:'.json_encode($info['payload']));
        //dd(["URL"=>$url,"method"=>$reqData['method'],"Request"=>json_encode($info['payload']),"JsonRequest"=>json_encode($parameter),"Header"=>$header,"Response"=>$response]);  
        // if (curl_errno($curl)) {
        //     $resp = array("errorCode" => "cipherpay", "error_code" => curl_errno($curl), "message" => curl_error($curl), "errorMessage" => "Unable to get response please try again later");
        // } else {
        //     $resp = self::response($response);
            
        // }
        $resp = self::response($response['response']);
       // dd($resp);
        //$finalresponse = self::finalResponse($resp);
        \Log::info('Cipher DecryptResponse:'.json_encode($resp).'== DecryptRequest:'.json_encode($reqData));
        return $resp;
    }

    public static function rsaEncrypt($data, $publicKey)
    {
        $publicKey = openssl_get_publickey($publicKey);
        openssl_public_encrypt($data, $encrypted, $publicKey);
        return base64_encode($encrypted);
    }

    public static function response($response)
    {
        //dd($response);
        $res = json_decode($response, TRUE);
        //dd($res);
        if(isset($res['responsecode'])&& $res['responsecode'] =="2"){
            return $res;
        }
        
        if(isset($res['responsecode'])&& $res['responsecode'] =="3"){
            return $res;
        }
        $responseData = !empty($res['returnData'])?$res['returnData']:$res['requestData'];
        $encrypted = base64_decode($responseData);
        $decrypted = openssl_decrypt($encrypted, 'aes-128-cbc', self::$aesKey, OPENSSL_RAW_DATA, self::$aesIv);
        $decrypted = json_decode($decrypted, true);
        return $decrypted;
    }
    public static function generateAesKey($salt)
    {
        $salt = hex2bin($salt);
        $passphrase = 'CipherPay API Payout';
        $iterationCount = 10000;
        $keySize = 128;
        $hashAlgorithm = 'sha1';
        $key = openssl_pbkdf2($passphrase, $salt, $keySize / 8, $iterationCount, $hashAlgorithm);
        self::$aesKey = $key;
        self::$aesIv = bin2hex($salt);
        return [$key, bin2hex($salt)];
    }
    public static function finalResponse($response)
    {
        $responseData = $response['returnData'];
        $encrypted = base64_decode($responseData);
        $decrypted = openssl_decrypt($encrypted, 'aes-128-cbc', self::$aesKey, OPENSSL_RAW_DATA, self::$aesIv);
        $decrypted = json_decode($decrypted, true);
        return $decrypted;
    }
    
    public static function getTransferStatus($transferId,$created_at)
    {
        
        
        $reqData = array("refid"=> $transferId ,"debitAccountNumber"=> "9123270369", "addedDate"=>$created_at);

        $request = array(
            "method" => "POST",
            "url" => "pay/check-status/",
            "parameter" => $reqData
        );
        
        $res = self::hit($request);
        $output = json_encode($res);
        $response['data'] = json_decode($output);
        //$response['data'] = json_decode($result['response']);

        return $response;
    }
}
