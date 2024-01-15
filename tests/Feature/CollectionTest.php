<?php

namespace Tests\Feature;

use App\Data\Person;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertEqualsCanonicalizing;
use function PHPUnit\Framework\assertTrue;

class CollectionTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = collect([1,2,3]);
        self::assertEqualsCanonicalizing([1,2,3], $collection->all());
    }

    public function testForEach()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        foreach ($collection as $key => $value) {
            self::assertEquals($key+1, $value);
        }
    }

    public function testCrud()
    {
        $collection = collect([]);
        $collection->push(1,2,3);
        self::assertEqualsCanonicalizing([1,2,3], $collection->all());

        $result = $collection->pop();
        assertEquals(3, $result);
        assertEqualsCanonicalizing([1,2], $collection->all());
    }

    public function testMap()
    {
        $collection = collect([1,2,3]);
        $result = $collection->map(function ($item){
            return $item * 2;
        });

        assertEquals([2,4,6], $result->all());
    }

    public function testMapInto()
    {
        $collection = collect(["Ahzi"]);
        $result = $collection->mapInto(Person::class);
        assertEquals([new Person("Ahzi")], $result->all());
    }

    public function testMapSpread()
    {
        $collection = collect([["Ahmad", "Fauzi"], ["Ah", "Zi"]]);
        $result = $collection->mapSpread(function ($firstName, $lastName){
            $fullName = $firstName . " " . $lastName;
            return new Person($fullName);
        });
        assertEquals([
            new Person("Ahmad Fauzi"),
            new Person("Ah Zi")
        ], $result->all());
    }

    public function testMapToGroup()
    {
        $collection = collect([
            [
                "name" => "Fauzi",
                "department" => "IT"
            ],
            [
                "name" => "Ahzi",
                "department" => "IT"
            ],
            [
                "name" => "Budi",
                "department" => "HR"
            ]
        ]);

        $result = $collection->mapToGroups(function ($item){
            return [$item["department"] => $item["name"]];
        });

        assertEquals([
            "IT" => collect(["Fauzi", "Ahzi"]),
            "HR" => collect(["Budi"])
        ], $result->all());
    }

    public function testZip()
    {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4,5,6]);
        $collection3 = $collection1->zip($collection2);

        assertEquals([
            collect([1,4]),
            collect([2,5]),
            collect([3,6])
        ], $collection3->all());
    }

    public function testConcat()
    {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4,5,6]);
        $collection3 = $collection1->concat($collection2);

        assertEquals([1,2,3,4,5,6], $collection3->all());
    }

    public function testCombine()
    {
        $collection1 = collect(["name", "country"]);
        $collection2 = collect(["Ahzi", "Indonesia"]);
        $collection3 = collect($collection1->combine($collection2));

        assertEquals([
            "name" => "Ahzi",
            "country" => "Indonesia"
        ], $collection3->all());
    }

    public function testCollapse()
    {
        $collection = collect([
            [1,2,3],
            [4,5,6],
            [7,8,9]
        ]);
        $result = $collection->collapse();

        assertEquals([1,2,3,4,5,6,7,8,9], $result->all());
    }

    public function testFlatMap()
    {
        $collection = collect([
            [
                "name" => "Ahzi",
                "hobbies" => ["Coding", "Gaming"]
            ],
            [
                "name" => "Fau",
                "hobbies" => ["Reading", "Writing"]
            ]
        ]);

        $hobbies = $collection->flatMap(function ($item){
            return $item["hobbies"];
        });

        assertEquals(["Coding", "Gaming","Reading", "Writing"], $hobbies->all());
    }

    public function testJoin()
    {
        $collection = collect(["Ahmad", "Fauzi", "Ahzi"]);

        assertEquals("Ahmad-Fauzi-Ahzi", $collection->join("-"));
        assertEquals("Ahmad-Fauzi_Ahzi", $collection->join("-", "_"));
    }

    public function testFilter()
    {
        $collection = collect([
            "Ahzi" => 100,
            "Budi" => 80,
            "Joko" => 90
        ]);
        $result = $collection->filter(function ($item, $key) {
            return $item >= 90;
        });

        assertEquals([
            "Ahzi" => 100,
            "Joko" => 90
        ], $result->all());
    }

    public function testFilterIndex()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);
        $result = $collection->filter(function ($value, $key){
            return $value % 2 == 0;
        });

        assertEqualsCanonicalizing([2,4,6,8,10], $result->all());
    }

    public function testPartitioning()
    {
        $collection = collect([
            "Ahzi" => 100,
            "Budi" => 80,
            "Joko" => 90
        ]);
        [$result1, $result2] = $collection->partition(function ($item, $key){
            return $item >= 90;
        });

        self::assertEquals(["Ahzi" => 100, "Joko" => 90], $result1->all());
        self::assertEquals(["Budi" => 80], $result2->all());
    }

    public function testTesting()
    {
        $collection = collect(["Ahmad", "Fauzi", "Ahzi"]);
        assertTrue($collection->contains("Ahzi"));
        assertTrue($collection->contains(function ($value, $key){
            return $value = "Ahmad";
        }));
    }
}
