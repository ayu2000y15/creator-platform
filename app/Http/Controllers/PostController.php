<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Store a new post with video.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'video' => 'required|file|mimetypes:video/mp4|max:102400', // max:100MB
        ]);

        // S3の'videos'ディレクトリにファイルを保存
        $path = $request->file('video')->store('videos', 's3');

        // S3へのフルURLを取得する場合は以下のようにする
        // $url = Storage::disk('s3')->url($path);

        $post = $request->user()->posts()->create([
            'title' => $request->title,
            'video_path' => $path, // S3上のパスをDBに保存
        ]);

        return response()->json($post, 201);
    }
}
