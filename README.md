# OECaseSearch
Advanced Case searching module for OpenEyes.

## Configuration
To enable this module in OpenEyes, add OECaseSearch to the module list in any common config file.

## Creating your own Case Search parameters
To create a new Case search parameter type, simply:

1. Enable Gii.
2. Navigate to Gii in your web browser of choice using the following URL: `http://<openeyes_url>/gii`.
3. After logging in, click ***CaseSearchParameter Generator***.
4. On the next screen, enter in the class name of the parameter.
5. The SQL alias prefix and parameter name fields will be pre-filled based on the value entered for the class name. If the values need to be different, feel free to change them.
6. Enter in the name of each attribute the parameter will have in addition to its name and selected operator. This can be left blank. Separate each attribute with a comma. ***Do not put spaces between the commas***.
7. Enter in the class name of at least one search provider. If your parameter supports multiple search providers, separate the name of each search provider with a comma. ***Do not put spaces between the commas***.
8. Click Preview. This will generate a snapshot of the parameter class.
9. If you are satisfied with the auto-generated code, click Generate. This will add the parameter to the models folder within the OECaseSearch module.
10. Once the code has been generated, implement the renderParameter function and any functions required by each enabled search provider's interface.
11. Add any validation rules on the attributes to the rules function.
12. In `OECaseSearch/config/common.php`, add the class name to the parameters and/or fixedParameters arrays under 'core' if the parameter is located within the OECaseSearch module, or under the specific module it resides within.
    * **NOTE**: If your case search parameter is located within another module, you must add an array of strings, where the key of the array is the module name and the contents are the class names of the parameters. This should be added to the parent module's config file and not to OECaseSearch.Refer to the OECaseSearch common config file for a sample structure.
13. You're all set! Save your changes and add the parameter to your next case search!

## Search Providers
The OECaseSearch module supports the use of several different search providers concurrently, whether that be an SQL implementation or an indexed search such as Elasticsearch or SOLR. Provisioning between search providers is performed within the CaseSearchController and so is user-defined.
By default, all searching is routed through a single DBProvider instance.

A MySQL-supported search provider, DBProvider, is provided by default (which may also support other SQL-based databases as well); however other search providers can be added by creating subclasses of the SearchProvider abstract class.
To define a new search provider, the subclass must implement the executeSearch($parameters) function and possess a unique providerID. Additionally, any defined case search parameter classes should include handling for each different provider, whether it be MySQL or SOLR, for instance. This is achieved through interface classes.

### Creating your own search providers
To create a new search provider type, simply:

1. Enable Gii.
2. Navigate to Gii in your web browser of choice using the following URL: `http://<openeyes_url>/gii`.
3. After logging in, click ***SearchProvider Generator***.
4. On the next screen, enter in the class name of the search provider.
5. Click Preview. This will generate a snapshot of the search provider class and an interface for CaseSearchParameter subclasses to implement.
6. If you are satisfied with the auto-generated code, click Generate. This will add the search provider and its interface to the components folder within the OECaseSearch module.
7. Once the code has been generated, implement the executeSearch function.
8. Add the class name to the 'providers' array in `OECaseSearch/config/common.php` using its unique identifier as the key eg. `'providers' => array('providerID' => 'ProviderClass'),`
9. You're all set! Save your changes and create some case search parameters that utilise your new search provider.