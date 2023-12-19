<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user->id,
            'post_id' => $this->id,
            'post_content' => $this->content,
            'post_image' => $this->image ? asset('uploads') . "/$this->image" : null,
        ];
    }
}
