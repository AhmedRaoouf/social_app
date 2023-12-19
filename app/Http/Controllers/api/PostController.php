<?php

namespace App\Http\Controllers\api;

use App\Helpers\helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function getUserPosts(Request $request ,$userId)
    {
        $user = User::where('id', $userId)->first();
        if ($user) {
            $posts = $user->posts;
            return helper::responseData(PostResource::collection($posts));

        }else{
            return helper::responseError(404,'User not found');
        }
    }

    public function createPost(Request $request)
    {
        $token = $request->header('Authorization');
        $user = User::where('token', $token)->first();
        $userImage = helper::uploadFile($request->file('image'), 'users/photos/');
        $post = $user->posts()->create([
            'content' => $request->content,
            'image' => $userImage,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Post created successfully',
            'data' => new PostResource($post),
        ]);
    }

}
