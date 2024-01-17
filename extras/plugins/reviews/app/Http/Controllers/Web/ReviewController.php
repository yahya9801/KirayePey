<?php

namespace extras\plugins\reviews\app\Http\Controllers\Web;

use App\Helpers\UrlGen;
use App\Http\Controllers\Web\FrontController;;
use extras\plugins\reviews\app\Http\Requests\ReviewRequest;

class ReviewController extends FrontController
{
	/**
	 * @param $postId
	 * @param \extras\plugins\reviews\app\Http\Requests\ReviewRequest $request
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function store($postId, ReviewRequest $request)
	{
		// Call API endpoint
		$endpoint = '/plugins/posts/' . $postId . '/reviews';
		$data = makeApiRequest('post', $endpoint, $request->all());
		
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : 'Unknown Error.';
		
		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			return back()->withErrors(['error' => $message])->withInput();
		}
		
		// Notification Message
		if (data_get($data, 'success')) {
			flash($message)->success();
			session()->flash('review_posted');
		} else {
			flash($message)->error();
		}
		
		// Get the Listing
		$post = data_get($data, 'extra.post') ?? [];
		
		$nextUrl = !empty($post) ? UrlGen::postUri($post) . '#item-reviews' : url('/');
		
		// Redirect
		return redirect($nextUrl);
	}
	
	/**
	 * @param $postId
	 * @param $id
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function destroy($postId, $id)
	{
		// Get Entries ID
		$ids = [];
		if (request()->filled('entries')) {
			$ids = request()->input('entries');
		} else {
			if ((is_numeric($id) && $id > 0) || (is_string($id) && !empty($id))) {
				$ids[] = $id;
			}
		}
		$ids = implode(',', $ids);
		
		// Call API endpoint
		$endpoint = '/plugins/posts/' . $postId . '/reviews/' . $ids;
		$data = makeApiRequest('delete', $endpoint, request()->all());
		
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : 'Unknown Error.';
		
		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			return back()->withErrors(['error' => $message])->withInput();
		}
		
		// Notification Message
		if (data_get($data, 'success')) {
			flash($message)->success();
			session()->flash('review_removed');
		} else {
			flash($message)->error();
		}
		
		// Get the Listing
		$post = data_get($data, 'extra.post') ?? [];
		
		$nextUrl = !empty($post) ? UrlGen::postUri($post) . '#item-reviews' : url('/');
		
		// Redirect
		return redirect($nextUrl);
	}
}
