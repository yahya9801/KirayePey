<?php
$isPriceFilterCanBeDisplayed = ((isset($cat) && !empty($cat)) && !in_array(data_get($cat, 'type'), ['not-salable']));
//dd($cat);
// Clear Filter Button
$clearFilterBtn = \App\Helpers\UrlGen::getPriceFilterClearLink($cat ?? null, $city ?? null);
?>
	{{-- Price --}}
	<div class="block-title has-arrow sidebar-header">
		<h5>
			<span class="fw-bold">
				{{ (!in_array(data_get($cat, 'type'), ['job-offer', 'job-search'])) ? 'Area' : t('salary_range') }}
			</span> {!! $clearFilterBtn !!}
		</h5>
	</div>
	<div class="block-content list-filter number-range-slider-wrapper">
		<form role="form" class="form-inline" action="{{ request()->url() }}" method="GET">
			@foreach(request()->except(['page', 'minArea', 'maxArea', '_token']) as $key => $value)
				@if (is_array($value))
					@foreach($value as $k => $v)
						@if (is_array($v))
							@foreach($v as $ik => $iv)
								@continue(is_array($iv))
								<input type="hidden" name="{{ $key.'['.$k.']['.$ik.']' }}" value="{{ $iv }}">
							@endforeach
						@else
							<input type="hidden" name="{{ $key.'['.$k.']' }}" value="{{ $v }}">
						@endif
					@endforeach
				@else
					<input type="hidden" name="{{ $key }}" value="{{ $value }}">
				@endif
			@endforeach
			<div class="row px-1 gx-1 gy-1">
				<div class="col-12 mb-3 number-range-slider" id="areaRangeSlider"></div>
				<div class="col-lg-4 col-md-12 col-sm-12">
					<input type="number" min="0" id="minArea" name="minArea" class="form-control" placeholder="{{ t('Min') }}" value="{{ request()->get('minArea') }}">
				</div>
				<div class="col-lg-4 col-md-12 col-sm-12">
					<input type="number" min="0" id="maxArea" name="maxArea" class="form-control" placeholder="{{ t('Max') }}" value="{{ request()->get('maxArea') }}">
				</div>
				<div class="col-lg-4 col-md-12 col-sm-12">
					<button class="btn btn-default btn-block" type="submit">{{ t('go') }}</button>
				</div>
			</div>
		</form>
	</div>
	<div style="clear:both"></div>

@section('after_scripts')
	@parent
	
	@if ($isPriceFilterCanBeDisplayed)
		<link href="{{ url('assets/plugins/noUiSlider/15.5.0/nouislider.css') }}" rel="stylesheet">
		<style>
			/* Hide Arrows From Input Number */
			/* Chrome, Safari, Edge, Opera */
			.number-range-slider-wrapper input::-webkit-outer-spin-button,
			.number-range-slider-wrapper input::-webkit-inner-spin-button {
				-webkit-appearance: none;
				margin: 0;
			}
			/* Firefox */
			.number-range-slider-wrapper input[type=number] {
				-moz-appearance: textfield;
			}
		</style>
	@endif
@endsection
@section('after_scripts')
	@parent
	@if ($isPriceFilterCanBeDisplayed)
		<script src="{{ url('assets/plugins/noUiSlider/15.5.0/nouislider.js') }}"></script>
		@php
			$minArea = (int)config('settings.list.min_price', 0);
			$maxArea = (int)config('settings.list.max_price', 10000);
			$areaSliderStep = (int)config('settings.list.area_slider_step', 50);
			
			$startArea = (int)request()->get('minArea', $minArea);
			$endArea = (int)request()->get('maxArea', $maxArea);
		@endphp
		<script>
			$(document).ready(function ()
			{
				let minArea = {{ $minArea }};
				let maxArea = {{ $maxArea }};
				let areaSliderStep = {{ $areaSliderStep }};
				
				{{-- Price --}}
				let startArea = {{ $startArea }};
				let endArea = {{ $endArea }};
				
				let areaRangeSliderEl = document.getElementById('areaRangeSlider');
				noUiSlider.create(areaRangeSliderEl, {
					connect: true,
					start: [startArea, endArea],
					step: areaSliderStep,
					keyboardSupport: true,     			 /* Default true */
					keyboardDefaultStep: 5,    			 /* Default 10 */
					keyboardPageMultiplier: 5, 			 /* Default 5 */
					keyboardMultiplier: areaSliderStep, /* Default 1 */
					range: {
						'min': minArea,
						'max': maxArea
					}
				});
				
				let minAreaEl = document.getElementById('minArea');
				let maxAreaEl = document.getElementById('maxArea');
				
				areaRangeSliderEl.noUiSlider.on('update', function (values, handle) {
					let value = values[handle];
					
					if (handle) {
						maxAreaEl.value = Math.round(value);
					} else {
						minAreaEl.value = Math.round(value);
					}
				});
				minAreaEl.addEventListener('change', function () {
					areaRangeSliderEl.noUiSlider.set([this.value, null]);
				});
				maxAreaEl.addEventListener('change', function () {
					if (this.value <= maxArea) {
						areaRangeSliderEl.noUiSlider.set([null, this.value]);
					}
				});
			});
		</script>
	@endif
@endsection