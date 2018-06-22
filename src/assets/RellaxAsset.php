<?php

namespace mate\yii\assets;

use yii\web\AssetBundle;

class RellaxAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mate-code/yii2-mates/dist/rellax/';

    public $js = [
        'rellax.min.js',
    ];
    public $depends = [
    ];
}