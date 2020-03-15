<?php

namespace Coosos\BidirectionalRelation\Tests\Serializer;

use Coosos\BidirectionalRelation\Tests\AbstractTestCase;

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
        $this->assertArrayNotHasKey('root,fieldExcludedFromMapping', $mapping);

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
}
