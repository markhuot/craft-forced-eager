<?php

namespace markhuot\forcedeager;

use craft\elements\db\ElementQuery;
use craft\events\DefineBehaviorsEvent;
use craft\events\PopulateElementEvent;
use craft\fields\BaseRelationField;
use craft\fields\Matrix;
use markhuot\forcedeager\base\ErrorProxy;
use markhuot\forcedeager\behaviors\AllowLazyRelationsBehavior;
use verbb\supertable\elements\db\SuperTableBlockQuery;
use verbb\supertable\fields\SuperTableField;
use yii\base\BootstrapInterface;
use yii\base\Event;

class Eager implements BootstrapInterface
{
    function bootstrap($app)
    {
        Event::on(ElementQuery::class, ElementQuery::EVENT_DEFINE_BEHAVIORS, function (DefineBehaviorsEvent $event) {
            $event->behaviors['AllowLazyRelationsBehavior'] = AllowLazyRelationsBehavior::class;
        });

        Event::on(ElementQuery::class, ElementQuery::EVENT_AFTER_POPULATE_ELEMENT, function (PopulateElementEvent $event) {
            if (\Craft::$app->request->isConsoleRequest) {
                return;
            }

            if (!\Craft::$app->request->isSiteRequest) {
                return;
            }

            /** @var AllowLazyRelationsBehavior $query */
            $query = $event->sender;
            if (!is_a($query, SuperTableBlockQuery::class) && $query->lazyRelationsAreAllowed()) {
                return;
            }

            foreach ($event->element->getFieldLayout()->getCustomFields() as $field) {
                if (is_subclass_of($field, BaseRelationField::class) || is_a($field, Matrix::class) || is_a($field, SuperTableField::class)) {
                    $event->element->setEagerLoadedElements($field->handle, [new ErrorProxy($event->sender, $event->element, $field)]);
                }
            }
        });
    }
}