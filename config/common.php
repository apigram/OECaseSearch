<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 13/06/2017
 * Time: 10:42 AM
 */

return array(
    'params' => array(
        'OECaseSearch' => array(
            'parameters' => array(
                'PatientAge',
                'PatientDiagnosis',
                'PatientMedication',
                'PreviousTrial',
                'PatientAllergy',
                'FamilyHistory',
            ),
            'fixedParameters' => array(
                'PatientDeceased',
            ),
            'providers' => array(
                'mysql' => 'DBProvider',
            ),
        ),
    ),
);