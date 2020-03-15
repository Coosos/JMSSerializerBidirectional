<?php

namespace Coosos\BidirectionalRelation\EventSubscriber;

use ArrayAccess;
use Coosos\BidirectionalRelation\Annotations\SerializerBidirectionalRelation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * Class MapDeserializerSubscriber
 *
 * @package Coosos\BidirectionalRelation\EventSubscriber
 * @author  Remy Lescallier <lescallier1@gmail.com>
 */
class MapDeserializerSubscriber implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $currentMappings;

    /**
     * MapDeserializerSubscriber constructor.
     */
    public function __construct()
    {
        $this->currentMappings = [];
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
            ],
            [
                'event' => 'serializer.post_deserialize',
                'method' => 'onPostDeserialize',
            ],
        ];
    }

    /**
     * Get mapping
     *
     * @param PreDeserializeEvent $event
     */
    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        if (
            $event->getContext()->getDepth() === 1 &&
            isset($event->getData()[SerializerBidirectionalRelation::MAPPING_FIELD_NAME])
        ) {
            $this->currentMappings = $event->getData()[SerializerBidirectionalRelation::MAPPING_FIELD_NAME];
        }
    }

    /**
     * Restore relation
     *
     * @param ObjectEvent $event
     *
     * @throws ReflectionException
     */
    public function onPostDeserialize(ObjectEvent $event)
    {
        $object = $event->getObject();
        if (
            $event->getContext()->getDepth() !== 0 ||
            !$this->hasSerializerBidirectionalRelationAnnotation($object)
        ) {
            return;
        }

        $map = $this->currentMappings;
        $this->parseDeserialize($object, $map);
    }

    /**
     * Parse deserialize
     *
     * @param mixed      $object
     * @param array                           $map
     * @param string                          $currentMap
     * @param array                           $already
     *
     * @return mixed
     * @throws ReflectionException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function parseDeserialize(
        $object,
        array &$map,
        string $currentMap = 'root',
        array &$already = []
    ) {
        if (!isset($map[$currentMap])) {
            return $object;
        }

        if (isset($already[$map[$currentMap]])) {
            return $already[$map[$currentMap]];
        }

        $already[$map[$currentMap]] = $object;
        unset($map[$currentMap]);

        $properties = (new ReflectionClass($object))->getProperties(
            ReflectionProperty::IS_PUBLIC |
            ReflectionProperty::IS_PROTECTED |
            ReflectionProperty::IS_PRIVATE
        );

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $propertyValue = $property->getValue($object);
            $propertyName = $property->getName();

            if (is_object($propertyValue) && !$propertyValue instanceof ArrayAccess) {
                $property->setValue(
                    $object,
                    $this->parseDeserialize(
                        $propertyValue,
                        $map,
                        sprintf('%s,%s', $currentMap, $propertyName),
                        $already
                    )
                );

                unset($map[sprintf('%s,%s', $currentMap, $propertyName)]);
            } elseif ((is_array($propertyValue) || $propertyValue instanceof ArrayAccess)) {
                $property->setValue(
                    $object,
                    $this->parseList($propertyValue, $propertyName, $currentMap, $map, $already)
                );
            } elseif (
                $propertyValue === null
                && isset($map[$currentMap . ',' . $propertyName])
                && isset($already[$map[$currentMap . ',' . $propertyName]])
            ) {
                $property->setValue($object, $already[$map[$currentMap . ',' . $propertyName]]);
                unset($map[$currentMap . ',' . $propertyName]);
            }
        }

        return $object;
    }

    /**
     * Parse list
     *
     * @param mixed  $propertyValue
     * @param string $propertyName
     * @param string $currentMap
     * @param array  $map
     * @param array  $already
     *
     * @return array|ArrayCollection
     * @throws ReflectionException
     */
    private function parseList($propertyValue, string $propertyName, string $currentMap, array &$map, array &$already)
    {
        $list = ($propertyValue instanceof ArrayCollection) ? $propertyValue : [];
        foreach ($propertyValue as $key => $item) {
            if (class_exists(ArrayCollection::class) && $list instanceof ArrayCollection) {
                $list->set(
                    $key,
                    $this->parseDeserialize(
                        $item,
                        $map,
                        sprintf('%s,%s,__array,%s', $currentMap, $propertyName, $key),
                        $already
                    )
                );
            } elseif (is_array($propertyValue)) {
                $list[$key] = $this->parseDeserialize(
                    $item,
                    $map,
                    sprintf('%s,%s,__array,%s', $currentMap, $propertyName, $key),
                    $already
                );
            }
        }

        return $list;
    }

    /**
     * @param mixed $object
     *
     * @return bool
     * @throws ReflectionException
     */
    private function hasSerializerBidirectionalRelationAnnotation($object)
    {
        $reader = new AnnotationReader();
        $annotation = $reader->getClassAnnotation(new ReflectionClass($object), SerializerBidirectionalRelation::class);

        return $annotation ? true : false;
    }
}
