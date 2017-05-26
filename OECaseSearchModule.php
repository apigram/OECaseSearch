<?php

/**
 * Class OECaseSearchModule
 * When adding this module to the application, parameter classes are specified as XXYY (Parameter is automatically appended eg. PatientAge becomes PatientAgeParameter).
 */
class OECaseSearchModule extends CWebModule
{
    /**
     * @var array A list of parameter classes that can be selected on the Case Search screen.
     */
    public $parameters = array();

    /**
     * @var array A List of search providers that can be used for searching. These are specified in the format ['providerID' => 'className'].
     */
    public $providers = array();
    private $searchProviders = array();

    public function init()
    {
        // import the module-level models and components
        $this->setImport(array(
            'OECaseSearch.models.*',
            'OECaseSearch.components.*',
        ));

        // Initialise the search provider/s.
        foreach ($this->providers as $providerID => $searchProvider)
        {
            $this->searchProviders[$providerID] = new $searchProvider($providerID);
        }

        $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.OECaseSearch.assets'));
        Yii::app()->clientScript->registerScriptFile($path . '/js/QueryBuilder.js');

        Yii::app()->clientScript->registerCssFile($path . '/css/QueryBuilder.css');
    }

    /**
     * @return array The list of parameter classes configured for the case search module.
     */
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
     * @param $providerID The unique ID of the search provider you wish to use. This can be found in config/common.php for each included search provider.
     * @return mixed
     */
    public function getSearchProvider($providerID)
    {
        return $this->searchProviders[$providerID];
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
