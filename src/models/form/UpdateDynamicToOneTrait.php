<?php


namespace mate\yii\models\form;

use yii\db\ActiveRecord;

trait UpdateDynamicToOneTrait
{

    /**
     * @deprecated use updateToOne() instead
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

    /**
     * Will dynamically update a toOne related assiciation.
     *
     * If the foreign key of an existing related model is set, it will be kept.
     *
     * If another value is set to the foreign key, a new related model will be added with this as a defined value, "name" by default.
     *
     * Options:
     * relatedNameAttribute: Related Value Attribute to be filled upon the creation of a new related model
     * value: Can be used as the value set to RelatedModel::$relValueAttr alternatively to whatever value is set to Model::$foreignKeyAttr
     * unique: If set to true, it will be looked for existing models with the given value, too.
     * createWithAttributes: Array of values, that will be set upon model creation, if necessarry.
     *
     * @param string $relationName
     * @param array $options
     * @return null
     */
    public function updateToOne($relationName, $options = [])
    {
        $relation = $this->getRelation($relationName);
        $link = $relation->link;
        $relPrimaryKeyAttr = key($link);
        $foreignKeyAttr = current($link);
        $relModelClass = $relation->modelClass;

        $relNameAttr = isset($options['relatedNameAttribute']) ? $options['relatedNameAttribute'] : "name";
        $value = isset($options['value']) ? $options['value'] : $this->$foreignKeyAttr;
        $unique = isset($options['unique']) ? $options['unique'] : true;
        $createWithAttributes = isset($options['creationAttributes']) ? $options['creationAttributes'] : [];

        if(!$value) {
            return null;
        }

        /** @var ActiveRecord $relModelClass */
        if($unique) {
            $existingModel = $relModelClass::find()->where([
                'or',
                [$relPrimaryKeyAttr => $value],
                [$relNameAttr => $value]
            ])->one();
        } else {
            $existingModel = $relModelClass::find()->where([$relPrimaryKeyAttr => $value])->one();
        }

        if($existingModel) {
            $this->$foreignKeyAttr = $existingModel->$relPrimaryKeyAttr;
        } else {
            /** @var ActiveRecord $addModel */
            $addModel = new $relModelClass;
            $createWithAttributes = array_merge(
                [$relNameAttr => $value],
                $createWithAttributes
            );
            $addModel->setAttributes($createWithAttributes);
            if(!$addModel->save()) {
                throw new \RuntimeException("Unable to add related model $relModelClass with value '$value'. Validation errors: " . var_export($addModel->errors));
            }
            $this->$foreignKeyAttr = $addModel->$relPrimaryKeyAttr;
        }
    }

}