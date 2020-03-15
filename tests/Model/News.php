<?php

namespace Coosos\BidirectionalRelation\Tests\Model;

use Coosos\BidirectionalRelation\Annotations\ExcludeFromMapping;
use Coosos\BidirectionalRelation\Annotations\SerializerBidirectionalRelation;
use JMS\Serializer\Annotation as Serializer;
use stdClass;

/**
 * Class News
 *
 * @package Coosos\BidirectionalRelation\Tests\Model
 * @author  Remy Lescallier <lescallier1@gmail.com>
 *
 * @SerializerBidirectionalRelation()
 */
class News
{
    /**
     * @var User|null
     *
     * @Serializer\Exclude()
     */
    private $author;

    /**
     * @var NewsTranslation[]
     *
     * @Serializer\Type("array<string,Coosos\BidirectionalRelation\Tests\Model\NewsTranslation>")
     */
    private $newsTranslations;

    /**
     * @var string
     *
     * @Serializer\Exclude()
     */
    private $excludedField;

    /**
     * @var array
     *
     * @Serializer\Type("array")
     */
    private $simpleArray;

    /**
     * @var stdClass
     *
     * @Serializer\Type("stdClass")
     * @ExcludeFromMapping()
     */
    private $fieldExcludedFromMapping;

    /**
     * News constructor.
     */
    public function __construct()
    {
        $this->newsTranslations = [];
        $this->simpleArray = ['hello', 'word'];
        $this->fieldExcludedFromMapping = new stdClass();
    }

    /**
     * @return User|null
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @return int|null
     * @Serializer\VirtualProperty("author")
     */
    public function getAuthorId()
    {
        return $this->author ? $this->author->getId() : null;
    }

    /**
     * @param User|null $author
     *
     * @return News
     */
    public function setAuthor(?User $author): News
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return NewsTranslation[]
     */
    public function getNewsTranslations(): array
    {
        return $this->newsTranslations;
    }

    /**
     * @param NewsTranslation[] $newsTranslations
     *
     * @return News
     */
    public function setNewsTranslations(array $newsTranslations): News
    {
        array_walk($newsTranslations, [$this, 'setNewsTranslatable']);
        $this->newsTranslations = $newsTranslations;

        return $this;
    }

    /**
     * @return string
     */
    public function getExcludedField(): string
    {
        return $this->excludedField;
    }

    /**
     * @param string $excludedField
     *
     * @return News
     */
    public function setExcludedField(string $excludedField)
    {
        $this->excludedField = $excludedField;

        return $this;
    }

    /**
     * @param NewsTranslation $newsTranslation
     *
     * @return $this
     */
    private function setNewsTranslatable(NewsTranslation $newsTranslation)
    {
        $newsTranslation->setNewsTranslatable($this);

        return $this;
    }
}
