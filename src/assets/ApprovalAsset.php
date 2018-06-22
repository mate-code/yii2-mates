<?php

namespace mate\yii\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;

class ApprovalAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mate-code/yii2-mates/dist/';

    public $js = [
        'gii/approval.js',
    ];
    public $depends = [
        JuiAsset::class,
        BxSliderAsset::class
    ];
}