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

namespace App\Helpers\Search\Traits\Filters;

trait PriceFilter
{
	protected function applyPriceFilter()
	{
		// The 'calculatedPrice' is a calculated column, so HAVING clause is required
		if (!isset($this->having)) {
			return;
		}
		//dd($this);
		$minPrice = null;
		$maxPrice = null;
		if (request()->filled('minPrice')) {
			$minPrice = request()->get('minPrice');
		}
		if (request()->filled('maxPrice')) {
			$maxPrice = request()->get('maxPrice');
		}
		
		$minPrice = (is_numeric($minPrice)) ? $minPrice : null;
		$maxPrice = (is_numeric($maxPrice)) ? $maxPrice : null;
		
		if (!is_null($minPrice) && !is_null($maxPrice)) {
			if ($maxPrice > $minPrice) {
				$this->having[] = 'calculatedPrice >= ' . $minPrice;
				$this->having[] = 'calculatedPrice <= ' . $maxPrice;
			}
		} else {
			if (!is_null($minPrice)) {
				$this->having[] = 'calculatedPrice >= ' . $minPrice;
			}
			if (!is_null($maxPrice)) {
				$this->having[] = 'calculatedPrice <= ' . $maxPrice;
			}
		}
	}
}
