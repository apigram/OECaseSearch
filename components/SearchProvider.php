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

    abstract protected function executeSearch($criteria);
}