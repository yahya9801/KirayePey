{{--
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
--}}
@extends('layouts.master')

@section('search')
	@parent
    @includeFirst([config('larapen.core.customizedViewPath') . 'pages.inc.page-intro', 'pages.inc.page-intro'])
@endsection

@section('content')
	@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
	<div class="main-container inner-page">
		<div class="container">
			<div class="section-content">
				<div class="row">
                    
                    @if (empty(data_get($page, 'picture')))
                        <h1 class="text-center title-1" style="color: {!! data_get($page, 'name_color') !!};">
							<strong>About Us</strong>
						</h1>
                        <hr class="center-block small mt-0" style="background-color: {!! data_get($page, 'name_color') !!};">
                    @endif
                    
					<div class="col-md-12 page-content">
						<div class="inner-box relative">
							<div class="row">
								<div class="col-sm-12 page-content">
                                    @if (empty(data_get($page, 'picture')))
									    <h3 class="text-center" style="color: {!! data_get($page, 'title_color') !!};">{{ data_get($page, 'title') }}</h3>
                                    @endif
									<div class="text-content text-start from-wysiwyg">
										<p>KirayePey is an online rental classified ads platform through which lenders can advertise their products and services.
KirayePey was founded in 2023, with a plan to help the lenders with advertisement of rental products and/or services. There was no proper platform for such cause so KirayePey came into being.
										</p>

										<h4><b>Mission Statement</b></h4>
<p>Our mission is to help the lenders with their advertisements.</p>

<h4><b>Vision</b></h4>
<p>Our vision is to provide the best rental service.</p>

									</div>
								</div>
							</div>
						</div>
					</div>

				</div>

				@includeFirst([config('larapen.core.customizedViewPath') . 'layouts.inc.social.horizontal', 'layouts.inc.social.horizontal'])

			</div>
		</div>
	</div>
@endsection

@section('info')
@endsection
