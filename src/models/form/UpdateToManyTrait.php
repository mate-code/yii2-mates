<?php

namespace mate\yii\models\form;

use yii\base\Model;
use yii\db\ActiveRecord;


trait UpdateToManyTrait
{

    public function updateToMany($relationName, $modelClass, $selectedIds, $delete = true, $primaryKeyAttr = "id")
    {
        /** @var Model $this */
        if(!$this instanceof Model) {
            throw new \RuntimeException("Class using UpdateManyToManyTrait should extend " . Model::class);
        }
        $selectedIds = empty($selectedIds) ? array() : $selectedIds;
        if(!is_array($selectedIds)) {
            return null;
        }
        /** @var ActiveRecord $modelClass */

        $existing = [];
        foreach ($this->$relationName as $relatedModel) {
            $existing[$relatedModel->$primaryKeyAttr] = $relatedModel;
        }
        $existingIds = array_keys($existing);

        $removeIds = array_diff($existingIds, $selectedIds);
        foreach ($removeIds as $removeId) {
            $this->unlink($relationName, $existing[$removeId], $delete);
        }

        $addIds = array_diff($selectedIds, $existingIds);

        foreach ($addIds as $addId) {
            $linkModel = $modelClass::findOne($addId);
            if(!$linkModel) {
                throw new \InvalidArgumentException("$modelClass with ID $addId does not exist");
            }
            $this->link($relationName, $linkModel);
        }
    }

    public function saveToManyRelatedModels($selectionProperty, $modelClass, $nameAttribute = "name", $primaryKeyAttr = "id")
    {
        $processedSelection = [];
        $selectedValues = $this->$selectionProperty;
        if(!$selectedValues || !is_array($selectedValues)) {
            return $processedSelection;
        }
        /** @var ActiveRecord $modelClass */
        foreach ($selectedValues as $selectedNameOrPosition => $selectedValue) {
            if(!is_numeric($selectedValue) && null !== ($model = $modelClass::findOne([$nameAttribute => $selectedValue]))) {
                $processedSelection[$model->$nameAttribute] = $model->$primaryKeyAttr;
            } elseif(!is_numeric($selectedValue) && !empty($selectedValue)) {
                /** @var ActiveRecord $addModel */
                $addModel = new $modelClass();
                $addModel->$nameAttribute = $selectedValue;
                $addModel->save();
                $processedSelection[$addModel->$nameAttribute] = $addModel->$primaryKeyAttr;
            } else {
                $processedSelection[$selectedNameOrPosition] = $selectedValue;
            }
        }
        $this->$selectionProperty = $processedSelection;
        return $processedSelection;
    }

}