<?php

/**
 * Class DBProvider
 */
class DBProvider extends SearchProvider
{
    /**
     * @param array $criteria The parameters to search with. The parameters must implement the DBProviderInterface interface.
     * @return array The returned data from the search.
     */
    protected function executeSearch($criteria)
    {
        $lastID = -1;
        $bindValues = array();
        $queryStr = null;

        // Construct the SQL search string using each parameter as a separate dataset merged using JOINs.
        foreach ($criteria as $id => $param) {
            // Ignore any case search parameters that do not implement DBProviderInterface
            if ($criteria instanceof DBProviderInterface) {
                if ($queryStr !== null) {
                    $queryStr .= $param->join($criteria[$lastID]->alias(), array('id' => 'id'), $this);
                } else {
                    $from = $param->query($this);
                    $alias = $param->alias();
                    $queryStr = "SELECT $alias.id FROM ($from) $alias";
                }
                $bindValues = array_merge($bindValues, $param->bindValues());
                $lastID = $id;
            }
        }

        $command = Yii::app()->db->createCommand($queryStr)->bindValues($bindValues);
        $command->prepare();

        return $command->queryAll();
    }
}