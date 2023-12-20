<?php

namespace App\Http\Resources;

use App\Models\Comment;
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
        $allComments = Comment::where('post_id', $this->id)->get();
        return [
            'user_id' => $this->user->id,
            'post_id' => $this->id,
            'post_content' => $this->content,
            'post_image' => $this->image ? asset('uploads') . "/$this->image" : null,
            'total_likes' => $this->total_likes ?? 0,
            'total_comments' => $this->total_comments ?? 0,
            'comments' => count($allComments) != 0 ? CommentResource::collection($allComments) : null,
        ];
    }
}
