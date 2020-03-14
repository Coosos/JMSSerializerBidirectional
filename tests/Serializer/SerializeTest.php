<?php

namespace Coosos\BidirectionalRelation\Tests\Serializer;

use Coosos\BidirectionalRelation\Tests\AbstractTestCase;
use Coosos\BidirectionalRelation\Tests\Model\News;
use Coosos\BidirectionalRelation\Tests\Model\NewsTranslation;
use Coosos\BidirectionalRelation\Tests\Model\NewsTranslationImage;
use Coosos\BidirectionalRelation\Tests\Model\Tag;
use Coosos\BidirectionalRelation\Tests\Model\User;

/**
 * Class SerializeTest
 *
 * @package Coosos\BidirectionalRelation\Tests\Serializer
 * @author  Remy Lescallier <lescallier1@gmail.com>
 */
class SerializeTest extends AbstractTestCase
{
    /**
     * Test serialize and check mapping result
     */
    public function testSerialize()
    {
        $news = $this->generateExampleNews();
        $result = $this->jmsSerializer->serialize($news, 'json');
        $array = json_decode($result, true);
        $this->assertArrayHasKey('_mapping_bidirectional_relation', $array);

        $mapping = $array['_mapping_bidirectional_relation'];
        $this->assertArrayHasKey('root', $mapping);
        $this->assertArrayHasKey('root,newsTranslations,__array,en', $mapping);
        $this->assertArrayHasKey('root,newsTranslations,__array,en,newsTranslatable', $mapping);
        $this->assertArrayHasKey('root,newsTranslations,__array,en,newsTranslationImage', $mapping);
        $this->assertArrayHasKey('root,newsTranslations,__array,en,newsTranslationImage,newsTranslation', $mapping);
        $this->assertArrayHasKey('root,newsTranslations,__array,en,tags,__array,0', $mapping);
        $this->assertArrayHasKey('root,newsTranslations,__array,en,tags,__array,1', $mapping);
        $this->assertArrayHasKey('root,newsTranslations,__array,fr', $mapping);
        $this->assertArrayHasKey('root,newsTranslations,__array,fr,newsTranslatable', $mapping);
        $this->assertArrayHasKey('root,newsTranslations,__array,fr,newsTranslationImage,newsTranslation', $mapping);
        $this->assertArrayHasKey('root,newsTranslations,__array,fr,tags,__array,0', $mapping);
        $this->assertArrayHasKey('root,newsTranslations,__array,fr,tags,__array,1', $mapping);
        $this->assertArrayNotHasKey('root,author', $mapping);

        $this->assertSame($mapping['root,newsTranslations,__array,en,newsTranslatable'], $mapping['root']);
        $this->assertSame($mapping['root,newsTranslations,__array,fr,newsTranslatable'], $mapping['root']);

        $this->assertSame(
            $mapping['root,newsTranslations,__array,en,newsTranslationImage,newsTranslation'],
            $mapping['root,newsTranslations,__array,en']
        );

        $this->assertSame(
            $mapping['root,newsTranslations,__array,fr,newsTranslationImage,newsTranslation'],
            $mapping['root,newsTranslations,__array,fr']
        );
    }

    /**
     * @return News
     */
    private function generateExampleNews()
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
