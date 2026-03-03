<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostEditorImageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Post::class);

        $file = $request->file('upload') ?? $request->file('file');

        $validator = Validator::make(
            ['upload' => $file],
            ['upload' => ['required', 'image', 'max:5120']]
        );

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'message' => $validator->errors()->first('upload'),
                ],
            ], 422);
        }

        $path = $file->store('posts/content-images', 'public');
        $url = Storage::disk('public')->url($path);

        return response()->json([
            'uploaded' => true,
            'url' => $url,
            'urls' => [
                'default' => $url,
            ],
        ]);
    }
}
