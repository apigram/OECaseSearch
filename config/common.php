<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 13/06/2017
 * Time: 10:42 AM
 */

return array(
    'params' => array(
        'menu_bar_items' => array(
            'casesearch' => array(
                'title' => 'Case Search',
                'uri' => 'OECaseSearch/caseSearch',
                'position' => 4,
            ),
        ),
        'CaseSearch' => array(
            'parameters' => array(
                'core' => array(
                    'PatientAge',
                    'PatientDiagnosis',
                    'PatientMedication',
                    'PatientAllergy',
                    'FamilyHistory',
                    'PatientName',
                    'PatientNumber'
                ),
                /*
                'module' => array(
                    'CaseSearchParameter'
                ),
                */
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