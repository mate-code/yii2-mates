<?php

namespace mate\yii\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

class DialogAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mate-code/dialog/dist/';

    public $js = [
        "dialog.js"
    ];
    public $css = [
        "dialog.css"
    ];
    public $depends = [
        YiiAsset::class,
        JuiAsset::class,
    ];
}