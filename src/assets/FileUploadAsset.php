<?php

namespace mate\yii\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;
use dosamigos\fileupload\FileUploadAsset as DosAmigosFileUploadAsset;

class FileUploadAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mate-code/yii2-mates/dist/file-upload/';

    public $js = [
        "file-upload.js",
    ];
    public $css = [
        "file-upload.css",
    ];
    public $depends = [
        YiiAsset::class,
        ScrollSliderAsset::class,
        DosAmigosFileUploadAsset::class,
    ];
}