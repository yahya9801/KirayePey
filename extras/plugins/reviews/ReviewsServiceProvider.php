<?php

namespace extras\plugins\reviews;

use App\Http\Controllers\Admin\Panel\Library\PanelRoutes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class ReviewsServiceProvider extends ServiceProvider
{
	/**
	 * Register any package services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('reviews', function ($app) {
			return new Reviews($app);
		});
	}
	
	/**
	 * Perform post-registration booting of services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Load plugin views
		$this->loadViewsFrom(realpath(__DIR__ . '/resources/views'), 'reviews');
		
		// Load plugin languages files
		$this->loadTranslationsFrom(realpath(__DIR__ . '/lang'), 'reviews');
		
		$this->registerAdminMiddleware($this->app->router);
		$this->setupRoutes($this->app->router);
	}
	
	/**
	 * Define the routes for the application.
	 *
	 * @param Router $router
	 */
	public function setupRoutes(Router $router)
	{
		// API
		$router->group([
			'middleware' => ['api'],
			'namespace'  => 'extras\plugins\reviews\app\Http\Controllers\Api',
			'prefix'     => 'api/plugins',
		], function ($router) {
			$router->pattern('postId', '[0-9]+');
			$router->pattern('ids', '[0-9,]+');
			Route::get('posts/{postId}/reviews', 'ReviewController@index')->name('reviews.index');
			Route::post('posts/{postId}/reviews', 'ReviewController@store')->name('reviews.store');
			Route::delete('posts/{postId}/reviews/{ids}', 'ReviewController@destroy')->name('reviews.destroy');
		});
		
		// Front
		$router->group([
			'middleware' => ['web'],
			'namespace'  => 'extras\plugins\reviews\app\Http\Controllers\Web',
		], function ($router) {
			$router->pattern('postId', '[0-9]+');
			$router->pattern('id', '[0-9]+');
			Route::post('posts/{postId}/reviews/create', 'ReviewController@store');
			Route::get('posts/{postId}/reviews/{id}/delete', 'ReviewController@destroy');
			Route::post('posts/{postId}/reviews/delete', 'ReviewController@destroy');
		});
		
		// Admin
		$router->group([
			'middleware' => ['admin', 'banned.user'],
			'namespace'  => 'extras\plugins\reviews\app\Http\Controllers\Admin',
			'prefix'     => config('larapen.admin.route', 'admin'),
		], function ($router) {
			PanelRoutes::resource('reviews', 'ReviewController');
		});
	}
	
	public function registerAdminMiddleware(Router $router)
	{
		Route::aliasMiddleware('admin', \App\Http\Middleware\Admin::class);
		Route::aliasMiddleware('banned.user', \App\Http\Middleware\BannedUser::class);
	}
}
