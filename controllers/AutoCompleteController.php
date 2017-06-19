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
                'actions' => array('commonDiagnoses', 'commonMedicines', 'commonAllergies'),
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

    /**
     * Get the first 30 medicine matches for the given text. This is executed through an implicit AJAX request from the CJuiAutoComplete widget.
     * @param $term The term supplied from the JUI Autocomplete widget.
     */
    public function actionCommonMedicines($term)
    {
        $drugs = Drug::model()->findAllBySql("
SELECT *
FROM drug d 
WHERE LCASE(d.name) LIKE LCASE(:term) ORDER BY d.name LIMIT 30", array('term' => "$term%"));

        $medicationDrugs = MedicationDrug::model()->findAllBySql("
SELECT *
FROM medication_drug md
WHERE LCASE(md.name) LIKE LCASE(:term) ORDER BY md.name LIMIT 30", array('term' => "$term%"));

        $values = array();
        foreach ($drugs as $drug)
        {
            $values[$drug->name] = $drug->name;
        }

        foreach ($medicationDrugs as $medicationDrug)
        {
            // Filter out any duplicates.
            if (!isset($values[$medicationDrug->name]))
            {
                $values[$medicationDrug->name] = $medicationDrug->name;
            }
        }

        sort($values);

        echo CJSON::encode($values);
    }

    /**
     * Get the first 30 allergy matches for the given text. This is executed through an implicit AJAX request from the CJuiAutoComplete widget.
     * @param $term The term supplied from the JUI Autocomplete widget.
     */
    public function actionCommonAllergies($term)
    {
        $allergies = Allergy::model()->findAllBySql("
SELECT a.*
FROM allergy a 
WHERE LCASE(a.name) LIKE LCASE(:term) ORDER BY a.name LIMIT 30", array('term' => "%$term%"));
        $values = array();
        foreach ($allergies as $allergy)
        {
            $values[] = $allergy->name;
        }

        echo CJSON::encode($values);
    }
}