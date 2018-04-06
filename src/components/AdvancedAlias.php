<?php


namespace mate\yii\components;

use Yii;
use yii\base\Component;
use yii\web\Application;

class AdvancedAlias extends Component
{

    public function init() {
        Yii::$app->on(Application::EVENT_BEFORE_ACTION, [$this, 'addAdvancedAliases']);
    }

    public function addAdvancedAliases()
    {
        $this->setFrontendRootAlias();
        $this->setBackendRootAlias();
        if(!Yii::$app->request->hasProperty("baseUrl")) {
            return null;
        }
        $this->setFrontendWebAlias();
        $this->setBackendWebAlias();
    }

    protected function setFrontendRootAlias()
    {
        Yii::setAlias('@webrootFrontend', realpath(Yii::getAlias("@app/../frontend/web/")));
    }

    protected function setBackendRootAlias()
    {
        Yii::setAlias('@webrootBackend', realpath(Yii::getAlias("@app/../backend/web/")));
    }

    protected function setFrontendWebAlias()
    {
        Yii::setAlias('@webFrontend', str_replace("backend", "frontend", Yii::$app->request->baseUrl));
    }

    protected function setBackendWebAlias()
    {
        Yii::setAlias('@webBackend', str_replace("frontend", "backend", Yii::$app->request->baseUrl));
    }
}