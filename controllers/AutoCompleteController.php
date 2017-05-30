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
        $medicines = Medication::model()->findAllBySql("
SELECT m.*
FROM medication m 
LEFT JOIN drug d 
  ON d.id = m.drug_id 
LEFT JOIN medication_drug md
  ON md.id = m.medication_drug_id
WHERE LCASE(d.name) LIKE LCASE(:term) OR LCASE(md.name) LIKE LCASE(:term) ORDER BY d.name, md.name LIMIT 30", array('term' => "%$term%"));
        $values = array();
        foreach ($medicines as $medicine)
        {
            $values[] = $medicine->getDrugLabel();
        }

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