@if (isset($packages, $paymentMethods) && $packages->count() > 0 && $paymentMethods->count() > 0)
	<div class="well pb-0">
		<h3><i class="fas fa-certificate icon-color-1"></i> {{ t('Premium Listing') }} </h3>
		<p>
			{{ t('premium_plans_hint') }}
		</p>
		<?php $packageIdError = (isset($errors) && $errors->has('package_id')) ? ' is-invalid' : ''; ?>
		<div class="row mb-3 mb-0">
			<table id="packagesTable" class="table table-hover checkboxtable mb-0">
				@foreach ($packages as $package)
					@php
						$packageStatus = '';
						$badge = '';
						if (isset($currentPackageId, $currentPackagePrice, $currentPaymentIsActive)) {
							// Prevent Package's Downgrading
							if ($currentPackagePrice > $package->price) {
								$packageStatus = ' disabled';
								$badge = ' <span class="badge bg-danger">' . t('Not available') . '</span>';
							} elseif ($currentPackagePrice == $package->price) {
								$badge = '';
							} else {
								if ($package->price > 0) {
									$badge = ' <span class="badge bg-success">' . t('Upgrade') . '</span>';
								}
							}
							if ($currentPackageId == $package->id) {
								$badge = ' <span class="badge bg-secondary">' . t('Current') . '</span>';
								if ($currentPaymentIsActive == 0) {
									$badge .= ' <span class="badge bg-warning">' . t('Payment pending') . '</span>';
								}
							}
						} else {
							if ($package->price > 0) {
								$badge = ' <span class="badge bg-success">' . t('Upgrade') . '</span>';
							}
						}
					@endphp
					<tr>
						<td class="text-start align-middle p-3">
							<div class="form-check">
								<input class="form-check-input package-selection{{ $packageIdError }}"
									   type="radio"
									   name="package_id"
									   id="packageId-{{ $package->id }}"
									   value="{{ $package->id }}"
									   data-name="{{ $package->name }}"
									   data-currencysymbol="{{ $package->currency->symbol }}"
									   data-currencyinleft="{{ $package->currency->in_left }}"
										{{ (old('package_id', $currentPackageId ?? 0)==$package->id) ? ' checked' : (($package->price==0) ? ' checked' : '') }} {{
										$packageStatus }}
								>
								<label class="form-check-label mb-0{{ $packageIdError }}">
									<strong class=""
											data-bs-placement="right"
											data-bs-toggle="tooltip"
											title="{!! $package->description_string !!}"
									>{!! $package->name . $badge !!} </strong>
								</label>
							</div>
						</td>
						<td class="text-end align-middle p-3">
							<p id="price-{{ $package->id }}" class="mb-0">
								@if ($package->currency->in_left == 1)
									<span class="price-currency">{!! $package->currency->symbol !!}</span>
								@endif
								<span class="price-int">{{ $package->price }}</span>
								@if ($package->currency->in_left == 0)
									<span class="price-currency">{!! $package->currency->symbol !!}</span>
								@endif
							</p>
						</td>
					</tr>
				@endforeach
				
				<tr>
					{{-- <td class="text-start align-middle p-3">
						@includeFirst([
                            config('larapen.core.customizedViewPath') . 'post.createOrEdit.inc.payment-methods',
                            'post.createOrEdit.inc.payment-methods'
                        ])
					</td> --}}

					<input name="payment_method_id" id="paymentMethodId" value="5" hidden/>

					{{-- <select class="form-control selecter{{ $paymentMethodIdError }}" name="payment_method_id" id="paymentMethodId">
						@foreach ($paymentMethods as $paymentMethod)
							@if (view()->exists('payment::' . $paymentMethod->name))
								<option value="{{ $paymentMethod->id }}"
										data-name="{{ $paymentMethod->name }}"
										{{ (old('payment_method_id', $currentPaymentMethodId)==$paymentMethod->id) ? 'selected="selected"' : '' }}
								>
									@if ($paymentMethod->name == 'offlinepayment')
										{{ trans('offlinepayment::messages.Offline Payment') }}
									@else
										{{ $paymentMethod->display_name }}
									@endif
								</option>
							@endif
						@endforeach
					</select> --}}
					<td class="text-end align-middle p-3">
						<p class="mb-0">
							<strong>
								{{ t('Payable Amount') }}:
								<span class="price-currency amount-currency currency-in-left" style="display: none;"></span>
								<span class="payable-amount">0</span>
								<span class="price-currency amount-currency currency-in-right" style="display: none;"></span>
							</strong>
						</p>
					</td>
				</tr>
			    @includeFirst([
        config('larapen.core.customizedViewPath') . 'post.createOrEdit.inc.payment-methods.plugins',
        'post.createOrEdit.inc.payment-methods.plugins'
    ])
			</table>
		</div>
	</div>
	
	

@endif