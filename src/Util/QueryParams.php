<?php

namespace Amelia\Monzo\Util;

class QueryParams
{
    /**
     * The instance's query params.
     *
     * @var array
     */
    protected $params;

    /**
     * Make a new QueryParams instance.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Build a full query string working around {@see http_build_query()}'s flaws.
     *
     * @return string
     */
    public function build()
    {
        $params = collect($this->params)->map(function ($value, string $key) {
            return $this->buildQueryParams($value, $key);
        });

        return $params->implode('&');
    }

    /**
     * Build query params for the API.
     *
     * @param string|array|int $value
     * @param string $key
     * @return string
     */
    protected function buildQueryParams($value, string $key)
    {
        if (is_array($value)) {
            return $this->buildQueryArray($key, $value);
        }

        return rawurlencode($key) . '=' . rawurlencode($value);
    }

    /**
     * Build a query array. {@see http_build_query()} shows you why this is needed!
     *
     * @param string $name
     * @param array $value
     * @return string
     */
    protected function buildQueryArray(string $name, array $value)
    {
        $name = rawurlencode($name);

        return collect($value)->map(function ($value, $key) use ($name) {
            if (is_string($key)) {
                $key = rawurlencode($key);

                return "{$name}[$key]=" . rawurlencode($value);
            }

            return "{$name}[]=" . rawurlencode($value);
        })->implode('&');
    }
}
