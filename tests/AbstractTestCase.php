<?php

namespace Coosos\BidirectionalRelation\Tests;

use Coosos\BidirectionalRelation\EventSubscriber\MapDeserializerSubscriber;
use Coosos\BidirectionalRelation\EventSubscriber\MapSerializerSubscriber;
use Coosos\BidirectionalRelation\Tests\Model\News;
use Coosos\BidirectionalRelation\Tests\Model\NewsTranslation;
use Coosos\BidirectionalRelation\Tests\Model\NewsTranslationImage;
use Coosos\BidirectionalRelation\Tests\Model\Tag;
use Coosos\BidirectionalRelation\Tests\Model\User;
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
            $dispatcher->addSubscriber(new MapDeserializerSubscriber());
        });

        $this->jmsSerializer = $builder->build();
    }

    /**
     * @return News
     */
    protected function generateExampleNews()
    {
        $news = new News();
        $news->setAuthor($this->generateUser());

        $newsTranslations = [];
        foreach (['en', 'fr'] as $locale) {
            $newsTranslations[$locale] = $this->generateNewsTranslation($locale);
        }

        $news->setNewsTranslations($newsTranslations);

        return $news;
    }

    /**
     * @return User
     */
    private function generateUser()
    {
        return (new User())->setId(1)->setUsername('USER');
    }

    /**
     * @param string $locale
     *
     * @return NewsTranslation
     */
    private function generateNewsTranslation(string $locale)
    {
        $newsTranslation = new NewsTranslation();
        $newsTranslation->setLocale($locale);
        $newsTranslation->setNewsTranslationImage($this->generateNewsTranslationImage($locale));
        $newsTranslation->setTags($this->generateTags($locale));
        if ($locale === 'fr') {
            $newsTranslation->setTitle('Un titre');
            $newsTranslation->setContent('Un continue');
        } elseif ($locale === 'en') {
            $newsTranslation->setTitle('A title');
            $newsTranslation->setContent('A content');
        }

        return $newsTranslation;
    }

    /**
     * @param string $locale
     *
     * @return array
     */
    private function generateTags(string $locale)
    {
        $key = 10;
        $tags = [];
        $values = ['en' => ['Hello', 'Bye'], 'fr' => ['Bonjour', 'Au revoir']];
        foreach ($values[$locale] as $value) {
            $tags[$key++] = (new Tag())->setValue($value);
        }

        return $tags;
    }

    /**
     * @param string $locale
     *
     * @return NewsTranslationImage
     */
    private function generateNewsTranslationImage(string $locale)
    {
        $newsTranslationImage = new NewsTranslationImage();
        $newsTranslationImage->setPath('path_' . $locale . '.png');

        return $newsTranslationImage;
    }
}
