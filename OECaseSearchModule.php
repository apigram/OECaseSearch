<?php

/**
 * Class OECaseSearchModule
 * When adding this module to the application, parameter classes are specified as XXYY (Parameter is automatically appended).
 */
class OECaseSearchModule extends CWebModule
{
    public $parameters = array();

    public $searchClass;
    private $searchProvider;

    public function init()
    {
        // import the module-level models and components
        $this->setImport(array(
            'OECaseSearch.models.*',
            'OECaseSearch.components.*',
        ));

        // Initialise the search provider.
        $this->searchProvider = new $this->searchClass;

        $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.OECaseSearch.assets'));
        Yii::app()->clientScript->registerScriptFile($path . '/js/QueryBuilder.js');

        Yii::app()->clientScript->registerCssFile($path . '/css/QueryBuilder.css');
    }

    public function getParamList()
    {
        $keys = array();
        foreach ($this->parameters as $parameter)
        {
            $className = $parameter . 'Parameter';
            $obj = new $className;
            $keys[$className] = $obj->getKey();
        }

        return $keys;
    }

    /**
     * @return SearchProvider The initialised search provider.
     */
    public function getSearchProvider()
    {
        return $this->searchProvider;
    }

    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else {
            return false;
        }
    }

    public function getModuleInheritanceList()
    {
        return array();
    }
}
