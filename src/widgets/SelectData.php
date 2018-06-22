<?php


namespace mate\yii\widgets;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class SelectData
{

    /**
     * Creates an array from all database entries of $modelClass
     * $fromField being the attribute used as keys and $toField the attribute used as values
     * Pass $useModels=true if the attribute is not a database field but a getter. Please note that
     * the entire model objects will be cached using this option
     * @param $modelClass
     * @param $fromField
     * @param $toField
     * @param bool $useModels
     * @return array
     */
    public static function fromModel($modelClass, $fromField = null, $toField = null, $useModels = false)
    {
        return Yii::$app->selectData->fromModel($modelClass, $fromField, $toField, $useModels);
    }

}