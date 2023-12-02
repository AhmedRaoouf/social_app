<?php

namespace App\Http\Resources;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->token,
            'name' => $this->name,
            'email' => $this->email,
            'email_active' => $this->email_verified_at ? 'Yes' : 'No',
            'role' => Role::where('id', $this->role_id)->value('name'),
            'image' => $this->image ? asset('uploads')."/$this->image" : 'Not Found',
            'cover' => $this->cover ? asset('uploads')."/$this->cover" : 'Not Found',
            'birthday' => $this->birthday,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            
        ];
    }
}
