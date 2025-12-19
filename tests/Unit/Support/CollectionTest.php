<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Src\Support\Collection;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour Collection
 */
class CollectionTest extends TestCase
{
    /**
     * @test
     */
    public function testMakeCreatesCollection(): void
    {
        $collection = Collection::make([1, 2, 3]);
        $this->assertInstanceOf(Collection::class, $collection);
    }

    /**
     * @test
     */
    public function testAll(): void
    {
        $collection = new Collection([1, 2, 3]);
        $this->assertEquals([1, 2, 3], $collection->all());
    }

    /**
     * @test
     */
    public function testMap(): void
    {
        $collection = new Collection([1, 2, 3]);
        $result = $collection->map(fn($x) => $x * 2);
        $this->assertEquals([2, 4, 6], $result->all());
    }

    /**
     * @test
     */
    public function testFilter(): void
    {
        $collection = new Collection([1, 2, 3, 4]);
        $result = $collection->filter(fn($x) => $x > 2);
        $this->assertEquals([3, 4], array_values($result->all()));
    }

    /**
     * @test
     */
    public function testFirst(): void
    {
        $collection = new Collection([1, 2, 3]);
        $this->assertEquals(1, $collection->first());
    }

    /**
     * @test
     */
    public function testLast(): void
    {
        $collection = new Collection([1, 2, 3]);
        $this->assertEquals(3, $collection->last());
    }

    /**
     * @test
     */
    public function testCount(): void
    {
        $collection = new Collection([1, 2, 3]);
        $this->assertEquals(3, $collection->count());
    }

    /**
     * @test
     */
    public function testIsEmpty(): void
    {
        $empty = new Collection([]);
        $notEmpty = new Collection([1]);
        $this->assertTrue($empty->isEmpty());
        $this->assertFalse($notEmpty->isEmpty());
    }

    /**
     * @test
     */
    public function testSum(): void
    {
        $collection = new Collection([1, 2, 3, 4]);
        $this->assertEquals(10, $collection->sum());
    }

    /**
     * @test
     */
    public function testPluck(): void
    {
        $collection = new Collection([
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
        ]);
        $names = $collection->pluck('name');
        $this->assertEquals(['Alice', 'Bob'], $names->all());
    }
}
