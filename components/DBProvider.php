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
        $queryStr = null;
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
            $lastID = $id;
        }

        $count = Yii::app()->db->createCommand($queryStr)->query()->rowCount;

        $dataProvider = new CSqlDataProvider($queryStr, array(
            'totalItemCount' => $count,
        ));
        return $dataProvider->getData();
    }

    public function isSql()
    {
        return true;
    }
}