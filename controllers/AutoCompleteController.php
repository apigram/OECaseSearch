<?php

class AutoCompleteController extends BaseModuleController
{
    /**
     * Ensure that actions in this controller are only executed via AJAX requests.
     * @return array The filters that apply to this controller.
     */
    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + commonDiagnoses',
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('commonDiagnoses'),
                'users' => array('@'),
            ),
        );
    }

    /**
     * Get the first 30 diagnosis matches for the given text. This is executed through an implicit AJAX request from the CJuiAutoComplete widget.
     * @param $term The term supplied from the JUI Autocomplete widget.
     */
    public function actionCommonDiagnoses($term)
    {
        $disorders = Disorder::model()->findAllBySql("SELECT * FROM disorder WHERE term LIKE :term ORDER BY term LIMIT 30", array('term' => "%$term%"));
        $values = array();
        foreach ($disorders as $disorder)
        {
            $values[] = $disorder->term;
        }

        echo CJSON::encode($values);
    }
}