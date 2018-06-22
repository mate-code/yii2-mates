<?php

namespace mate\yii\models\behaviors;

use mate\yii\widgets\SelectData;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveRecord;

class UpdateSelectDataCacheBehavior extends Behavior
{

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'updateSelectDataCache',
            ActiveRecord::EVENT_AFTER_DELETE => 'updateSelectDataCache',
            ActiveRecord::EVENT_AFTER_UPDATE => 'updateSelectDataCache',
        ];
    }

    public function updateSelectDataCache(Event $event)
    {
        SelectData::refreshModelsDataCache(get_class($event->sender));
    }
}