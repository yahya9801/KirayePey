<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostFieldResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return array
	 */
	public function toArray($request): array
	{
		return [
			'id'    => data_get($this, 'id'),
			'name'  => data_get($this, 'name'),
			'type'  => data_get($this, 'type'),
			'value' => data_get($this, 'value'),
		];
	}
}
