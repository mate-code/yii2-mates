<?php

namespace mate\yii\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class AlertBoxesAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mate-code/alert-boxes/dist/';

    public $js = [
        "jquery.alert-boxes.js"
    ];
    public $css = [
        "jquery.alert-boxes.css"
    ];
    public $depends = [
        JqueryAsset::class,
    ];
}