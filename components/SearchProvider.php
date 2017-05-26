<?php

/**
 * Class SearchProvider
 */
abstract class SearchProvider
{
    private $providerID;

    /**
     * SearchProvider constructor.
     * @param $id An identifier uniquely identifying the search provider.
     */
    public function __construct($id)
    {
        $this->providerID = $id;
    }

    /**
     * Perform a search using the specified parameters.
     * @param $parameters A list of Parameter objects representing a search parameter.
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
     * Search delegate function.
     * @param $criteria A list of search parameters.
     * @return mixed Search results.
     */
    abstract protected function executeSearch($criteria);
}