<?php

namespace markhuot\forcedeager;

use craft\elements\db\ElementQuery;
use craft\events\PopulateElementEvent;
use craft\fields\BaseRelationField;
use craft\fields\Matrix;
use markhuot\forcedeager\base\ErrorProxy;
use yii\base\BootstrapInterface;
use yii\base\Event;

class Eager implements BootstrapInterface
{
    function bootstrap($app)
    {
        Event::on(ElementQuery::class, ElementQuery::EVENT_AFTER_POPULATE_ELEMENT, function (PopulateElementEvent $event) {
            foreach ($event->element->getFieldLayout()->getCustomFields() as $field) {
                if (is_subclass_of($field, BaseRelationField::class) || is_a($field, Matrix::class)) {
                    $event->element->setEagerLoadedElements($field->handle, [new ErrorProxy($event->sender, $event->element, $field)]);
                }
            }
        });
    }
}