<?php

namespace App\Http\Controllers\api;

use App\Helpers\helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function allPosts()
    {
        $posts = Post::orderByDesc('created_at')->get();
        return helper::responseData([
            'posts' => PostResource::collection($posts),
        ]);
    }
    public function getUserPosts(Request $request, $userId)
    {
        $user = User::where('id', $userId)->first();
        if ($user) {
            $posts = $user->posts;
            return helper::responseData(['posts' => PostResource::collection($posts)]);
        } else {
            return helper::responseError('User not found');
        }
    }

    public function createPost(Request $request)
    {
        $token = $request->header('Authorization');
        $user = User::where('token', $token)->first();
        $userImage = helper::uploadFile($request->file('image'), 'users/posts/');

        if ($request->content or $request->image) {
            $post = $user->posts()->create([
                'content' => $request->content,
                'image' => $userImage,
            ]);

            return helper::responseData([
                'post' => new PostResource($post),
            ], 'Post created successfully');
        } else {
            return helper::responseError("You should provide 'content', upload 'image', or both");
        }
    }


    public function reactToPost(Request $request, $postId)
    {
        $token = $request->header('Authorization');
        $user = User::where('token', $token)->first();
        $post = Post::where('id', $postId)->first();

        if ($post) {
            $existingLike = $post->post_reacts()->where('user_id', $user->id)->first();
            if ($existingLike) {
                $existingLike->delete();
                $message = 'Like removed successfully';
                $post->ser('total_likes');
            } else {
                $like = new Reaction(['user_id' => $user->id, 'post_id' => $postId]);
                $post->post_reacts()->save($like);
                $message = 'Post liked successfully';
                $post->increment('total_likes');
            }
            $reactedUserIds = $post->post_reacts()->pluck('user_id')->toArray();
            $totalLikes = $post->total_likes;
            return helper::responseData([
                'total_likes' => $totalLikes,
                'users' => $reactedUserIds,
            ], $message);
        } else {
            return helper::responseError('Post Not Found!');
        }
    }

    public function addCommentToPost(Request $request, $postId)
    {
        $token = $request->header('Authorization');
        $user = User::where('token', $token)->first();
        $post = Post::where('id', $postId)->first();
        if ($post) {
            if ($request->content) {
                $comment = new Comment(['user_id' => $user->id, 'post_id' => $postId, 'content' => $request->content]);
                $post->post_comments()->save($comment);
                $message = 'Comment added successfully';
                $post->increment('total_comments');
                $totalComments = $post->total_comments;
                return helper::responseData(['total_comments' => $totalComments, 'comment' => $request->content], $message);
            } else {
                return helper::responseError('Should add comment');
            }
        } else {
            return helper::responseError('Invalid post');
        }
    }

    public function delete_post(Request $request, $postId)
    {
        $token = $request->header('Authorization');
        $user = User::where('token', $token)->first();
        $post = Post::where('id', $postId)->first();
        if ($post) {
            if ($post->user_id == $user->id) {
                $post->delete();
                return helper::responseMsg('Post deleted Succesfully');
            } else {
                return  helper::responseError('You are not authorized to perform this action');
            }
        } else {
            return helper::responseError("This post does not exist");
        }
    }
}
