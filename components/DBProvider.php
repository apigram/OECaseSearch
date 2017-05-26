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
        $lastID = -1;
        $bindValues = array();

        // Construct the SQL search string using each parameter as a separate dataset merged using JOINs.
        foreach ($criteria as $id => $param)
        {
            if (isset($queryStr))
            {
                $queryStr .= $param->join($lastID, array('id' => 'id'), $this);
            }
            else
            {
                $from = $param->query($this);
                $alias = $param->alias();
                $queryStr = "SELECT $alias.id FROM ($from) $alias";
            }
            $bindValues = array_merge($bindValues, $param->bindValues());
            $lastID = $id;
        }

        $command = Yii::app()->db->createCommand($queryStr)->bindValues($bindValues);
        $command->prepare();

        return $command->queryAll();
    }
}