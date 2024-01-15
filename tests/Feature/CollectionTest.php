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

    public function testGrouping()
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

        $result = $collection->groupBy("department");
        assertEquals([
            "IT" => collect([
                [
                    "name" => "Fauzi",
                    "department" => "IT"
                ],
                [
                    "name" => "Ahzi",
                    "department" => "IT"
                ],
            ]),
            "HR" => collect([
                [
                    "name" => "Budi",
                    "department" => "HR"
                ]
            ])
        ], $result->all());

        self::assertEquals([
            "IT" => collect([
                [
                    "name" => "Fauzi",
                    "department" => "IT"
                ],
                [
                    "name" => "Ahzi",
                    "department" => "IT"
                ],
            ]),
            "HR" => collect([
                [
                    "name" => "Budi",
                    "department" => "HR"
                ]
            ])
        ], $collection->groupBy(function ($value, $key){
            return $value["department"];
        })->all());
    }

    public function testSlice()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->slice(3);
        assertEqualsCanonicalizing([4,5,6,7,8,9], $result->all());

        $result = $collection->slice(3,2);
        assertEqualsCanonicalizing([4,5], $result->all());
    }

    public function testTake()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->take(3);
        assertEqualsCanonicalizing([1,2,3], $result->all());

        $result = $collection->takeUntil(function ($value, $key){
            return $value == 3;
        });
        assertEqualsCanonicalizing([1,2], $result->all());

        $result = $collection->takeWhile(function ($value, $key){
            return $value < 3;
        });
        self::assertEqualsCanonicalizing([1,2], $result->all());
    }

    public function testSkip()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->skip(3);
        assertEqualsCanonicalizing([4,5,6,7,8,9], $result->all());

        $result = $collection->skipUntil(function ($value, $key){
            return $value == 3;
        });
        self::assertEqualsCanonicalizing([3,4,5,6,7,8,9], $result->all());

        $result = $collection->skipWhile(function ($value, $key){
            return $value < 3;
        });
        assertEqualsCanonicalizing([3,4,5,6,7,8,9], $result->all());
    }

    public function testChunked()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);
        $result = $collection->chunk(3);

        assertEqualsCanonicalizing([1,2,3], $result->all()[0]->all());
        assertEqualsCanonicalizing([4,5,6], $result->all()[1]->all());
        assertEqualsCanonicalizing([7,8,9], $result->all()[2]->all());
        assertEqualsCanonicalizing([10], $result->all()[3]->all());

    }

    public function testFirst()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->first();
        self::assertEquals(1, $result);

        $result = $collection->first(function ($value, $key){
            return $value > 5;
        });
        assertEquals(6, $result);
    }

    public function testLast()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->last();
        self::assertEquals(9, $result);

        $result = $collection->last(function ($value, $key){
            return $value < 5;
        });
        assertEquals(4, $result);
    }

    public function testRandom()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->random();
        assertTrue(in_array($result, [1,2,3,4,5,6,7,8,9]));
    }

    public function testCheckingExistence()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        assertTrue($collection->isNotEmpty());
        self::assertFalse($collection->isEmpty());
        assertTrue($collection->contains(8));
        self::assertFalse($collection->contains(10));
        self::assertTrue($collection->contains(function ($value, $key){
            return $value == 8;
            }));
    }

    public function testOrdering()
    {
        $collection = collect([1,4,2,3,5,6,8,7,9]);
        $result = $collection->sort();
        assertEqualsCanonicalizing([1,2,3,4,5,6,7,8,9], $result->all());

        $result = $collection->sortDesc();
        self::assertEqualsCanonicalizing([9,8,7,6,5,4,3,2,1], $result->all());
    }

    public function testAggregate()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->sum();
        assertEquals(45, $result);

        $result = $collection->avg();
        assertEquals(5, $result);

        $result = $collection->min();
        assertEquals(1,$result);

        $result = $collection->max();
        assertEquals(9, $result);
    }

    public function testReduce()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->reduce(function ($carry, $item) {
            return $carry + $item;
        });
        assertEquals(45,$result);
    }
}
