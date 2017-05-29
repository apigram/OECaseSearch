<?php

class CaseSearchController extends BaseModuleController
{
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
     */
    public function actionIndex()
    {
        $valid = true;
        $parameters = array();
        $ids = array();
        if (isset($_SESSION['last_search'])) {
            $ids = $_SESSION['last_search'];
        }

        $criteria = new CDbCriteria();
        foreach ($this->module->parameters as $parameter) {
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
        if (!empty($parameters) and $valid) {
            $this->actionClear();
            $results = $this->module->getSearchProvider('mysql')->search($parameters);

            $ids = array();

            // deconstruct the results list into a single array of primary keys.
            foreach ($results as $result) {
                $ids[] = $result['id'];
            }

            // Only copy to the $_SESSION array if it isn't already there - Shallow copy is done at the start if it is already set.
            if (!isset($_SESSION['last_search']) or empty($_SESSION['last_search']))
            {
                $_SESSION['last_search'] = $ids;
            }
        }
        $criteria->compare('id', empty($ids) ? -1 : $ids);

        $patientData = new CActiveDataProvider('Patient', array(
            'criteria' => $criteria,
            'totalItemCount' => count($ids),
            'pagination' => array(
                'pageSize' => 10
            )
        ));
        $paramList = $this->module->getParamList();

        $this->render('index', array(
            'paramList' => $paramList,
            'params' => $parameters,
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