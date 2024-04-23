<?php

namespace extras\plugins\offlinepayment\app\Traits;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Post;

trait InstallTrait
{
	/**
	 * @return array
	 */
	public static function getOptions(): array
	{
		$options = [];
		
		$paymentMethod = PaymentMethod::active()->where('name', 'offlinepayment')->first();
		if (!empty($paymentMethod)) {
			$options[] = (object)[
				'name'     => mb_ucfirst(trans('admin.settings')),
				'url'      => admin_url('payment_methods/' . $paymentMethod->id . '/edit'),
				'btnClass' => 'btn-info',
			];
		}
		
		return $options;
	}
	
	/**
	 * @return bool
	 */
	public static function installed(): bool
	{
		$cacheExpiration = 86400; // Cache for 1 day (60 * 60 * 24)
		
		return cache()->remember('plugins.offlinepayment.installed', $cacheExpiration, function () {
			$paymentMethod = PaymentMethod::active()->where('name', 'offlinepayment')->first();
			if (empty($paymentMethod)) {
				return false;
			}
			
			return true;
		});
	}
	
	/**
	 * @return bool
	 */
	public static function install(): bool
	{
		// Remove the plugin entry
		self::uninstall();
		
		// Plugin data
		$data = [
			'id'                => 5,
			'name'              => 'offlinepayment',
			'display_name'      => 'Offline Payment',
			'description'       => null,
			'has_ccbox'         => 0,
			'is_compatible_api' => 1,
			'lft'               => 5,
			'rgt'               => 5,
			'depth'             => 1,
			'active'            => 1,
		];
		
		try {
			// Create plugin data
			$paymentMethod = PaymentMethod::create($data);
			if (empty($paymentMethod)) {
				return false;
			}
		} catch (\Throwable $e) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * @return bool
	 */
	public static function uninstall(): bool
	{
		try {
			cache()->forget('plugins.offlinepayment.installed');
		} catch (\Throwable $e) {
		}
		
		$uninstalled = false;
		
		$paymentMethod = PaymentMethod::where('name', 'offlinepayment')->first();
		if (!empty($paymentMethod)) {
			$deleted = $paymentMethod->delete();
			if ($deleted > 0) {
				$uninstalled = true;
			}
		}
		
		if ($uninstalled) {
			try {
				$payments = Payment::where('transaction_id', 'featured');
				if ($payments->count() > 0) {
					foreach ($payments->cursor() as $payment) {
						$payable = Post::find($payment->post_id);
						if (!empty($payable)) {
							$payable->featured = 0;
							$payable->save();
						}
						
						$payment->delete();
					}
				}
			} catch (\Throwable $e) {
			}
		}
		
		return $uninstalled;
	}
}
