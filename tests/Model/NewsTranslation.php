<?php

namespace Coosos\BidirectionalRelation\Tests\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class NewsTranslation
 *
 * @package Coosos\BidirectionalRelation\Tests\Model
 * @author  Remy Lescallier <lescallier1@gmail.com>
 */
class NewsTranslation
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private $title;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private $content;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private $locale;

    /**
     * @var News
     *
     * @Serializer\Type("Coosos\BidirectionalRelation\Tests\Model\News")
     */
    private $newsTranslatable;

    /**
     * @var NewsTranslationImage
     *
     * @Serializer\Type("Coosos\BidirectionalRelation\Tests\Model\NewsTranslationImage")
     */
    private $newsTranslationImage;

    /**
     * @var Tag[]
     *
     * @Serializer\Type("array<Coosos\BidirectionalRelation\Tests\Model\Tag>")
     */
    private $tags;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return NewsTranslation
     */
    public function setTitle(string $title): NewsTranslation
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return NewsTranslation
     */
    public function setContent(string $content): NewsTranslation
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return NewsTranslation
     */
    public function setLocale(string $locale): NewsTranslation
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return News
     */
    public function getNewsTranslatable(): News
    {
        return $this->newsTranslatable;
    }

    /**
     * @param News $newsTranslatable
     *
     * @return NewsTranslation
     */
    public function setNewsTranslatable(News $newsTranslatable): NewsTranslation
    {
        $this->newsTranslatable = $newsTranslatable;

        return $this;
    }

    /**
     * @return NewsTranslationImage
     */
    public function getNewsTranslationImage(): NewsTranslationImage
    {
        return $this->newsTranslationImage;
    }

    /**
     * @param NewsTranslationImage $newsTranslationImage
     *
     * @return NewsTranslation
     */
    public function setNewsTranslationImage(NewsTranslationImage $newsTranslationImage): NewsTranslation
    {
        $newsTranslationImage->setNewsTranslation($this);
        $this->newsTranslationImage = $newsTranslationImage;

        return $this;
    }

    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param Tag[] $tags
     *
     * @return NewsTranslation
     */
    public function setTags(array $tags): NewsTranslation
    {
        $this->tags = $tags;

        return $this;
    }
}
