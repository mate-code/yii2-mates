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
    public static function fromModel($modelClass, $fromField, $toField, $useModels = false)
    {
        if($useModels) {
            $models = self::getModelsCache($modelClass);
            return ArrayHelper::map(
                $models,
                $fromField,
                $toField
            );
        } else {
            $modelsData = self::getModelsDataCache($modelClass);
            return array_combine(
                array_column($modelsData, $fromField),
                array_column($modelsData, $toField)
            );
        }
    }

    /**
     * @param $modelClass
     * @return array|mixed|ActiveRecord[]
     */
    public static function getModelsCache($modelClass)
    {
        /** @var ActiveRecord|string $modelClass */
        $cacheKey = 'SelectData:Models:' . $modelClass::tableName();
        $modelsData = Yii::$app->cache->get($cacheKey);
        if ($modelsData === false) {
            $modelsData = $modelClass::find()->all();
            Yii::$app->cache->set($cacheKey, $modelsData);
        }
        return $modelsData;
    }

    /**
     * @param $modelClass
     * @return array|mixed|ActiveRecord[]
     */
    public static function getModelsDataCache($modelClass)
    {
        /** @var ActiveRecord|string $modelClass */
        $cacheKey = 'SelectData:ModelsData:' . $modelClass::tableName();
        $modelsData = Yii::$app->cache->get($cacheKey);
        if ($modelsData === false) {
            $modelsData = $modelClass::find()->asArray()->all();
            Yii::$app->cache->set($cacheKey, $modelsData);
        }
        return $modelsData;
    }

    /**
     * @param $modelClass
     */
    public static function refreshModelsDataCache($modelClass)
    {
        /** @var ActiveRecord|string $modelClass */
        Yii::$app->cache->delete('SelectData:Models:' . $modelClass::tableName());
        Yii::$app->cache->delete('SelectData:ModelsData:' . $modelClass::tableName());
    }

}