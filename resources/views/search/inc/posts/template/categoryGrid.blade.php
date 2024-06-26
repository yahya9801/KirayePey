@php
	$posts ??= [];
	$totalPosts ??= 0;
@endphp
@if (!empty($posts) && $totalPosts > 0)
	@foreach($posts as $key => $post)
	@if(data_get($post, 'latestPayment.package_id') != 3) 
		<div class="item-list">
			@if (data_get($post, 'featured') == 1)
				@if (!empty(data_get($post, 'latestPayment.package')))
					@if (data_get($post, 'latestPayment.package.ribbon') != '')
						<div class="ribbon-horizontal {{ data_get($post, 'latestPayment.package.ribbon') }}">
							<span>{{ data_get($post, 'latestPayment.package.short_name') }}</span>
						</div>
					@endif
				@endif
			@endif
			
			<div class="row">
				<div class="col-sm-2 col-12 no-padding photobox">
					<div class="add-image">
						<span class="photo-count">
							<i class="fa fa-camera"></i> {{ data_get($post, 'count_pictures') }}
						</span>
						<a href="{{ \App\Helpers\UrlGen::post($post) }}">
							{!! imgTag(data_get($post, 'picture.filename'), 'medium', ['class' => 'lazyload thumbnail no-margin', 'alt' => data_get($post, 'title')]) !!}
						</a>
					</div>
				</div>
				
				<div class="col-sm-7 col-12 add-desc-box">
					<div class="items-details">
						<h5 class="add-title">
							<a href="{{ \App\Helpers\UrlGen::post($post) }}">{{ str(data_get($post, 'title'))->limit(70) }}</a>
						</h5>
						
						<span class="info-row">
							@if (config('settings.single.show_listing_types'))
								@if (!empty(data_get($post, 'postType')))
									<span class="add-type business-posts"
										  data-bs-toggle="tooltip"
										  data-bs-placement="bottom"
										  title="{{ data_get($post, 'postType.name') }}"
									>
										{{ strtoupper(mb_substr(data_get($post, 'postType.name'), 0, 1)) }}
									</span>&nbsp;
								@endif
							@endif
							@if (!config('settings.list.hide_dates'))
								<span class="date"{!! (config('lang.direction')=='rtl') ? ' dir="rtl"' : '' !!}>
									<i class="far fa-clock"></i> {!! data_get($post, 'created_at_formatted') !!}
								</span>
							@endif
							<span class="category"{!! (config('lang.direction')=='rtl') ? ' dir="rtl"' : '' !!}>
								<i class="bi bi-folder"></i>&nbsp;
								@if (!empty(data_get($post, 'category.parent')))
									<a href="{!! \App\Helpers\UrlGen::category(data_get($post, 'category.parent'), null, $city ?? null) !!}" class="info-link">
										{{ data_get($post, 'category.parent.name') }}
									</a>&nbsp;&raquo;&nbsp;
								@endif
								<a href="{!! \App\Helpers\UrlGen::category(data_get($post, 'category'), null, $city ?? null) !!}" class="info-link">
									{{ data_get($post, 'category.name') }}
								</a>
							</span>
							<span class="item-location"{!! (config('lang.direction')=='rtl') ? ' dir="rtl"' : '' !!}>
								<i class="bi bi-geo-alt"></i>&nbsp;
								<a href="{!! \App\Helpers\UrlGen::city(data_get($post, 'city'), null, $cat ?? null) !!}" class="info-link">
									{{ data_get($post, 'city.name') }}
								</a> {{ (!empty(data_get($post, 'distance'))) ? '- ' . round(data_get($post, 'distance'), 2) . getDistanceUnit() : '' }}
							</span>
						</span>
						
						@if (config('plugins.reviews.installed'))
							@if (view()->exists('reviews::ratings-list'))
								@include('reviews::ratings-list')
							@endif
						@endif
					</div>
				</div>
				
				<div class="col-sm-3 col-12 text-end price-box" style="white-space: nowrap;">
					<div class="row w-100">
						<div class="col-12 m-0 p-0 d-flex justify-content-end">
							<h2 class="item-price">
								{!! data_get($post, 'price_formatted'). data_get($post, 'pricing') !!}
							</h2>
						</div>
						@if (data_get($post, 'securityDepositAmount') > 0)
							<div class="col-12 m-0 p-0 d-flex justify-content-end">
								<h2 class="item-price">
									@if (data_get($post, 'securityDeposit') == 1)
										<small class="label bg-success" style="font-size: 12px;"> Security Deposit Required</small>
									@else
										<small class="label bg-primary" style="font-size: 12px;">Security Deposit Optional</small>
									@endif
								</h2>
							</div>
						@else
						<div class="col-12 m-0 p-0 d-flex justify-content-end">
							<h2 class="item-price">
								@if (data_get($post, 'securityDeposit') == 1)
									<small class="label bg-primary" style="font-size: 12px;">Security Deposit Optional</small>
								@else 
									<small class="label bg-danger" style="font-size: 12px;">Security Deposit Not Required</small>
								@endif
							</h2>
						</div>
											
						@endif
						<div class="col-12 m-0 p-0 d-flex justify-content-end">
							@if (!empty(data_get($post, 'latestPayment.package')))
								@if (data_get($post, 'latestPayment.package.has_badge') == 1)
									<a class="btn btn-danger btn-sm make-favorite">
										<i class="fa fa-certificate"></i> <span>{{ data_get($post, 'latestPayment.package.short_name') }}</span>
									</a>&nbsp;
								@endif
							@endif
							@if (!empty(data_get($post, 'savedByLoggedUser')))
								<a class="btn btn-success btn-sm make-favorite" id="{{ data_get($post, 'id') }}">
									<i class="fas fa-heart"></i> <span>{{ t('Saved') }}</span>
								</a>
							@else
								<a class="btn btn-default btn-sm make-favorite" id="{{ data_get($post, 'id') }}">
									<i class="fas fa-heart"></i> <span>{{ t('Save') }}</span>
								</a>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	@endif
	@endforeach
@else
	<div class="p-4 w-100">
		{{ t('no_result_refine_your_search') }}
	</div>
@endif

@section('after_scripts')
	@parent
	<script>
		{{-- Favorites Translation --}}
		var lang = {
			labelSavePostSave: "{!! t('Save listing') !!}",
			labelSavePostRemove: "{!! t('Remove favorite') !!}",
			loginToSavePost: "{!! t('Please log in to save the Listings') !!}",
			loginToSaveSearch: "{!! t('Please log in to save your search') !!}"
		};
	</script>
@endsection
