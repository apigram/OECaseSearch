<?php

/**
 * Created by PhpStorm.
 * User: andre
 * Date: 22/05/2017
 * Time: 11:11 AM
 */
abstract class CaseSearchParameter extends CFormModel
{
    /**
     * @var CaseSearchParameter name
     */
    public $name;

    /**
     * @var CaseSearchParameter operator.
     */
    public $operation;

    /**
     * Get the parameter identifier (usually the name).
     * @return string The human-readable name of the parameter (for display purposes).
     */
    abstract public function getKey();

    /**
     * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
     * @return array An array of attribute names.
     */
    public function attributeNames()
    {
        return array('name','operation');
    }

    /**
     * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array(
            array('operation', 'required'),
            array('operation', 'safe')
        );
    }

    /**
     * Render the parameter on-screen.
     * @param $id The position of the parameter in the list of parameters.
     */
    abstract public function renderParameter($id);

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @param $searchProvider The search provider. This is used to determine whether or not the search provider is using SQL syntax.
     * @return mixed The constructed query string.
     */
    abstract public function query($searchProvider);

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    abstract public function bindValues();

    /**
     * Generate a SQL fragment representing a JOIN condition to a subquery.
     * @param $joinAlias The alias of the table being joined to.
     * @param $criteria An array of join conditions. The ID for each element is the column name from the aliased table.
     * @param $searchProvider The search provider. This is used for an internal query invocation for subqueries.
     * @return string A SQL string representing a complete join condition. Join type is specified within the subclass definition.
     */
    abstract public function join($joinAlias, $criteria, $searchProvider);

    /**
     * Get the alias of the database table being used by this parameter instance.
     * @return string The alias of the table for use in the SQL query.
     */
    abstract public function alias();
}