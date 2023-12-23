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
        $protocol = $request->isSecure() ? 'https' : 'http';


        return [
            'token' => $this->token,
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_active' => $this->email_verified_at ? 'Yes' : 'No',
            'role' => Role::where('id', $this->role_id)->value('name'),
            'image' => $this->image ? rtrim(env('APP_URL'), '/') . "uploads/$this->image" : null,
            'cover' => $this->cover ? rtrim(env('APP_URL'), '/') . "uploads/$this->cover" : null,
            'birthday' => $this->birthday,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
        ];
    }
}
