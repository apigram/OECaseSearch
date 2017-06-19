# OECaseSearch
Advanced Case searching module for OpenEyes.

##Configuration
To enable this module in OpenEyes, add OECaseSearch to the module list in any common config file.

## Creating your own Case Search parameters
To create a new Case search parameter type, simply:

1. Enable Gii.
2. Navigate to Gii in your web browser of choice using the following URL: `http://<openeyes_url>/gii`.
3. After logging in, click *CaseSearchParameter Generator*.
4. On the next screen, enter in the class name of the parameter.
5. The SQL alias prefix and parameter name fields will be pre-filled based on the value entered for the class name. If the values need to be different, feel free to change them.
6. Enter in the name of each attribute the parameter will have in addition to its name and selected operator. This can be left blank.
7. Click Preview. This will generate a snapshot of the parameter class.
8. If you are satisfied with the auto-generated code, click Generate. This will add the parameter to the models folder within the OECaseSearch module.
9. Once the code has been generated, implement the query, join and renderParameter functions.
10. Add any validation rules on the attributes to the rules function.
11. In `OECaseSearch/config/common.php`, add the class name to the parameters and/or fixedParameters arrays.
12. You're all set! Save your changes and add the parameter to your next case search!

## Search Providers
The OECaseSearch module supports the use of several different search providers concurrently, whether that be a SQL implementation or an indexed search such as Elasticsearch or SOLR. Provisioning between providers is performed within the CaseSearchController and so is user-defined.

A MySQL-supported search provider, DBProvider, is provided by default (which may also support other SQL-based databases as well); however other search providers can be added by creating subclasses of the SearchProvider abstract class.
To define a new search provider, the subclass must implement the executeSearch($parameters) function and possess a unique providerID. Additionally, any defined case search parameter classes should include handling for each different provider, whether it be MySQL or SOLR, for instance.

To add these subclasses to OECaseSearch, add the class name to the 'providers' array in `config/common.php` using its unique identifier as the key eg.
    
    'providers' = array(
        'providerID' => 'ProviderClass'
    ),