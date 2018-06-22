<?php


namespace mate\yii\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;
use yii\jui\JuiAsset;

class SortableUpdateAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mate-code/yii2-mates/dist/';

    public $css = [
    ];
    public $js = [
        'gii/sortable.js',
    ];
    public $depends = [
        AlertBoxesAsset::class,
        YiiAsset::class,
        JuiAsset::class
    ];
}