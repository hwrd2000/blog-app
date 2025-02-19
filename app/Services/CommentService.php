<?php

namespace App\Services;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

/**
 * Class CommentService.
 */
class CommentService
{
    public function get()
    {
        $data = Comment::get();

        return [
            'data' => $data,
            'code' => 200
        ];
    }

    public function find($id)
    {
        $data = Comment::find($id);

        if (!$data) {
            return [
                'message' => 'Comment not found',
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
        $data = Comment::create(array_merge(
            $request,
            ['user_id' => Auth::id()]
        ));

        $data->load('user');
        
        return [
            'data' => $data,
            'message' => 'Comment created successfully',
            'code' => 201
        ];
    }

    public function update($id, array $request)
    {
        $data = Comment::findOrFail($id);

        if ($data->user_id !== Auth::id()) {
            return [
                'message' => 'You are not authorized to update this comment',
                'code' => 403
            ];
        }

        $data->update($request);

        return [
            'data' => $data,
            'message' => 'Comment updated successfully',
            'code' => 200
        ];
    }

    public function delete($id)
    {
        $data = Comment::findOrFail($id);
        
        if ($data->user_id !== Auth::id()) {
            return [
                'message' => 'You are not authorized to delete this comment',
                'code' => 403
            ];
        }

        if (!$data) {
            return [
                'message' => 'Comment not found',
                'code' => 404
            ];
        }

        $data->delete();

        return [
            'message' => 'Comment deleted successfully',
            'code' => 200
        ];
    }
}
