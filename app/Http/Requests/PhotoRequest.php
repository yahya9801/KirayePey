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

namespace App\Http\Requests;

class PhotoRequest extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function rules()
	{
		$rules = [];
		
		// Require 'pictures' if exists
		if ($this->file('pictures')) {
			$files = $this->file('pictures');
			foreach ($files as $key => $file) {
				if (!empty($file)) {
					$rules['pictures.' . $key] = [
						'image',
						'mimes:' . getUploadFileTypes('image'),
						'min:' . (int)config('settings.upload.min_image_size', 0),
						'max:' . (int)config('settings.upload.max_image_size', 1000),
					];
				}
			}
		}
		
		// Apply this rules only for the 'Multi Steps Form' Web based requests
		if (!isFromApi()) {
			// Check if this request comes from Listing creation form
			// i.e. Not from Listing updating form, where 'postInput' & 'picturesInput' sessions are not available
			if (session()->has('postInput')) {
				// If no picture is uploaded & If picture is mandatory,
				// Don't allow user to go to the next page.
				$picturesInput = (array)session()->get('picturesInput');
					

				if (empty($picturesInput)) {
					if (config('settings.single.picture_mandatory')) {
						$rules['pictures'] = ['required'];
					}
				}
			}
		}
		return $rules;
	}
	
	/**
	 * Get custom attributes for validator errors.
	 *
	 * @return array
	 */
	public function attributes()
	{
		$attributes = [];
		
		if ($this->file('pictures')) {
			$files = $this->file('pictures');
			foreach ($files as $key => $file) {
				$attributes['pictures.' . $key] = t('picture X', ['key' => ($key + 1)]);
			}
		}
		
		return $attributes;
	}
	
	/**
	 * Get custom messages for validator errors.
	 *
	 * @return array
	 */
	public function messages()
	{
		$messages = [];
		
		if ($this->file('pictures')) {
			
			$files = $this->file('pictures');
			
			foreach ($files as $key => $file) {
				// uploaded
				$maxSize = (int)config('settings.upload.max_image_size', 1000); // In KB
				$maxSize = $maxSize * 1024; // Convert KB to Bytes
				 //dd(@ini_get('post_max_size'));

				$msg = t('large_file_uploaded_error', [
					'field'   => t('picture X', ['key' => ($key + 1)]),
					'maxSize' => readableBytes($maxSize),
				]);
				
				$uploadMaxFilesizeStr = @ini_get('upload_max_filesize');
				$postMaxSizeStr = @ini_get('post_max_size');
				if (!empty($uploadMaxFilesizeStr) && !empty($postMaxSizeStr)) {
					$uploadMaxFilesize = (int)strToDigit($uploadMaxFilesizeStr);
					$postMaxSize = (int)strToDigit($postMaxSizeStr);
					
					$serverMaxSize = min($uploadMaxFilesize, $postMaxSize);
					$serverMaxSize = $serverMaxSize * 1024 * 1024; // Convert MB to KB to Bytes
					if ($serverMaxSize < $maxSize) {
						$msg = t('large_file_uploaded_error_system', [
							'field'   => t('picture X', ['key' => ($key + 1)]),
							'maxSize' => readableBytes($serverMaxSize),
						]);
					}
				}
				
				$messages['pictures.' . $key . '.uploaded'] = $msg;
			}
		}
		
		if (config('settings.single.picture_mandatory')) {
			$messages['pictures.required'] = t('pictures_mandatory_text');
		}
		return $messages;
	}
}
