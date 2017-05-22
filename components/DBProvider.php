<?php

/**
 * Created by PhpStorm.
 * User: andre
 * Date: 22/05/2017
 * Time: 10:18 AM
 */
class DBProvider extends SearchProvider
{
    /**
     * @param array $criteria The parameters to search with.
     * @return array The returned data from the search.
     */
    protected function executeSearch($criteria)
    {
        // search the database using the specified criteria.
        $query = new CDbCriteria();
        $query->compare(get_key($criteria[1]));
    }
}