<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 13/06/2017
 * Time: 10:42 AM
 */

return array(
    'params' => array(
        'CaseSearch' => array(
            'parameters' => array(
                'core' => array(
                    'PatientAge',
                    'PatientDiagnosis',
                    'PatientMedication',
                    'PatientAllergy',
                    'FamilyHistory',
                ),
            ),
            'fixedParameters' => array(
                'core' => array(
                    'PatientDeceased',
                ),
            ),
            'providers' => array(
                'mysql' => 'DBProvider',
            ),
        ),
    ),
);