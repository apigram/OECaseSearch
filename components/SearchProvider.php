<?php

/**
 * Created by PhpStorm.
 * User: andre
 * Date: 19/05/2017
 * Time: 5:11 PM
 */
abstract class SearchProvider
{
    public function search($parameters)
    {
        return $this->executeSearch($parameters);
    }

    /**
     * @param $criteria A list of search parameters.
     * @return mixed An array of search results.
     */
    abstract protected function executeSearch($criteria);

    /**
     * @return boolean True if the search provider uses SQL, or false if it doesn't use SQL.
     */
    abstract public function isSql();
}