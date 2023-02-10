<?php

namespace markhuot\forcedeager\behaviors;

use yii\base\Behavior;

class AllowLazyRelationsBehavior extends Behavior
{
    /**
     * Whether lazy relations are allowed at all
     *
     * @var bool
     */
    protected $allowLazyRelations = false;

    public function allowLazyRelations($value=true)
    {
        $this->allowLazyRelations = $value;

        return $this->owner;
    }

    public function lazyRelationsAreAllowed()
    {
        return $this->allowLazyRelations;
    }

}