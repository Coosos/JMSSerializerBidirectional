<?php

namespace Coosos\BidirectionalRelation\Tests;

use Coosos\BidirectionalRelation\EventSubscriber\MapSerializerSubscriber;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractTestCase
 *
 * @package Coosos\BidirectionalRelation\Tests
 * @author  Remy Lescallier <lescallier1@gmail.com>
 */
abstract class AbstractTestCase extends TestCase
{
    /**
     * @var SerializerInterface
     */
    protected $jmsSerializer;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $builder = SerializerBuilder::create();
        $builder->configureListeners(function (EventDispatcher $dispatcher) {
            $dispatcher->addSubscriber(new MapSerializerSubscriber());
        });

        $this->jmsSerializer = $builder->build();
    }
}
