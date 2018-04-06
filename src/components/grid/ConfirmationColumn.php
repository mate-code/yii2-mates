<?php

namespace common\components\grid;

use yii\grid\ActionColumn;

class ConfirmationColumn extends ActionColumn
{

    public $template = '{confirm} {decline}';

    protected function initDefaultButtons()
    {
        parent::initDefaultButtons();
        $this->initDefaultButton('confirm', 'ok', [
            "class" => "confirmation-link confirm"
        ]);
        $this->initDefaultButton('decline', 'remove', [
            "class" => "confirmation-link decline ajax-dialog"
        ]);
    }


}