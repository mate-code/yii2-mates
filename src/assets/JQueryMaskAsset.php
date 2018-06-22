<?php

namespace mate\yii\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class JQueryMaskAsset extends AssetBundle
{
    public $sourcePath = '@vendor/igorescobar/jquery-mask-plugin/dist/';

    public $js = [
        "jquery.mask.min.js"
    ];
    public $depends = [
        JqueryAsset::class,
    ];
}