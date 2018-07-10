<?php

namespace mate\yii\widgets;

use mate\yii\assets\AlertBoxesAsset;
use Yii;
use yii\bootstrap\Html;

class AlertBoxes
{

    public static function htmlFromFlashMessages($alertOptions = [], $containerOptions = [])
    {
        $allFlashes = Yii::$app->session->getAllFlashes();
        if(empty($allFlashes)) {
            return null;
        }
        $view = Yii::$app->getView();

        AlertBoxesAsset::register($view);
        $view->registerJs("
        $(document).ready(function () {
            var messages = $('#flash-messages');
            $.fn.alertBox.options = $.extend(true, {}, $.fn.alertBox.options, messages.data('options'));
            var target = $(messages.data('target') ? messages.data('target') : 'body')
            var messagesData = messages.data('messages');
            $.each(messagesData, function () {
                target.alertBox(this);
            });
        });");

        $flashMessages = [];
        foreach ($allFlashes as $status => $data) {
            $data = (array)$data;
            foreach ($data as $message) {
                $flashMessages[] = [
                    "status"  => $status,
                    "message" => $message
                ];
            }
        }
        return Html::tag('div', '', array_merge_recursive($containerOptions, [
            'id' => 'flash-messages',
            'data' => [
                'messages' => json_encode($flashMessages),
                'options' => json_encode($alertOptions)
            ]
        ]));
    }

}