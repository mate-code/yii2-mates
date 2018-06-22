<?php

namespace mate\yii\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

class TableSearchAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mate-code/yii2-mates/dist/';

    public $js = [
        'gii/table-search.js',
    ];
    public $depends = [
        DialogAsset::class,
        YiiAsset::class,
    ];

}