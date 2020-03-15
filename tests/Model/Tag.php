<?php

namespace Coosos\BidirectionalRelation\Tests\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class Tag
 *
 * @package Coosos\BidirectionalRelation\Tests\Model
 * @author  Remy Lescallier <lescallier1@gmail.com>
 */
class Tag
{
    /**
     * @var string
     * @Serializer\Type("string")
     */
    private $value;

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return Tag
     */
    public function setValue(string $value): Tag
    {
        $this->value = $value;

        return $this;
    }
}
