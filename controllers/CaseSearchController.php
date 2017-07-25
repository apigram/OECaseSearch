<?php

class CaseSearchController extends BaseModuleController
{
    public $layout = '//layouts/main';
    public $trialContext;

    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + addParameter',
            'ajaxOnly + clear',
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index', 'addParameter', 'clear'),
                'users' => array('@'),
            ),
        );
    }

    /**
     * Primary case search action.
     * @param $trial_id integer The Trial that this case search is in context of
     */
    public function actionIndex($trial_id = null)
    {
        $valid = true;
        $parameters = array();
        $fixedParameters = $this->module->getFixedParams();
        $ids = array();

        $this->trialContext = null;
        if ($trial_id !== null) {
            $this->trialContext = Trial::model()->findByPk($trial_id);
        }

        $criteria = new CDbCriteria();

        foreach ($this->module->getConfigParam('parameters') as $group) {
            foreach ($group as $parameter) {
                $paramName = $parameter . 'Parameter';
                if (isset($_POST[$paramName])) {
                    foreach ($_POST[$paramName] as $id => $param) {
                        $newParam = new $paramName;
                        $newParam->attributes = $_POST[$paramName][$id];
                        if (!$newParam->validate()) {
                            $valid = false;
                        }
                        $parameters[$id] = $newParam;
                    }
                }
            }
        }

        foreach ($fixedParameters as $parameter) {
            if (isset($_POST[get_class($parameter)])) {
                foreach ($_POST[get_class($parameter)] as $id => $param) {
                    $parameter->attributes = $_POST[get_class($parameter)][$id];
                    if (!$parameter->validate()) {
                        $valid = false;
                    }
                }
            }
        }

        // This can always run as there will always be at least 1 fixed parameter included in the search. Just as long as it is valid!
        if ($valid) {
            $mergedParams = array_merge($parameters, $fixedParameters);
            $this->actionClear();
            $searchProvider = $this->module->getSearchProvider('mysql');
            $results = $searchProvider->search($mergedParams);

            $ids = array();

            // deconstruct the results list into a single array of primary keys.
            foreach ($results as $result) {
                $ids[] = $result['id'];
            }
        }

        // If there are no IDs found, pass -1 as the value (as this will not match with anything).
        $criteria->compare('t.id', empty($ids) ? -1 : $ids);
        $criteria->with = 'contact';
        $criteria->order = 'last_name, first_name';

        // A data provider is used here to allow faster search times. Results are iterated through using the data provider's pagination functionality and the CListView widget's pager.
        $patientData = new CActiveDataProvider('Patient', array(
            'criteria' => $criteria,
            'totalItemCount' => count($ids),
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));

        // Get the list of parameter types for display on-screen.
        $paramList = $this->module->getParamList();

        $this->render('index', array(
            'paramList' => $paramList,
            'params' => $parameters,
            'fixedParams' => $fixedParameters,
            'patients' => $patientData,
        ));
    }

    /**
     * Add a parameter to the case search. This is executed through an AJAX request.
     */
    public function actionAddParameter()
    {
        $id = $_GET['id'];
        $param = $_GET['param'];
        $parameter = new $param;
        $parameter->id = $id;

        $this->renderPartial('parameter_form', array(
            'model' => $parameter,
            'id' => $id,
        ));
    }

    /**
     * Clear the parameters and search results. This is executed through an AJAX request
     */
    public function actionClear()
    {
        unset($_SESSION['last_search']);
    }
}