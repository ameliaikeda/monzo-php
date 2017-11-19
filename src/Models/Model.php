<?php

namespace Amelia\Monzo\Models;

use ArrayAccess;
use Carbon\Carbon;
use JsonSerializable;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class Model implements Jsonable, Arrayable, JsonSerializable, ArrayAccess
{
    /**
     * The original values given to this model.
     *
     * @var array
     */
    protected $original = [];

    /**
     * An array of attributes on this model.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Attributes to append to this model's json output.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * An array of cast types for this model.
     *
     * As an improvement over laravel, you can cast to models, too.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Base Monzo model constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setAttributes($attributes);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = $this->attributes;

        foreach ($this->appends as $append) {
            $method = 'get' . Str::studly($append) . 'Attribute';

            if (method_exists($this, $method)) {
                $attributes[$append] = $this->$method();
            }
        }

        return $attributes;
    }

    /**
     * Get an array suitable for JSON encoding.
     *
     * @return array
     */
    public function toJsonArray()
    {
        return collect($this->toArray())->map(function ($value) {
            return $value instanceof Carbon
                ? $value->format('Y-m-d\TH:i:s.uP')
                : $value;
        })->all();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toJsonArray(), $options);
    }

    /**
     * Specify data which should be serialized to JSON.
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toJsonArray();
    }

    /**
     * Set attributes on this model.
     *
     * @param array $attributes
     * @return void
     */
    public function setAttributes(array $attributes)
    {
        $this->original = $attributes;

        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $this->cast($key, $value);
        }
    }

    /**
     * Cast a value explicitly to a type defined in {@see static::$casts}.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function cast(string $key, $value)
    {
        if (! array_key_exists($key, $this->casts) || $value === null) {
            return $value;
        }

        $cast = $this->casts[$key];
        $collection = false;

        if (starts_with($cast, 'collection:') && is_array($value)) {
            $collection = true;
            $cast = substr($cast, 10);
        }

        if (class_exists($cast) && is_array($value)) {
            return $this->castClass($cast, $value, $collection);
        }

        switch ($cast) {
            case 'date':
                return Carbon::parse($value);

            default:
                return $value;
        }
    }

    /**
     * Cast to a class (aka a relation).
     *
     * @param string $cast
     * @param array|mixed $value
     * @param bool $collection
     * @return \Amelia\Monzo\Models\Model|\Illuminate\Support\Collection
     */
    protected function castClass(string $cast, $value, bool $collection = false)
    {
        if ($collection) {
            return collect($value)->map(function (array $item) use ($cast) {
                return new $cast($item);
            });
        }

        return new $cast($value);
    }

    /**
     * Get an attribute on this model.
     *
     * @param string $key
     * @return mixed|null
     */
    public function getAttribute(string $key)
    {
        $method = 'get' . Str::studly($key) . 'Attribute';

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return $this->attributes[$key] ?? null;
    }

    /**
     * Dynamically get an attribute on this model.
     *
     * @param string $key
     * @return mixed|null
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Debug information for var_dump/dd.
     *
     * @return array
     */
    public function __debugInfo()
    {
        return $this->toJsonArray();
    }

    /**
     * Whether a offset exists.
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return bool true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->attributes);
    }

    /**
     * Offset to retrieve.
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * Offset to set.
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        //
    }

    /**
     * Offset to unset.
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        //
    }
}
