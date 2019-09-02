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
        $cache = Yii::$app->cache;
        $cache->delete(self::CACHE_KEY_MODELS . $modelClass);
        $cache->delete(self::CACHE_KEY_MODELS_DATA . $modelClass);
        unset($this->cachedModels[$modelClass]);
        $cache->set(self::CACHE_KEY_CACHED_MODELS, $this->cachedModels);
    }

    /**
     * clears the entire select data cache
     */
    public function clear()
    {
        $cache = Yii::$app->cache;
        foreach ($this->cachedModels as $cachedModel) {
            $cache->delete(self::CACHE_KEY_MODELS . $cachedModel);
            $cache->delete(self::CACHE_KEY_MODELS_DATA . $cachedModel);
        }
        $this->cachedModels = [];
        $cache->set(self::CACHE_KEY_CACHED_MODELS, $this->cachedModels);
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
     * @throws RuntimeException
     */
    public function fromModel($modelClass, $fromField = null, $toField = null, $useModels = null)
    {
        $map = $this->getMap($modelClass);
        $fromField = $fromField ? $fromField : $map['from'];
        $toField = $toField ? $toField : $map['to'];
        $useModels = $useModels === null ? (isset($map['useModels']) ? $map['useModels'] : false) : $useModels;

        if ($useModels) {
            $models = $this->getCachedModelData($modelClass, self::CACHE_KEY_MODELS, function ($modelClass) use ($toField) {
                /** @var ActiveRecord $modelClass  */
                $orderBy = $this->getOrderBy($modelClass, $toField);
                return $modelClass::find()->orderBy($orderBy)->all();
            });
            return ArrayHelper::map(
                $models,
                $fromField,
                $toField
            );
        } else {
            $modelsData = $this->getCachedModelData($modelClass, self::CACHE_KEY_MODELS_DATA, function ($modelClass) use ($toField) {
                /** @var ActiveRecord $modelClass  */
                $orderBy = $this->getOrderBy($modelClass, $toField);
                return $modelClass::find()->orderBy($orderBy)->asArray()->all();
            });
            $fromData = array_column($modelsData, $fromField);
            $toData = array_column($modelsData, $toField);
            if(count($fromData) != count($toData)) {
                throw new \RuntimeException(
                    "Invalid data mapping for model $modelClass"
                    . (count($fromData) == 0 ? ": Indexes could not be retrieved." : "")
                    . (count($toData) == 0 ? ": Values could not be retrieved." : "")
                    ." Try setting useModels = true"
                );
            }
            return array_combine($fromData, $toData);
        }
    }

    /**
     * @param $modelClass
     * @param $toField
     * @return string
     */
    protected function getOrderBy($modelClass, $toField)
    {
        $map = $this->getMap($modelClass, false);
        if(isset($map['orderBy'])) {
            return $map['orderBy'];
        }
        /** @var ActiveRecord $dummyModel */
        $dummyModel = new $modelClass;
        if($dummyModel->hasAttribute('order')) {
            return 'order ASC';
        }

        return $toField . ' ASC';
    }

    /**
     * @param $modelClass
     * @param $keyPrefix
     * @param $createDataFunction
     * @return mixed
     */
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
     * @param $beStrict throw exception if map could not be found
     * @return mixed
     */
    protected function getMap($modelClass, $beStrict = true)
    {
        if (isset($this->maps[$modelClass])) {
            return $this->maps[$modelClass];
        } elseif (isset($this->maps['default'])) {
            return $this->maps['default'];
        } elseif($beStrict) {
            throw new \RuntimeException('No value to name map or default map found for ' . $modelClass . ' while trying to create a select field.');
        } else {
            return [];
        }
    }

}