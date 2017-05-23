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
        // Construct the SQL search string using each parameter as a separate dataset merged using INTERSECTs.
        foreach ($criteria as $param)
        {
            if (!empty($queryStr))
            {
                $queryStr .= '\nINTERSECT\n';
            }
            if (isset($queryStr))
            {
                $queryStr .= $param->query($this);
            }
            else
            {
                $queryStr = $param->query($this);
            }
        }

        $count = Yii::app()->db->createCommand($queryStr)->query()->rowCount;

        $dataProvider = new CSqlDataProvider($queryStr, array(
            'totalItemCount' => $count,
            'pagination' => array(
                'pageSize' => 10,
            )
        ));
        return $dataProvider->getData();
    }

    public function isSql()
    {
        return true;
    }
}