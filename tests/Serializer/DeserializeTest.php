<?php

namespace Coosos\BidirectionalRelation\Tests\Serializer;

use Coosos\BidirectionalRelation\Tests\AbstractTestCase;
use Coosos\BidirectionalRelation\Tests\Model\News;

/**
 * Class DeserializeTest
 *
 * @package Coosos\BidirectionalRelation\Tests
 * @author  Remy Lescallier <lescallier1@gmail.com>
 */
class DeserializeTest extends AbstractTestCase
{
    /**
     * Test restore relation
     */
    public function testDeserialize()
    {
        $news = $this->generateExampleNews();
        $data = $this->jmsSerializer->serialize($news, 'json');

        /** @var News $newsDeserialize */
        $newsDeserialize = $this->jmsSerializer->deserialize($data, News::class, 'json');
        $this->assertNotEmpty($newsDeserialize->getNewsTranslations());
        foreach ($newsDeserialize->getNewsTranslations() as $newsTranslation) {
            $this->assertNotNull($newsTranslation->getNewsTranslatable());
            $this->assertSame(
                spl_object_hash($newsDeserialize),
                spl_object_hash($newsTranslation->getNewsTranslatable())
            );

            $this->assertNotNull($newsTranslation->getNewsTranslationImage());
            $this->assertNotNull($newsTranslation->getNewsTranslationImage()->getNewsTranslation());
            $this->assertSame(
                spl_object_hash($newsTranslation),
                spl_object_hash($newsTranslation->getNewsTranslationImage()->getNewsTranslation())
            );
        }
    }
}
