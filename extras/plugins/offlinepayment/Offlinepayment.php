<?php

namespace extras\plugins\offlinepayment;

use App\Http\Resources\PaymentResource;
use App\Models\Permission;
use App\Models\Post;
use App\Models\User;
use App\Notifications\SubscriptionNotification;
use App\Notifications\SubscriptionPurchased;
use extras\plugins\offlinepayment\app\Notifications\PaymentNotification;
use extras\plugins\offlinepayment\app\Notifications\PaymentSent;
use extras\plugins\offlinepayment\app\Traits\InstallTrait;
use Illuminate\Http\Request;
use App\Helpers\Payment;
use App\Models\Package;
use App\Models\Payment as PaymentModel;
use Illuminate\Support\Facades\Notification;

class Offlinepayment extends Payment
{
	use InstallTrait;
	
	/**
	 * Send Payment
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\Models\Post|\App\Models\User $payable
	 * @param array $resData
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
	 */
	public static function sendPayment(Request $request, Post|User $payable, array $resData = [])
	{
		// Messages
		self::$msg['checkout']['success'] = trans('offlinepayment::messages.We have received your offline payment request.') . ' ' .
			trans('offlinepayment::messages.We will wait to receive your payment to process your request.');
		
		// Set the right URLs
		parent::setRightUrls($resData);
		
		// Get the Package
		$package = Package::find($request->input('package_id'));
		
		// Don't make a payment if 'price' = 0 or null
		if (empty($package) || $package->price <= 0) {
			$message = 'Package does not exist or its price is <= 0.';
			dd($payable);
			
			if (isFromApi()) {
				$resData['extra']['payment']['message'] = $message;
				$resData['extra']['payment']['result'] = null;
				$resData['extra']['previousUrl'] = parent::$uri['previousUrl'];
				$resData['extra']['nextUrl'] = parent::$uri['nextUrl'];
				
				return apiResponse()->json($resData);
			} else {
				flash($message)->error();
				
				return redirect()->to(parent::$uri['previousUrl'] . '?error=package')->withInput();
			}
		}
		// Don't make payment if selected Package is not compatible with payable (Post|User)
		/*if (!parent::isPayableCompatibleWithPackage($payable, $package)) {
			$message = 'The selected package is not compatible with the payable.';
			
			if (isFromApi()) {
				$resData['extra']['payment']['message'] = $message;
				$resData['extra']['payment']['result'] = null;
				$resData['extra']['previousUrl'] = parent::$uri['previousUrl'];
				$resData['extra']['nextUrl'] = parent::$uri['nextUrl'];
				
				return apiResponse()->json($resData);
			} else {
				flash($message)->error();
				
				return redirect()->to(parent::$uri['previousUrl'] . '?error=packageType')->withInput();
			}
		}
		*/
		
		$isPromoting = ($package->type == 'promotion');
		$isSubscripting = ($package->type == 'subscription');
		
		$payInfo = ' #' . $payable->id . ' - ' . $package->name;

		$params = [
			'payment_method_id' => $request->input('payment_method_id'),
			'post_id'           => $payable->id,
			'package_id'        => $package->id,
			'amount'            => $package->price,
			'currency_code'     => $package->currency_code,
			'transaction_id'	=> 123,
		];
		
		/*
		// API Parameters
		$params = parent::getLocalParameters($request, $payable, $package);
		$params['package']['description'] = trim($payInfo);
		if ($isPromoting) {
			$params['package']['description'] = trans('offlinepayment::messages.listing') . $payInfo;
		}
		if ($isSubscripting) {
			$params['package']['description'] = trans('offlinepayment::messages.user') . $payInfo;
		}
		*/
		// Save the Payment in database
		
		$resData = self::register($payable, $params, $resData);
		
		//dd($resData);
		
		if (isFromApi()) {
			
			return apiResponse()->json($resData);
			
		} else {
			
			if (data_get($resData, 'extra.payment.success')) {
				flash(data_get($resData, 'extra.payment.message'))->success();
			} else {
				flash(data_get($resData, 'extra.payment.message'))->error();
			}
			
			if (data_get($resData, 'success')) {
				session()->flash('message', data_get($resData, 'message'));
				
				return redirect()->to(self::$uri['nextUrl']);
			} else {
				// Maybe never called
				return redirect()->to(self::$uri['nextUrl'])->withErrors(['error' => data_get($resData, 'message')]);
			}
			
		}
	}
	
	/**
	 * Save the payment and Send payment confirmation email
	 *
	 * @param \App\Models\Post|\App\Models\User $payable
	 * @param array $params
	 * @param array $resData
	 * @return array
	 */
	public static function register($payable, $params, $resData = [])
	{
		// Don't save payment if selected Package is not compatible with payable (Post|User)
		/*if (!parent::isPayableCompatibleWithPackageArray($payable, $params)) {
			return $resData;
		}
		*/
		
		$request = request();
		
		// Get the payable full name with namespace
		$payableType = get_class($payable);
		$isPromoting = (str_ends_with($payableType, 'Post'));
		$isSubscripting = (str_ends_with($payableType, 'User'));
		
		// Update the payable (Post|User)
		if ($isPromoting) {
			$payable->reviewed_at = (!empty($payable->reviewed_at)) ? now() : null;
		}
		$package_id = data_get($params, 'package_id');
		
		$payable->featured = ($package_id > 1) ? 1 : 0;
		$payable->save();
		
		// Save the payment
		$paymentInfo = [
			'post_id'        => $payable->id,
			'payable_type'      => $payableType,
			'package_id'        => data_get($params, 'package_id'),
			'payment_method_id' => data_get($params, 'payment_method_id'),
			'transaction_id'    => data_get($params, 'transaction_id'),
			'amount'            => data_get($params, 'amount', 1),
			//'period_start'      => data_get($params, 'package.period_start', now()->startOfDay()),
			//'period_end'        => data_get($params, 'package.period_end'),
			'active'            => 1,
		];
		
		$payment = new PaymentModel($paymentInfo);
		$payment->save();
		
		$resData['extra']['payment']['success'] = true;
		$resData['extra']['payment']['message'] = self::$msg['checkout']['success'];
		$resData['extra']['payment']['result'] = (new PaymentResource($payment))->toArray($request);
		
		// SEND EMAILS
		
		// Send Payment Email Notifications
		if (config('settings.mail.payment_notification') == 1) {
			// Send Confirmation Email
			try {
				if ($isPromoting) {
					$payable->notify(new PaymentSent($payment, $payable));
				}
				if ($isSubscripting) {
					$payable->notify(new SubscriptionPurchased($payment, $payable));
				}
			} catch (\Throwable $e) {
				// Not Necessary To Notify
			}
			
			// Send to Admin the Payment Notification Email
			try {
				$admins = User::permission(Permission::getStaffPermissions())->get();
				if ($admins->count() > 0) {
					if ($isPromoting) {
						Notification::send($admins, new PaymentNotification($payment, $payable));
					}
					if ($isSubscripting) {
						Notification::send($admins, new SubscriptionNotification($payment, $payable));
					}
				}
			} catch (\Throwable $e) {
				// Not Necessary To Notify
			}
		}
		return $resData;
	}
}
