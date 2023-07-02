<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PictureResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param $request
	 * @return array
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function toArray($request): array
	{
		$entity = [
			'id' => $this->id,
		];
		$columns = $this->getFillable();
		foreach ($columns as $column) {
			$entity[$column] = $this->{$column};
		}
		
		$embed = explode(',', request()->get('embed'));
		
		if (in_array('post', $embed)) {
			$entity['post'] = new PostResource($this->whenLoaded('post'));
		}
		
		$defaultPicture = config('larapen.core.picture.default');
		$defaultPictureUrl = imgUrl($defaultPicture);
		$entity['url'] = [
			'full'   => $this->filename_url ?? $defaultPictureUrl,
			'small'  => $this->filename_url_small ?? $defaultPictureUrl,
			'medium' => $this->filename_url_medium ?? $defaultPictureUrl,
			'big'    => $this->filename_url_big ?? $defaultPictureUrl,
		];
		
		return $entity;
	}
}
