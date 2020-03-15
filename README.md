# JMS Serializer Bidirectional Relation

## Description

Build a mapping for restore bidirectional relation when deserialize process.

This mapping is add to your serialized content with ``_mapping_bidirectional_relation`` key.

## Install

### With jms/serializer (without symfony or other framework who dispatch event with a config)

```php
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use Coosos\BidirectionalRelation\EventSubscriber\MapDeserializerSubscriber;
use Coosos\BidirectionalRelation\EventSubscriber\MapSerializerSubscriber;

$builder = SerializerBuilder::create();
$builder->configureListeners(function (EventDispatcher $dispatcher) {
    $dispatcher->addSubscriber(new MapSerializerSubscriber());
    $dispatcher->addSubscriber(new MapDeserializerSubscriber());
});

$serializer = $builder->build();
```

### With Symfony

```yaml
# services.yml
Coosos\BidirectionalRelation\EventSubscriber\MapDeserializerSubscriber:
      tags:
        - { name: jms_serializer.event_subscriber }

Coosos\BidirectionalRelation\EventSubscriber\MapSerializerSubscriber:
      tags:
        - { name: jms_serializer.event_subscriber }
```

## Usage

### Annotation (Minimum required)

For to avoid mapping on an object that does not need it, you should add 
``@Coosos\BidirectionalRelation\Annotations\SerializerBidirectionalRelation`` to annotation class (only root model)

### Exclude from mapping

If you want to exclude an object to map, 
you can by adding ``@Coosos\BidirectionalRelation\Annotations\ExcludeFromMapping`` annotation to your field.
