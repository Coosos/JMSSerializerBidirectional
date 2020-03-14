<?php

namespace Coosos\BidirectionalRelation\EventSubscriber;

use ArrayAccess;
use Coosos\BidirectionalRelation\Annotations\SerializerBidirectionalRelation;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Context;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * Class MapSerializerSubscriber
 *
 * @package Coosos\BidirectionalRelation\EventSubscriber
 * @author  Remy Lescallier <lescallier1@gmail.com>
 */
class MapSerializerSubscriber implements EventSubscriberInterface
{
    const MAPPING_FIELD_NAME = '_mapping_bidirectional_relation';

    /**
     * @var array
     */
    protected $alreadyHashObject;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.post_serialize',
                'method' => 'onPostSerialize',
            ],
        ];
    }

    /**
     * Apply mapping to object
     *
     * @param ObjectEvent $event
     *
     * @throws ReflectionException
     */
    public function onPostSerialize(ObjectEvent $event)
    {
        if (!is_object($event->getObject()) ||
            $event->getContext()->getDepth() !== 0 ||
            !$this->hasSerializerBidirectionalRelationAnnotation($event->getObject())
        ) {
            return;
        }

        $this->alreadyHashObject = [];
        $currentMappings = $this->optimizeMappingSerialize(
            $this->buildMapping($event->getObject(), $event->getContext())
        );

        $visitor = $event->getVisitor();
        $data = [
            new StaticPropertyMetadata('', self::MAPPING_FIELD_NAME, $currentMappings),
            $currentMappings,
        ];

        if (!$visitor instanceof SerializationVisitorInterface) {
            $data[] = $event->getContext();
        }

        $visitor->visitProperty(...$data);
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

    /**
     * Build mapping
     *
     * @param mixed       $object
     * @param Context     $context
     * @param string|null $prev
     *
     * @return mixed
     * @throws ReflectionException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function buildMapping($object, Context $context, string $prev = null)
    {
        $splObjectHash = spl_object_hash($object);
        if (isset($this->alreadyHashObject[$splObjectHash])) {
            return [$prev => $splObjectHash];
        }

        $this->alreadyHashObject[$splObjectHash] = true;

        if (!$prev) {
            $prev = 'root';
        }

        $map = [];
        $map[$prev] = $splObjectHash;

        $propertyMetadata = $context->getMetadataFactory()->getMetadataForClass(get_class($object))->propertyMetadata;
        $properties = (new ReflectionClass($object))->getProperties(
            ReflectionProperty::IS_PUBLIC |
            ReflectionProperty::IS_PROTECTED |
            ReflectionProperty::IS_PRIVATE
        );

        foreach ($properties as $property) {
            if (!in_array($property->getName(), array_keys($propertyMetadata))
                || $propertyMetadata[$property->getName()] instanceof VirtualPropertyMetadata) {
                continue;
            }

            $property->setAccessible(true);
            $propertyValue = $property->getValue($object);

            if (is_object($propertyValue) && !$propertyValue instanceof ArrayAccess) {
                $map = array_merge(
                    $map,
                    $this->buildMapping($propertyValue, $context, sprintf('%s,%s', $prev, $property->getName()))
                );
            } elseif (is_array($propertyValue) || $propertyValue instanceof ArrayAccess) {
                $map = array_merge($map, $this->buildMappingParseArray($object, $context, $property, $prev));
            }
        }

        return $map;
    }

    /**
     * Build mapping parse array
     *
     * @param mixed              $object
     * @param Context            $context
     * @param ReflectionProperty $property
     * @param string             $prev
     *
     * @return array
     * @throws ReflectionException
     */
    private function buildMappingParseArray($object, Context $context, ReflectionProperty $property, string $prev)
    {
        $i = 0;
        $map = [];
        $propertiesMetadata = $context->getMetadataFactory()->getMetadataForClass(get_class($object))->propertyMetadata;
        $propertyMetadata = $propertiesMetadata[$property->getName()];
        $propertyValue = $property->getValue($object);
        $keepKey = $propertyMetadata instanceof PropertyMetadata ?
            (is_null($propertyMetadata->type) || count($propertyMetadata->type['params']) > 1) :
            true;

        foreach ($propertyValue as $key => $item) {
            $prevMessage = sprintf('%s,%s,__array,%s', $prev, $property->getName(), ($keepKey ? $key : $i++));
            if (!is_object($item)) {
                continue;
            }

            $map = array_merge($map, $this->buildMapping($item, $context, $prevMessage));
        }

        return $map;
    }

    /**
     * Optimize mapping
     *
     * @param array $mappings
     *
     * @return array
     */
    private function optimizeMappingSerialize(array $mappings): array
    {
        $tempMapping = $newMapping = [];

        $i = 0;
        foreach ($mappings as $path => $mapping) {
            if (!isset($tempMapping[$mapping])) {
                $tempMapping[$mapping] = ++$i;
            }

            $newMapping[$path] = $tempMapping[$mapping];
        }

        return $newMapping;
    }
}
