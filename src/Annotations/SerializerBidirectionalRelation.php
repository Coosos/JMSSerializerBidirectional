<?php

namespace Coosos\BidirectionalRelation\Annotations;

/**
 * Class SerializerBidirectionalRelation
 *
 * @package Coosos\BidirectionalRelation\Annotations
 * @author  Remy Lescallier <lescallier1@gmail.com>
 *
 * @Annotation
 * @Target({"CLASS"})
 */
class SerializerBidirectionalRelation
{
    public const MAPPING_FIELD_NAME = '_mapping_bidirectional_relation';
}
