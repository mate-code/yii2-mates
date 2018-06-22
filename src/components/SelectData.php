<?php


namespace mate\yii\components;

use Yii;
use yii\base\Component;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class SelectData extends Component
{

    const CACHE_KEY_CACHED_MODELS = 'SelectData:cachedModels';
    const CACHE_KEY_MODELS = 'SelectData:Models:';
    const CACHE_KEY_MODELS_DATA = 'SelectData:ModelsData:';

    public $maps = [];
    protected $cachedModels = [];

    public function init()
    {
        $this->cachedModels = Yii::$app->cache->get('SelectData:cachedModels');
        $this->cachedModels = $this->cachedModels === false ? [] : $this->cachedModels;

        $refreshOnEvents = [
            ActiveRecord::EVENT_AFTER_UPDATE,
            ActiveRecord::EVENT_AFTER_INSERT,
            ActiveRecord::EVENT_AFTER_DELETE,
        ];

        foreach ($this->cachedModels as $cachedModelClass => $lastUpdate) {
            foreach ($refreshOnEvents as $eventName) {
                Event::on($cachedModelClass, $eventName, [$this, 'eventRefreshModelCache'], [
                    'modelClass' => $cachedModelClass
                ]);
            }
        }
    }

    /**
     * @param Event $event
     */
    public function eventRefreshModelCache(Event $event)
    {
        $modelClass = $event->data['modelClass'];
        Yii::$app->cache->delete(self::CACHE_KEY_MODELS . $modelClass);
        Yii::$app->cache->delete(self::CACHE_KEY_MODELS_DATA . $modelClass);
        unset($this->cachedModels[$modelClass]);
        Yii::$app->cache->set(self::CACHE_KEY_CACHED_MODELS, $this->cachedModels);
    }

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
    public function fromModel($modelClass, $fromField = null, $toField = null, $useModels = false)
    {
        $fromField = $fromField ? $fromField : $this->getMap($modelClass)['from'];
        $toField = $toField ? $toField : $this->getMap($modelClass)['to'];

        if ($useModels) {
            $models = $this->getCachedModelData($modelClass, self::CACHE_KEY_MODELS, function ($modelClass) {
                /** @var ActiveRecord $modelClass  */
                return $modelClass::find()->asArray()->all();
            });
            return ArrayHelper::map(
                $models,
                $fromField,
                $toField
            );
        } else {
            $modelsData = $this->getCachedModelData($modelClass, self::CACHE_KEY_MODELS_DATA, function ($modelClass) {
                /** @var ActiveRecord $modelClass  */
                return $modelClass::find()->asArray()->all();
            });;
            return array_combine(
                array_column($modelsData, $fromField),
                array_column($modelsData, $toField)
            );
        }
    }

    protected function getCachedModelData($modelClass, $keyPrefix, $createDataFunction)
    {
        /** @var ActiveRecord|string $modelClass */
        $cacheKey = $keyPrefix . $modelClass;
        $cache = Yii::$app->cache;
        if(!isset($this->cachedModels[$modelClass]) || false === ($modelsData = $cache->get($cacheKey))) {
            $modelsData = $createDataFunction($modelClass);
            Yii::$app->cache->set($cacheKey, $modelsData);
            $this->cachedModels[$modelClass] = time();
            Yii::$app->cache->set(self::CACHE_KEY_CACHED_MODELS, $this->cachedModels);
        }
        return $modelsData;
    }

    /**
     * Get option value to option name mapping for the given class
     * Mappings are expected to have at least a "from" and a "to" attribute
     * @param $modelClass
     * @return mixed
     */
    protected function getMap($modelClass)
    {
        if (isset($this->maps[$modelClass])) {
            return $this->maps[$modelClass];
        } elseif (isset($this->maps['default'])) {
            return $this->maps['default'];
        } else {
            throw new \RuntimeException('No value to name map or default map found for ' . $modelClass . ' while trying to create a select field.');
        }
    }

}