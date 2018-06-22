<?php

namespace mate\yii\assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;

class SidebarAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mate-code/yii2-mates/dist/';

    public $css = [
        'sidebar/sidebar.css',
    ];
    public $js = [
        'sidebar/sidebar.js',
    ];
    public $depends = [
        BootstrapAsset::class,
    ];
}