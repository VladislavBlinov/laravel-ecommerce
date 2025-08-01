<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $image = $this->image ?
            asset('storage/' . $this->image) :
            'https://dummyimage.com/212x282/000/fff';

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'active' => $this->active,
            'image' => $image,
            'rating' => $this->rating ?? 'Оценок нет',
        ];
    }
}
