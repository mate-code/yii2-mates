<?php

namespace mate\yii\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class AnimatedLabelFormAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mate-code/animated-label-form/dist/';

    public $js = [
        "animated-label-form.js"
    ];
    public $css = [
        "animated-label-form.css"
    ];
    public $depends = [
        YiiAsset::class
    ];
}