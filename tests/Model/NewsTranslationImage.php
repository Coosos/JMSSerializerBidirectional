<?php

namespace Coosos\BidirectionalRelation\Tests\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class NewsTranslationImage
 *
 * @package Coosos\BidirectionalRelation\Tests\Model
 * @author  Remy Lescallier <lescallier1@gmail.com>
 */
class NewsTranslationImage
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private $path;

    /**
     * @var NewsTranslation
     *
     * @Serializer\Type("Coosos\BidirectionalRelation\Tests\Model\NewsTranslation")
     */
    private $newsTranslation;

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return NewsTranslationImage
     */
    public function setPath(string $path): NewsTranslationImage
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return NewsTranslation
     */
    public function getNewsTranslation(): NewsTranslation
    {
        return $this->newsTranslation;
    }

    /**
     * @param NewsTranslation $newsTranslation
     *
     * @return NewsTranslationImage
     */
    public function setNewsTranslation(NewsTranslation $newsTranslation): NewsTranslationImage
    {
        $this->newsTranslation = $newsTranslation;

        return $this;
    }
}
