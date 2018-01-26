<?php

namespace Amelia\Monzo;

use Amelia\Monzo\Util\QueryParams;

trait UsesQueryParams
{
    /**
     * Default query params.
     *
     * @var array
     */
    protected $params = [];

    /**
     * Set the "since" pagination query param.
     *
     * @param string $id
     * @return void
     */
    public function since(string $id)
    {
        $this->params['since'] = $id;
    }

    /**
     * Set the "before" pagination query param.
     *
     * @param string $id
     * @return void
     */
    public function before(string $id)
    {
        $this->params['before'] = $id;
    }

    /**
     * Set the max number of results to return from the API.
     *
     * @param int $limit
     * @return void
     */
    public function take(int $limit)
    {
        $this->params['limit'] = $limit;
    }

    /**
     * Expand a given key in the response.
     *
     * @param string|array $params
     * @return void
     */
    public function expand($params)
    {
        $params = is_array($params) ? $params : [$params];

        $this->params['expand'] = $params;
    }

    /**
     * Return the currently set query parameters.
     *
     * @return array
     */
    public function params()
    {
        return $this->params;
    }

    /**
     * Set the query parameters for this request.
     *
     * @param array $params
     * @return void
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * Build query parameters for this call.
     *
     * @param array $query
     * @return string
     */
    protected function buildQueryParams(array $query)
    {
        $params = new QueryParams(array_merge($this->params, $query));

        return $params->build();
    }
}
