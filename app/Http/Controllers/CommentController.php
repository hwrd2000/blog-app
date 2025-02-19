<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Services\CommentService;

class CommentController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function get(Request $request)
    {
        $data = $this->commentService->get($request->all());
        return response()->json($data, $data['code']);
    }

    public function find($id)
    {
        $data = $this->commentService->find($id);
        return response($data, $data['code']);
    }

    public function create(CommentRequest $request)
    {
        $data = $this->commentService->create($request->validated());
        return response($data, $data['code']);
    }

    public function update($id, CommentRequest $request)
    {
        $data = $this->commentService->update($id, $request->validated());
        return response($data, $data['code']);
    }

    public function delete($id)
    {
        $data = $this->commentService->delete($id);
        return response($data, $data['code']);
    }
}
