<?php

namespace mate\yii\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class CrudAutocompleteAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mate-code/yii2-mates/dist/';

    public $js = [
        'gii/crud-autocomplete.js',
    ];
    public $depends = [
        YiiAsset::class,
    ];

}