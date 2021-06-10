<?php


namespace mate\yii\components;

use Yii;
use yii\base\Component;
use yii\web\Application;

class AdvancedAlias extends Component
{

    protected $aliases = [];

    public function init() {
        Yii::$app->on(Application::EVENT_BEFORE_ACTION, [$this, 'addAdvancedAliases']);
    }

    public function addAdvancedAliases()
    {
        if(isset(Yii::$app->params["aliases"]) && is_array(Yii::$app->params["aliases"])) {
            $this->aliases = Yii::$app->params["aliases"];
        }
        $this->setFrontendRootAlias();
        $this->setBackendRootAlias();
        if(Yii::$app->request->hasProperty("baseUrl")) {
            $this->setFrontendWebAlias();
            $this->setBackendWebAlias();
        }
        $this->overwriteAliasesWithConfig();
    }

    /**
     * Sets the default fronend root alias by using the @app alias
     */
    protected function setFrontendRootAlias()
    {
        Yii::setAlias('@webrootFrontend', realpath(Yii::getAlias("@app/../frontend/web/")));
    }

    /**
     * Sets the default backend root alias by using the @app alias
     */
    protected function setBackendRootAlias()
    {
        Yii::setAlias('@webrootBackend', realpath(Yii::getAlias("@app/../backend/web/")));
    }

    /**
     * Sets the default fronend web URL alias by using the baseUrl
     * Replaces "backend" with "frontend" if the backend baseUrl is given
     */
    protected function setFrontendWebAlias()
    {
        Yii::setAlias('@webFrontend', str_replace("backend", "frontend", Yii::$app->request->baseUrl));
    }

    /**
     * Sets the default backend web URL alias by using the baseUrl
     * Replaces "frontend" with "backend" if the frontend baseUrl is given
     */
    protected function setBackendWebAlias()
    {
        Yii::setAlias('@webBackend', str_replace("frontend", "backend", Yii::$app->request->baseUrl));
    }

    /**
     * Overwrites the default aliases with the Yii params config.
     * Yii::$params['aliases'] needs to provide an array of aliases to be set
     */
    protected function overwriteAliasesWithConfig() {
        foreach ($this->aliases as $aliasName => $aliasString) {
            Yii::setAlias("@{$aliasName}", $aliasString);
        }
    }
}
