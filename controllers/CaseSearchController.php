<?php

class CaseSearchController extends BaseModuleController
{
    public function filters()
    {
        return array(
            'accessControl',
            //'ajaxOnly + addParameter',
            'postOnly + delete'
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index', 'search', 'addParameter'),
                'users' => array('admin')
            )
        );
    }

    public function actionIndex()
    {
        $paramList = $this->module->getParamList();
        $this->render('index', array(
            'paramList' => $paramList
        ));
    }

    public function actionSearch()
    {
        //$parameters = array();
        $paramList = $this->module->getParamList();
        // construct the SQL search string
        foreach ($_POST as $id => $param) // The ID in this case should be the class name.
        {
            $newParam = new $id;
            $newParam->attributes = $param;
            $parameters[$id] = $newParam;
        }

        $dataProvider = $this->module->getSearchProvider();
        $results = $dataProvider->search($parameters);
        return $results;
    }

    public function actionAddParameter()
    {
        $parameter = new $_GET['param'];

        $this->renderPartial('parameter_form', array(
            'model' => $parameter
        ));
    }
}