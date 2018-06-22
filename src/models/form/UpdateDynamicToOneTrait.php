<?php


namespace mate\yii\models\form;

use yii\db\ActiveRecord;

trait UpdateDynamicToOneTrait
{

    /**
     * This method will set a given foreign key or use a given value to create a new associated model
     *
     * @param string $idColumn model attribute with foreign key
     * @param string $modelClass fully qualified name of associated model
     * @param null $value given value, will use $idColumn to find it if set to null
     * @param string $modelValueAttr name attribute of associated model
     * @param string $primaryKeyAttr primary key column name of associated model
     * @param array $creationAttributes attributes to set before a new model is created
     * @return null
     */
    public function updateDynamicToOne(
        $idColumn,
        $modelClass,
        $value = null,
        $modelValueAttr = "name",
        $primaryKeyAttr = "id",
        $creationAttributes = []
    )
    {
        $value = $value ? $value : $this->$idColumn;
        if(!$value) {
            return null;
        }
        /** @var ActiveRecord $modelClass */
        $hasModel = 0 < $modelClass::find()->where([$primaryKeyAttr => $value])->count();
        if($hasModel) {
            $this->$idColumn = $value;
        } else {
            /** @var ActiveRecord $addModel */
            $addModel = new $modelClass;
            $creationAttributes = array_merge(
                [$modelValueAttr => $value],
                $creationAttributes
            );
            $addModel->setAttributes($creationAttributes);
            $addModel->save();
            $this->$idColumn = $addModel->$primaryKeyAttr;
        }
        return null;
    }

}