<?php

namespace mate\yii\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class BxSliderAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mate-code/yii2-mates/dist/bxslider/';

    public $css = [
        'jquery.bxslider.min.css',
    ];
    public $js = [
        'jquery.bxslider.min.js',
    ];
    public $depends = [
        YiiAsset::class,
    ];
}