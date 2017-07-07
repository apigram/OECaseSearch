<?php

/**
 * Interface DBProviderInterface
 */
interface DBProviderInterface
{
    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @param $searchProvider DBProvider The database search provider.
     * @return string The constructed query string.
     */
    public function query($searchProvider);

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues();

    /**
     * Generate a SQL fragment representing a JOIN condition to a subquery.
     * @param $joinAlias string The alias of the table being joined to.
     * @param $criteria array An array of join conditions. The ID for each element is the column name from the aliased table.
     * @param $searchProvider DBProvider search provider. This is used for an internal query invocation for subqueries.
     * @return string A SQL string representing a complete join condition. Join type is specified within the subclass definition.
     */
    public function join($joinAlias, $criteria, $searchProvider);

    /**
     * Get the alias of the database table being used by this parameter instance.
     * @return string The alias of the table for use in the SQL query.
     */
    public function alias();
}