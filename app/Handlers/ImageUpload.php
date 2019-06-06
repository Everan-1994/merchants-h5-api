<?php

namespace App\Handlers;

use Illuminate\Support\Facades\Storage;

class ImageUpload
{
    /**
     * @param $files
     * @param $folder
     *
     * @return array
     */
    public function upload($files, $folder = 'public')
    {
        $paths = [];

        if (is_array($files)) {
            // 多文件
            foreach ($files as $file) {
                $paths[] = $this->_upolad($file, $folder);
            }
        } else {
            $paths[] = $this->_upolad($files, $folder);
        }

        return $paths;
    }

    protected function _upolad($file, $folder)
    {
        // 构建存储的文件夹规则，如：articles/201810/10/
        // 文件夹切割能让查找效率更高。
        $folderName = "$folder/".date('Ym/d', time());

        // 将图片上传目标存储路径中
        $fileUrl = Storage::put($folderName, $file);

        // $result = explode('/', $fileUrl);

        return env('FILE_URL').'/'.$fileUrl;
    }
}
