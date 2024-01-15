<?php

namespace Tests\Feature;

use Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = collect([1,2,3]);
        self::assertEqualsCanonicalizing([1,2,3], $collection->all());
    }
}
