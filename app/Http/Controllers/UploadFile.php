<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUpload;
use Illuminate\Http\Request;

class UploadFile extends Controller
{
    public function __invoke(Request $request, ImageUpload $imageUpload)
    {
//        $errors = $this->validate($request, [
//            'avatar' => 'mimes:jpeg,bmp,png,gif|dimensions:min_width=200,min_height=200',
//        ], [
//            'avatar.mimes'      => '头像必须是 jpeg, bmp, png, gif 格式的图片',
//            'avatar.dimensions' => '图片的清晰度不够，宽和高需要 200px 以上',
//        ]);

        $paths = $imageUpload->upload($request->file('file'), $request->input('folder', 'public'));

        return response()->json([
            'errno' => 0,
            'data' => count($paths) > 1 ? $paths : $paths[0],
        ]);
    }
}
