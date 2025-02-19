<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Utilities\Utils;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

/**
 * Class PostService.
 */
class PostService
{
    public function get(array $request)
    {
        $paginate = empty($request['paginate']) ? 5 : Utils::setPaginate($request['paginate']);
        $data = Post::with(['user', 'comments.user', 'comments.replies.user'])
        ->orderBy('created_at', 'desc')
            ->paginate($paginate);

        return [
            'data' => $data,
            'code' => 200
        ];
    }

    public function find($id)
    {
        $data = Post::with(['user', 'comments.user', 'comments.replies.user'])
            ->orderBy('created_at', 'desc')
            ->find($id);

        if (!$data) {
            return [
                'message' => 'Post not found',
                'code' => 404
            ];
        }

        return [
            'data' => $data,
            'code' => 200
        ];
    }

    public function create(array $request)
    {
        if (isset($request['image'])) {
            $imagePath = $request['image']->store('uploads', 'public');
            $request['image'] = $imagePath;
        }

        $data = Post::create(array_merge(
            $request,
            ['user_id' => Auth::id()]
        ));

        return [
            'data' => $data,
            'message' => 'Post created successfully',
            'code' => 201
        ];
    }

    public function update($id, array $request)
    {
        $data = Post::find($id);

        if (!$data) {
            return [
                'message' => 'Post not found',
                'code' => 404
            ];
        }

        if (isset($request['image'])) {
            $imagePath = $request['image']->store('uploads', 'public');
            $request['image'] = $imagePath;
        }

        $data->update($request);

        return [
            'data' => $data,
            'message' => 'Post updated successfully',
            'code' => 200
        ];
    }

    public function delete($id)
    {
        $data = Post::findOrFail($id);

        if ($data->user_id !== Auth::id()) {
            return [
                'message' => 'You are not authorized to delete this post',
                'code' => 403
            ];
        }

        if (!$data) {
            return [
                'message' => 'Post not found',
                'code' => 404
            ];
        }

        $data->delete();

        return [
            'message' => 'Post deleted successfully',
            'code' => 200
        ];
    }
}
