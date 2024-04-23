<?php

namespace extras\plugins\offlinepayment\app\Helpers;

use App\Helpers\Payment as PaymentHelper;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Post;
use App\Models\User;

class OpTools
{
	/**
	 * Ajax Checkbox Display
	 *
	 * @param $id
	 * @param $table
	 * @param $field
	 * @param null $fieldValue
	 * @return string
	 */
	public static function featuredCheckboxDisplay($id, $table, $field, $fieldValue = null): string
	{
		$lineId = $field . $id;
		$lineId = str_replace('.', '', $lineId); // fix JS bug (in admin layout)
		$data = 'data-table="' . $table . '"
			data-field="' . $field . '"
			data-line-id="' . $lineId . '"
			data-id="' . $id . '"
			data-value="' . ($fieldValue ?? 0) . '"';
		
		// Get the listing's latest current valid payment
		$latestValidPayment = self::getLatestCurrentValidPayment($id, $table);
		$validPaymentExists = (!empty($latestValidPayment));
		$isNotBlankPayment = ($validPaymentExists && $latestValidPayment->transaction_id != 'featured');
		
		// Decoration
		if (isset($fieldValue) && $fieldValue == 1 && $validPaymentExists) {
			$html = '<i id="' . $lineId . '" class="admin-single-icon fa fa-toggle-on" aria-hidden="true"></i>';
			if ($isNotBlankPayment) {
				return $html;
			}
		} else {
			$html = '<i id="' . $lineId . '" class="admin-single-icon fa fa-toggle-off" aria-hidden="true"></i>';
		}
		
		return '<a href="" class="ajax-request" ' . $data . '>' . $html . '</a>';
	}
	
	/**
	 * Get the payable (Post|User) latest current valid payment
	 *
	 * @param $payableId
	 * @param $table
	 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
	 */
	public static function getLatestCurrentValidPayment($payableId, $table)
	{
		$isPromoting = ($table == 'posts');
		$isSubscripting = ($table == 'users');
		
		$payment = Payment::query();
		// $payment->with('payable');
		
		if ($isPromoting) {
			$payment->whereHasMorph('payable', Post::class, function ($query) use ($payableId) {
				$query->where('id', $payableId);
			});
		}
		if ($isSubscripting) {
			$payment->whereHasMorph('payable', User::class, function ($query) use ($payableId) {
				$query->where('id', $payableId);
			});
		}
		
		$payment->valid()->active()->orderByDesc('id');
		
		return $payment->first();
	}
	
	/**
	 * Feature the Post
	 * This will create a blank payment for the Post
	 *
	 * @param \App\Models\Post|\App\Models\User $payable
	 * @return array
	 */
	public static function createFeatured(Post|User $payable): array
	{
		$result = [
			'success' => true,
			'message' => trans('offlinepayment::messages.inline_req_payment_created'),
		];
		
		// Get the payable full name with namespace
		$payableType = get_class($payable);
		
		$isPromoting = (str_ends_with($payableType, 'Post'));
		$isSubscripting = (str_ends_with($payableType, 'User'));
		
		if (!$isPromoting && !$isSubscripting) {
			$result['success'] = false;
			$result['message'] = t('payable_type_not_found');
			
			return $result;
		}
		
		// Get the cheapest package (orderBy ASC)
		$package = Package::query()
			->when($isPromoting, fn ($query) => $query->promotion())
			->when($isSubscripting, fn ($query) => $query->subscription())
			->orderBy('price')
			->first();
		if (empty($package)) {
			$result['success'] = false;
			$result['message'] = t('package_not_found');
			
			return $result;
		}
		
		// Get the OfflinePayment data
		$paymentMethod = PaymentMethod::where('name', 'offlinepayment')->first();
		if (empty($paymentMethod)) {
			$result['success'] = false;
			$result['message'] = t('payment_method_not_found');
			
			return $result;
		}
		
		$daysLeft = PaymentHelper::getDaysLeftBeforePayablePaymentsExpire($payable, $package->period_start);
		$periodStart = PaymentHelper::periodDate($package->period_start, $daysLeft);
		$periodEnd = PaymentHelper::periodDate($package->period_end, $daysLeft);
		
		try {
			// Save a blank payment
			$paymentInfo = [
				'payable_id'        => $payable->id,
				'payable_type'      => $payableType,
				'package_id'        => $package->id,
				'payment_method_id' => $paymentMethod->id,
				'transaction_id'    => 'featured',
				'amount'            => 0,
				'period_start'      => $periodStart->startOfDay(),
				'period_end'        => $periodEnd->endOfDay(),
				'active'            => 1,
			];
			$payment = new Payment($paymentInfo);
			$payment->save();
			
			// Update listing's 'reviewed' & 'featured' fields
			if ($isPromoting) {
				$payable->reviewed_at = now();
			}
			$payable->featured = 1;
			$payable->save();
		} catch (\Throwable $e) {
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}
		
		return $result;
	}
	
	/**
	 * Don't feature the Post
	 * This will remove the Post's blank payments
	 *
	 * @param \App\Models\Post|\App\Models\User $payable
	 * @return array
	 */
	public static function deleteFeatured(Post|User $payable): array
	{
		$result = [
			'success' => true,
			'message' => trans('offlinepayment::messages.inline_req_payment_deleted'),
		];
		
		try {
			// Get featured payments
			$payments = Payment::query()
				->whereMorphedTo('payable', $payable)
				->manuallyCreated()->get();
			
			if ($payments->count() > 0) {
				foreach ($payments as $payment) {
					$payment->delete();
				}
			}
			
			// Update listing's 'featured' fields
			$payable->featured = 0;
			$payable->save();
		} catch (\Throwable $e) {
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}
		
		return $result;
	}
}
