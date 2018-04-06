<?php


namespace mate\yii\components;

use Yii;
use yii\base\Component;
use yii\web\Application;

class Language extends Component
{

    public function init()
    {
        Yii::$app->on(Application::EVENT_BEFORE_ACTION, [$this, 'setLanguage']);
    }

    public function setLanguage()
    {
        if (
            Yii::$app->hasProperty("session") &&
            ($sessionLang = Yii::$app->session->get("language")) !== null
        ) {
            $language = $sessionLang;
        } elseif (
            Yii::$app->hasProperty("request") &&
            Yii::$app->request->hasProperty("cookie") &&
            ($cookieLang = Yii::$app->request->cookies->get('language')) !== null
        ) {
            $language = $cookieLang->value;
        } elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        } else {
            $language = "en";
        }
        Yii::$app->language = $language;
    }
}