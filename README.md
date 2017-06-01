# OECaseSearch
Advanced Case searching module for OpenEyes.

##Configuration
To enable this module in OpenEyes, add the following lines to config/common.php:

    'OECaseSearch' => array(
        'parameters' => array(
            'PatientAge',
            'PatientDiagnosis',
            'PatientMedication',
            'FamilyHistory',
            'PatientAllergy'
            // Add each parameter class you wish to include in case search here (wihout the 'Parameter' prefix).
        ),
        'providers' => array(
            'mysql' => 'DBProvider',
            // Include any other search provider classes you wish to use here. Format is providerID => providerClass
        )
    ),