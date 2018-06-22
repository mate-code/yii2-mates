<?php

namespace mate\yii\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

class ScrollSliderAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mate-code/yii2-mates/dist/scroll-slider/';
    public $js = [
    ];
    public $css = [
        "scroll-slider.css",
    ];
    public $depends = [
        JuiAsset::class,
        YiiAsset::class
    ];

}