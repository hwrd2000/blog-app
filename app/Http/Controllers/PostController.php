<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Services\PostService;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function get(Request $request)
    {
        $data = $this->postService->get($request->all());
        return response()->json($data, $data['code']);
    }

    public function find($id)
    {
        $data = $this->postService->find($id);
        return response($data, $data['code']);
    }

    public function create(PostRequest $request)
    {
        $data = $this->postService->create($request->validated());
        return response($data, $data['code']);
    }

    public function update($id, PostRequest $request)
    {
        $data = $this->postService->update($id, $request->validated());
        return response($data, $data['code']);
    }

    public function delete($id)
    {
        $data = $this->postService->delete($id);
        return response($data, $data['code']);
    }
}
