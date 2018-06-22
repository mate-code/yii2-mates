<?php


namespace mate\yii\components;

use Yii;
use yii\base\Component;
use yii\web\Application;
use yii\web\Cookie;

class Language extends Component
{

    public function init()
    {
        Yii::$app->on(Application::EVENT_BEFORE_ACTION, [$this, 'initLanguage']);
    }

    public function initLanguage()
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

    /**
     * @param $language
     */
    public static function switchLanguage($language)
    {
        Yii::$app->session->set("language", $language);
        $cookies = Yii::$app->request->cookies;
        if(!$cookies->readOnly) {
            Yii::$app->request->cookies->add(new Cookie([
                'name' => 'language',
                'value' => $language,
            ]));
        }
        Yii::$app->language = $language;
    }
}