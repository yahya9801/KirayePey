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

namespace App\Http\Controllers\Web\Post\CreateOrEdit\MultiSteps\Traits\Create;

use App\Helpers\Files\Upload;
use App\Models\CategoryField;
use App\Models\Package;
use App\Models\Post;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\VerifiedScope;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Payment as PaymentModel;

trait SubmitTrait
{
	/**
	 * Store all input data in database
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	protected $ourPrivateKey = "-----BEGIN RSA PRIVATE KEY-----
MIIJKwIBAAKCAgEAwaTThsT39BKT48gfyPSdz7Gc/l4CGz2RFpO7nRtRkPKrEbRp
EbeiTAsesoXRDujwR4e8GMvcw/NLP9IDqmy34U0F1tTy+AqGN0snfrzEMC5IgmOR
p6MzpfiwyikIxoNn6oskIT4U+wETlfJcAeDoPJlqDi6Dve4q+IACxPLE3B6jumEm
jpsJIgtaLIBu02IDKAQrogovn1DjVPWojbFk4RJw3F0EA1SNMfTxnfJAGuuvLbbo
pieWRUZQcJiMedYHWWMcAw69SCO3eCoYfdo0jMzr+eSYXj/ssSmGjDb+eeBueZw/
W47/2yDDyZ5F1tg924d73hWUk1PNy3SW0NWdHbCPPoZrAcPCYHaOdKmpX/WuRlbL
tzUSLjzjS8CgWMqyVuKw2W+bcinrtUZIiyvxTz3D4zOFRh04VcbrxtW+H6qB3208
1hu0JB/Ewc/jl66capwg3GQ8K80IapjRfDoRA2SqgcRoDCLEFv+CQXzKTmxyjy3G
03y/g/5PzwuAHi1nRwgcm3NsFvFeXB/gio+TKkGb8UO1e5pzxcHD3HkzAiK9CDGV
WdOu75ttQy/M2Xm3k4LeGuLkxtwfhZ6mzGdnoo1QP2XBbIInq04soDZmz39qUAZy
OZxG2q90j8xfzqclWKFrdhnLQa6Yvh8jv9znLI13Z1hmEi77hT0wNNzmtf8CAwEA
AQKCAgEArw1+/y0ebbFBqj/mo/amlvQcVULMqq18dKl2iC8DDIKjLS0ncpHMEOxY
gtA/zje7xx4oZ04bs7RDyVzkXXNMw0qqxavaPWreLCwvdv3UaS3d9KloKeB6N0bb
ItX3jeWK0leYgMirlMFDYGEo1bXom6egXVOlWtRsWhr25e4FEmxMXJeokHAM0I7s
pKAyD7th2RH0E9cbd+1apRoylmzQIjtWdY9MqmoL/iZNKhit5Og8g5n/NP1Cdjt7
Sl5NV3KpUCAtWYJAkG9hUyrS2yqgKMyzYkNJjqJBKWK9tBGe+LRvilY6XF2oZAJ5
m9ENl4wCV2tiCcD+gDTZLFyoXf9NuM6/1p/FpOW/rBRPzK5Jyox31QlYZH3OnBp0
E1Fp14WkMBIq31/JFyrVvXcWuG2Z8KrigX9DrASBCOUF5rHXsX1G95oylclEw9Xn
Sk7ZMuDEWrVYr2Ctx9dmyBRGWqexCjCiLvP3kb/f8qzWM6XLlDG+UrqhHfAeEmgv
crIpPigcT4u1+sLz1BcSJW4OQMwsolaMMvlre+1Tl1jHxunaNp8L4pssck4xE1VC
wFKnQDCJjsvwFA9qJhvaKtdkRE619qwVr2aHcQWwuLaHQcX/7ZbnC4bd2c7zZckQ
qLZXenOaDTzfm5a2l6HitEUaPtT1tTTzwVG3ZBevIhr5ZjeOtNECggEBAO5XZSkk
IMrk8qDysY6m251ygmty6GXDH0tjrYrcZc5gmROc8HUO8P5vpXEj812LUT/UNH6W
tEdtYYsLIXdYqSukE3MO1lJFFYSANk3s35w2Ncj+f2wW9ZUremRi1exWF+PwQPWO
Q5xmj3/1o/MDyxwGXEvUY6QiMW09Ecpf6BSniE8W99Z7cmXCOOdUQCzToaCq8Dg5
//UKCqM+H9qqe0oVQxXzSO3sou5b16+IA+NzIH3hcJSJ7C/ZjITFynkClenJoFdj
njCqEER7jksdvHgsG8HzhpPfGxSBFqSQOrQ/X0O6MWEGhKfBbr7xrU6XdbrSKoSv
Y44xJQx+nkshYxkCggEBAM/9p/EmfEKMl35d/0nfsKoZJbQkzgov1tjrbuoEVB/0
x6NCI3coMAD3StL8ZpjLWGXB02/ExEc3LVT1LSvmJFpMvov4g26p5ojdKKnNMezj
JawSAUDw9IiGiF/SfetDfKXh719/5NjsqDNniezMuyCsRON6MntnxQVytKysJZWe
5vtVsoIJ+yyKrJGO+GIuJVJeoGkn/G1KAvfGbavQzX+ePloLwsmhtlh6oEbHf7l5
7wXFBfLpgtSdBY+AYbqRJw6x2wSszhw/doc/jhMMh5oe+5j2AkyBA8SGtHu/CJcz
Cl+5WMdUYrKYu2ph469IbICejCaOzdjnD0lzegmr3NcCggEBAOo01yNmbRLzh7jA
w9n5/TTqoV4UvGnDI61hdddOXHxAUCwFvARLemWMMHbNbQgvLtgaHruGPAE0avXH
KkusxBAuWy5lFhYh7NMXakWIzvMiEhhAfdYpeYnr/4lF1hE3SKKkJDEoayf1YG3N
ZB4ZTC1t0qRxQLi5IPjHWgQT2mrxdfDCESiihfeYs3IeSDHEhL7tOsEFvY99Hpbr
JUIq5JwX6KRZPQEP18uCVKAYeDm5l+Qa9BYCsiUTEhImip/++nGRwA4fxgLsGrAw
+dK2lc1BCsW8GDKWunWbpDRxxj35SBO1k3BUh2aRndHkivj/vKCohT99WccQY+ez
RLlvRYECggEBAKT8HwXpc1QO1fUFdwYQjMDBJelZ8J6gCBsV1muHd3vJ3bqhMiTJ
GhsKDq6CTJgTZVGCiMf2G4QqPqlPur9B1cBTdCrvvMJBv379f84B2QKBxFZUCe6L
tE2/+dmYzQ2cAPpM2Ga2ur9gKqRsEzplxGJMhHC1c+n0DJMfxDXccAvZenwQJIOG
i1lvvyfPczPeSvil2zTO5SuENAp7um86OGhtDCS9g4wR3OUekJjUk3p7QKEhAnEH
ziH9VHXabqthjMvZRVTWBsDdjpYGUhrGK6KTxRw2uOgaxvRTrkMi7BuT/zyjd8rW
I1xsTu46LDPUjU0Rdzb2vE68KqInGb0mOQ0CggEBAJ/ATdnTelOVJFObvudgxBPf
Hw6zG5gTlx5k3tK7J7cwV1diQF+5sM0c3m6ejWH0vP8fUUBBaUZx77AtZMEuWCqt
7KlK9L68tByqNT7z+DW/ahZH7V+lNZFf8ELC4VZKivX/FRSuKbFymAAjnjMitqDg
KLSu/lSTLAOE3uQj4femNubRpoKhFrDAb/04ZIwbyz6aSGbG0NtuWTUHSKutP1vv
3syfzyxRziimB6z+/Az2VZ/wivKVmeDkYZRW/485I5tUyfEexWa3ejJVot0YG66Y
tqVEwiuxLvnJKk+iv7zS9aF2i4j//k9/gO+CfGdmWodkV/yLaEWQcf9pSHaY1CI=
-----END RSA PRIVATE KEY-----";


	private function storeInputDataInDatabase(Request $request)
	{
		// Get all saved input data

		if(isset($request->data) && $request->data){
			$encryptedData = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
			$encryptedData = str_replace("data=", "", $encryptedData);
			// $url_params = decryptData($encryptedData, $privateKey);
			$url_params = $this->decryptData($encryptedData, $this->ourPrivateKey);
			$splitToArray = explode("&",$url_params);
			$responseCode = str_replace("RESPONSE_CODE=","",$splitToArray[0]);
			$responseMsg = str_replace("RESPONSE_MESSAGE=","",$splitToArray[1]);
			$orderRefNumber = str_replace("ORDER_REF_NUMBER=","",$splitToArray[2]);
			$paymentType=str_replace("PAYMENT_TYPE=","",$splitToArray[3]);


			$paymentInfo = PaymentModel::where('transaction_id', $request->session()->get('REFERENCE_NUMBER'))
				->where('active', 0)->first();
			if($paymentInfo){
				$paymentInfo->response_code = $responseCode;
				$paymentInfo->response_message = $responseMsg . " Payment Type: $paymentType";
				$paymentInfo->update();
			}

			
			if($responseCode == 100){
				$msgToDisplay = $responseMsg;
			}
			else {
				$msgToDisplay = $responseMsg;
				$package = Package::find($request->session()->get('paymentInput')["package_id"]);
				$params = [
					'payment_method_id' => $request->session()->get('paymentInput')["payment_method_id"],
					'post_id'           => "",
					'package_id'        => $package->id,
					'amount'            => $package->price,
					'currency_code'     => $package->currency_code,
					'transaction_id'	=> $request->session()->get('REFERENCE_NUMBER'),
				];

				$paymentInfo = PaymentModel::where('transaction_id', $request->session()->get('REFERENCE_NUMBER'))
					->where('active', 0)->first();
				if($paymentInfo){
					$paymentInfo->package_id = data_get($params, 'package_id');
					$paymentInfo->payment_method_id = data_get($params, 'payment_method_id');
					$paymentInfo->update();
				}


				flash($msgToDisplay)->error();
				$previousUrl = $this->apiUri['previousUrl'];
				return redirect($previousUrl)->withInput();
			}
			
			
		}


		$postInput = (array)$request->session()->get('postInput');
		$picturesInput = (array)$request->session()->get('picturesInput');
		$paymentInput = (array)$request->session()->get('paymentInput');
		
		// Create the global input to send for database saving
		$inputArray = $postInput;
		if (isset($inputArray['category_id'], $inputArray['cf'])) {
			$fields = CategoryField::getFields($inputArray['category_id']);
			if ($fields->count() > 0) {
				foreach ($fields as $field) {
					if ($field->type == 'file') {
						if (isset($inputArray['cf'][$field->id]) && !empty($inputArray['cf'][$field->id])) {
							$inputArray['cf'][$field->id] = Upload::fromPath($inputArray['cf'][$field->id]);
						}
					}
				}
			}
		}
		
		$inputArray['pictures'] = [];
		if (!empty($picturesInput)) {
			foreach ($picturesInput as $key => $filePath) {
				if (!empty($filePath)) {
					$uploadedFile = Upload::fromPath($filePath);
					$inputArray['pictures'][] = $uploadedFile;
				}
			}
		}
		$inputArray = array_merge($inputArray, $paymentInput);
		
		request()->merge($inputArray);
		
		if (!empty($inputArray['pictures'])) {
			request()->files->set('pictures', $inputArray['pictures']);
		}
		
		// Call API endpoint
		$endpoint = '/posts';
		$data = makeApiRequest('post', $endpoint, request()->all(), [], true);
		
		// dd($data);
		
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : 'Unknown Error.';
		
		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			flash($message)->error();
			
			if (data_get($data, 'extra.previousUrl')) {
				return redirect(data_get($data, 'extra.previousUrl'))->withInput($request->except('pictures'));
			} else {
				return redirect()->back()->withInput($request->except('pictures'));
			}
		}
		
		// Notification Message
		if (data_get($data, 'success')) {
			session()->put('message', $message);
			
			// Save the post's ID in session
			$postId = data_get($data, 'result.id');
			if (!empty($postId)) {
				$request->session()->put('postId', $postId);
			}
			
			// $post = Post::find($postId);
			// if($post) {
			// 	$post->deleted_at = Carbon::now();
			// 	$post->save();
			// }
			// Clear Temporary Inputs & Files
			$this->clearTemporaryInput();
		} else {
			flash($message)->error();
			
			return redirect()->back()->withInput($request->except('pictures'));
		}
		
		// Get the next URL
		$nextUrl = url('posts/create/finish');
		
		if (!empty($paymentInput)) {
			// Check if the payment process has been triggered
			// NOTE: Payment bypass email or phone verification
			if ($request->filled('package_id') && $request->filled('payment_method_id')) {
				$postId = data_get($data, 'result.id', 0);
				$post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
					->where('id', $postId)->with([
						'latestPayment' => function ($builder) { $builder->with(['package']); },
					])->first();
				if (!empty($post)) {
					// Make Payment (If needed) - By not using REST API
					// Check if the selected Package has been already paid for this Post
					$alreadyPaidPackage = false;
					if (!empty($post->latestPayment)) {
						if ($post->latestPayment->package_id == $request->input('package_id')) {
							$alreadyPaidPackage = true;
						}
					}
					// Check if Payment is required
					$package = Package::find($request->input('package_id'));
					if (!empty($package)) {
						if ($package->price > 0 && $request->filled('payment_method_id') && !$alreadyPaidPackage) {
							// Get the next URL
							$nextUrl = $this->apiUri['nextUrl'];
							$previousUrl = $this->apiUri['previousUrl'];
							
							// this data is wrapped from HBl payment gateway need to decode it 
							
							// dd('Im here');
							$paymentData = $this->sendPayment($request, $post);
							
							// Check if a Payment has been sent
							if (data_get($paymentData, 'extra.payment')) {
								$paymentMessage = data_get($paymentData, 'message');
								if (data_get($paymentData, 'extra.payment.success')) {
									$paymentMessage  = $paymentMessage .' ' .$msgToDisplay;
									flash($paymentMessage)->success();
									
									if (data_get($paymentData, 'extra.nextUrl')) {
										$nextUrl = data_get($paymentData, 'extra.nextUrl');
									}
									
									return redirect($nextUrl);
								} else {
									flash($paymentMessage)->error();
									
									if (data_get($paymentData, 'extra.previousUrl')) {
										$previousUrl = data_get($paymentData, 'extra.previousUrl');
									}
									
									return redirect($previousUrl)->withInput();
								}
							}
						}
					}
				}
			}
		}
		
		// Get Listing Resource
		$post = data_get($data, 'result');
		
		if (
			data_get($data, 'extra.sendEmailVerification.emailVerificationSent')
			|| data_get($data, 'extra.sendPhoneVerification.phoneVerificationSent')
		) {
			session()->put('itemNextUrl', $nextUrl);
			
			if (data_get($data, 'extra.sendEmailVerification.emailVerificationSent')) {
				session()->put('emailVerificationSent', true);
				
				// Show the Re-send link
				$this->showReSendVerificationEmailLink($post, 'posts');
			}
			
			if (data_get($data, 'extra.sendPhoneVerification.phoneVerificationSent')) {
				session()->put('phoneVerificationSent', true);
				
				// Show the Re-send link
				$this->showReSendVerificationSmsLink($post, 'posts');
				
				// Go to Phone Number verification
				$nextUrl = url('posts/verify/phone/');
			}
		}
		
		// Mail Notification Message
		if (data_get($data, 'extra.mail.message')) {
			$mailMessage = data_get($data, 'extra.mail.message');
			if (data_get($data, 'extra.mail.success')) {
				flash($mailMessage)->success();
			} else {
				flash($mailMessage)->error();
			}
		}
		
		
		return redirect($nextUrl);
	}

	public function decryptData($data, $privatePEMKey)
	{
		$DECRYPT_BLOCK_SIZE = 512;
		$decrypted = '';
		
		$data = str_split(base64_decode($data), $DECRYPT_BLOCK_SIZE);
		foreach($data as $chunk)
		{
			$partial = '';
		
			$decryptionOK = openssl_private_decrypt($chunk, $partial, $privatePEMKey, OPENSSL_PKCS1_PADDING);
			
			if($decryptionOK === false)
			{
				$decrypted = '';
				return $decrypted;
			}
			$decrypted .= $partial;
		}
		
		return utf8_decode($decrypted);
	}
}
