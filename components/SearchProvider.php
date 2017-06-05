<?php

/**
 * Class SearchProvider
 */
abstract class SearchProvider
{
    private $providerID;

    /**
     * SearchProvider constructor.
     * @param $id mixed An identifier uniquely identifying the search provider.
     */
    public function __construct($id)
    {
        $this->providerID = $id;
    }

    /**
     * Perform a search using the specified parameters.
     * @param $parameters array A list of CaseSearchParameter objects representing a search parameter.
     * @return mixed Search results. This will take whatever form is specified within the subclass' executeSearch implementation.
     */
    public final function search($parameters)
    {
        return $this->executeSearch($parameters);
    }

    /**
     * Get the search provider's unique ID.
     * @return mixed The search provider's unique ID.
     */
    public final function getProviderID()
    {
        return $this->providerID;
    }

    /**
     * Search delegate function. Implement this function to specify how the search will be executed.
     * @param $criteria array A list of search parameters.
     * @return array Search results.
     */
    abstract protected function executeSearch($criteria);
}