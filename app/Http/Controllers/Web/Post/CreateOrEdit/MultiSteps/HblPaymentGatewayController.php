<?php
/**
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers\Web\Post\CreateOrEdit\MultiSteps;

use App\Helpers\Referrer;
use App\Http\Controllers\Api\Post\CreateOrEdit\Traits\RequiredInfoTrait;
use App\Http\Controllers\Api\Post\CreateOrEdit\Traits\PricingTrait;
use App\Http\Controllers\Web\Auth\Traits\VerificationTrait;
use App\Http\Controllers\Web\Post\CreateOrEdit\MultiSteps\Traits\WizardTrait;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Web\FrontController;
use Illuminate\Http\Request;
use Larapen\LaravelMetaTags\Facades\MetaTag;
use App\Http\Requests\PackageRequest;
use App\Models\City;
use App\Models\Package;
use App\Models\SubAdmin1;
use App\Models\User;

class HblPaymentGatewayController 
{

    protected $baseUrl = '/posts/create';
	protected $cfTmpUploadDir = 'temporary';
	protected $tmpUploadDir = 'temporary';


    protected $bankPublicPEMKey="-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA3dixunLMuvQ70wJkxOkg
61n5+GP/VwyIFPQVAvpkcwtC7irtpJIWybiCotBgeA2/BcIhg7TyesiUMjY0MQPC
nQFjjDfbCBpx0f01DXjVFdTAhxP1NenLJKrOy3wOOqIYMKWaVOttrfK+2MLkjufO
MCSvQcPu1zqnfaP0Y+GBx62IAv5FNqgPELzLRI6ezWKnWk/QX79HD0x68b24hX0j
OGKetVncW79lk0FhRd1adQTvaPgL70k44hAfYxHJHfA91RrGgKNmHy3UO7z7ZzWr
5wPQM88+TmHDefu+Xc09PY5j+K+aqzakyVN32scEvKqOkk9w6qZIyqettYiwMZCi
5aBLcb5d+1kYkXggCaB790vw838i0hzvE0Bo0hDH5jo9sxaSDk2Ex+zFdXv465QW
JokZWLywr+EII36neDa6bR8HrRvSjEkH9Psz6n2tpKWYuRdmgYLEU+5RF9ohuWjH
NoNXqgaa+QYn4RramfCDIyl1C3dC91JCKFQb15bQf11VC+couYov4BMoZHtf34Ij
ebcwlftfJCCSwvw9/vvLNbDa7T18Go1wEGI9LqhVBv+nMKFM7Jb4IFEJLEo8kAZa
L8Xjoj2YDlrqLMCH4Kn5Lace2dN3K4v7Uk3XqrL6KUCPvmKTvbQ6YkO3KcZ5BBcp
A0M2SW/GvSDG4g3R0K3TPHkCAwEAAQ==
-----END PUBLIC KEY-----";

    public function step(Request $request): int
	{
		if ($request->get('error') == 'paymentCancelled') {
			if ($request->session()->has('postId')) {
				$request->session()->forget('postId');
			}
		}
		
		$postId = $request->session()->get('postId');
		
		$step = 0;
		
		$data = $request->session()->get('postInput');
		if (isset($data) || !empty($postId)) {
			$step = 1;
		} else {
			return $step;
		}
		
		$data = $request->session()->get('picturesInput');
		if (isset($data) || !empty($postId)) {
			$step = 2;
		} else {
			return $step;
		}
		
		$data = $request->session()->get('paymentInput');
		if (isset($data) || !empty($postId)) {
			$step = 3;
		} else {
			return $step;
		}
		
		return $step;
	}

    public function rerouteToHblPaymentGateway(PackageRequest $request){
        if ($this->step($request) < 2) {
			if (config('settings.single.picture_mandatory')) {
				$backUrl = url($this->baseUrl . '/photos');
				$backUrl = qsUrl($backUrl, request()->only(['package']), null, false);
				
				return redirect($backUrl);
			}
		}
		$package_id = $request->package_id;
        $payment_method_id = $request->payment_method_id;

		$request->session()->put('paymentInput', array(
            'package_id' => $package_id,
            'payment_method_id' => $payment_method_id
        ));
       // dd($request->session());
        $stringData = $this->makePaymentGatewayData($request);
        $arrJson=json_decode($stringData,true);
//print_r($arrJson);exit;
        $arrJson=json_encode($this->recParamsEncryption($arrJson,$this));

        $url="https://digitalbankingportal.hbl.com/hostedcheckout/api/checkout";
//debug(callAPI("POST",$url,$cyb->encrypt_RSA($stringData)));
        $jsonCyberSourceResult=json_decode($this->callAPI("POST",$url,$arrJson),true);
        dd($jsonCyberSourceResult, $request->session()->get('REFERENCE_NUMBER'));
        if($jsonCyberSourceResult["IsSuccess"] && $jsonCyberSourceResult["ResponseMessage"]=="Success" && $jsonCyberSourceResult["ResponseCode"]==0){
            $sessionId=base64_encode($jsonCyberSourceResult["Data"]["SESSION_ID"]);
            $nextUrl = "https://digitalbankingportal.hbl.com/hostedcheckout/site/index.html#/checkout?data=$sessionId";
            return redirect($nextUrl);
        }
    }


    public function makePaymentGatewayData($request){
        $package_id = $request->session()->get('paymentInput')["package_id"];
        $package = Package::find($package_id);
        $randomNumber = $this->generateRandomNumber();
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        // dd($request->session());
        list($firstName, $lastName) = $this->breakUsername($user->name);
        
        $post_data = $request->session()->get('postInput');
        $country_code = $post_data['country_code'];
        $city_id = $post_data['city_id'];

        $city = City::find($city_id);
        $city_name = $city->name;
        $state = $city->subadmin1_code;
        $state_name = SubAdmin1::where('code', $state)->first()->name;
        $email = $post_data['email'];
        $phone_number = isset($post_data['phone']) && $post_data['phone'] ? $post_data['phone'] : ($user->phone ? $user->phone :"1231231234"); 
        // dd($request->session());
        $data = [
            "USER_ID" => "kirayepayadmin",
            "PASSWORD" => "5u4RYpa#Xj",
            "CLIENT_NAME" => "Kirayepay",
            "RETURN_URL" => "https://test.kirayepey.com/posts/submit/payment",
            "CANCEL_URL" => "https://test.kirayepey.com/posts/submit/payment",
            "CHANNEL" => "HBLPay_kirayepay_website",
            "TYPE_ID" => "0",
            "ORDER" => [
                "DISCOUNT_ON_TOTAL" => "0",
                "SUBTOTAL" => $package->price,
                "OrderSummaryDescription" => [
                    [
                        "ITEM_NAME" => $package->name,
                        "QUANTITY" => "1",
                        "UNIT_PRICE" => $package->price,
                        "OLD_PRICE" => "0",
                        "CATEGORY" => "Test Category",
                        "SUB_CATEGORY" => "Test Sub Category"
                    ]
                ]
            ],
            "SHIPPING_DETAIL" => [
                "NAME" => "DHL SERVICE",
                "ICON_PATH" => null,
                "DELIEVERY_DAYS" => "7",
                "SHIPPING_COST" => "0"
            ],
            "ADDITIONAL_DATA" => [
                "REFERENCE_NUMBER" => "kirayepey$randomNumber",
                "CUSTOMER_ID" => $user_id,
                "CURRENCY" => "PKR",
                "BILL_TO_FORENAME" => $firstName,
                "BILL_TO_SURNAME" => $lastName,
                "BILL_TO_EMAIL" => $email,
                "BILL_TO_PHONE" => $phone_number,
                "BILL_TO_ADDRESS_LINE" => $city_name,
                "BILL_TO_ADDRESS_CITY" => $city_name,
                "BILL_TO_ADDRESS_STATE" => $state_name,
                "BILL_TO_ADDRESS_COUNTRY" => $country_code,
                "BILL_TO_ADDRESS_POSTAL_CODE" => "54000",
                "MerchantFields" => [
                    "MDD1" => "WC",
                    "MDD2" => "YES",
                    "MDD3" => "YES",
                    "MDD4" => "Product Name",
                    "MDD5" => "No",
                    "MDD7" => "1",
                    "MDD20" => "NO"
                ]
            ]
        ];

        $stringData = json_encode($data);
        // dd($stringData);
        $request->session()->put('REFERENCE_NUMBER', "kirayepey$randomNumber");
        return $stringData;
    }

    function generateRandomNumber($length = 10) {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
    
        // Generate a random number
        $randomNumber = mt_rand($min, $max);
    
        // Check if the number is unique
        // You may need to adjust this logic based on your specific requirements
        // For example, if you're storing generated numbers in a database, you could check if the number already exists
        // Or you could maintain a list of generated numbers and check against that list
        // This example assumes uniqueness by checking against an array
        $generatedNumbers = [];
    
        while (in_array($randomNumber, $generatedNumbers)) {
            $randomNumber = mt_rand($min, $max);
        }
    
        // Add the generated number to the list
        $generatedNumbers[] = $randomNumber;
    
        return $randomNumber;
    }

    function breakUsername($username) {
        $names = explode(' ', $username, 2);
    
        $firstName = isset($names[0]) ? $names[0] : '';
        $lastName = isset($names[1]) ? $names[1] : '';
    
        return [$firstName, $lastName];
    }


    // bank functions started

    public function rsaEncryptCyb($plainData, $publicPEMKey=null){

        if(!$publicPEMKey)
            $publicPEMKey=$this->bankPublicPEMKey;
        $encryptionOk = openssl_public_encrypt ($plainData, $encryptedData,  trim($publicPEMKey), OPENSSL_PKCS1_PADDING);

        if($encryptionOk === false){
			$error = openssl_error_string();
			echo "Encryption failed: $error";
            return false;
        }
        return base64_encode($encryptedData);

        return false;

    }

    function recParamsEncryption($arrJson,$cyb){
        foreach($arrJson as $jsonIndex => $jsonValue){
            if( !is_array($jsonValue))
                if($jsonIndex!=="USER_ID")
                    $arrJson[$jsonIndex]=$cyb->rsaEncryptCyb($jsonValue);
                else
                    $arrJson[$jsonIndex]=$jsonValue;
            else{
                $arrJson[$jsonIndex]=$this->recParamsEncryption($jsonValue,$cyb);
            }
        }
        return $arrJson;
    }



    function callAPI($method, $url, $data){
        $is_live = 'no';
        $use_proxy = 'no';
        $curl = curl_init();
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                if ($data) $url = sprintf("%s?%s", $url, http_build_query($data));
        }// OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        //PROTOCOL_ERROR
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    
        if ($is_live === 'yes') {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        }// PROXY
        if ($use_proxy === 'yes') {
            //$proxy = ‘your proxy’;curl_setopt($curl, CURLOPT_PROXY, $proxy);
        }//EXECUTE
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
    
          $error_msg = curl_error($curl);
    
          }
    
          if (isset($error_msg)) {
    
            echo "Web Exception Raised::::::::::::::::".$error_msg;
    
          }
    
        // if(!$result){
        //     die("Connection Failure");
        // }
        curl_close($curl);
        return $result;
    }
	
}
