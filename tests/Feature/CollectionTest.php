<?php

namespace Tests\Feature;

use App\Data\Person;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertEqualsCanonicalizing;

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
}
