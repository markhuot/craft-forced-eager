<?php

namespace markhuot\forcedeager\base;

use markhuot\forcedeager\exceptions\LazyLoadedRelationException;

class ErrorProxy
{
    protected $query;
    protected $element;
    protected $field;

    public function __construct($query, $element, $field)
    {
        $this->query = $query;
        $this->element = $element;
        $this->field = $field;
    }

    public function __call($name, $arguments)
    {
        $this->throwError();
    }

    public function __get($name)
    {
        $this->throwError();
    }

    protected function throwError()
    {
        throw new LazyLoadedRelationException('You are lazily accessing a relationship through the [' . $this->field->handle . '] field that should be eager loaded. Try adding `.with([\'' . $this->field->handle . '\'])` to the query.');
    }
}