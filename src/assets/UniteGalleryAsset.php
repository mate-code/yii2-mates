<?php

namespace mate\yii\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;

class UniteGalleryAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mate-code/yii2-mates/dist/unite-gallery/';

    public $css = [
        'css/unite-gallery.css'
    ];
    public $js = [
        'js/unitegallery.min.js',
        'js/auto-execute.js',
        'themes/tiles/ug-theme-tiles.js',
    ];

    public $depends = [
        JuiAsset::class,
    ];

}