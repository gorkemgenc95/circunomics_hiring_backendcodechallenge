<?php

namespace Tests\Unit\Models;

use App\Models\Commit;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class CommitTest extends TestCase
{
    private Commit $commit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commit = new Commit();
    }

    public function testTableNameIsCorrect(): void
    {
        $this->assertEquals('commits', $this->commit->getTable());
    }

    public function testFillableAttributesAreSetCorrectly(): void
    {
        $expectedFillable = [
            'hash',
            'author',
            'date',
            'repository_owner',
            'repository_name',
            'platform',
            'message',
        ];

        $this->assertEquals($expectedFillable, $this->commit->getFillable());
    }

    public function testDateCastingIsConfigured(): void
    {
        $casts = $this->commit->getCasts();
        $this->assertArrayHasKey('date', $casts);
        $this->assertEquals('datetime', $casts['date']);
    }

    public function testToApiFormatMethodExists(): void
    {
        $this->assertTrue(method_exists($this->commit, 'toApiFormat'));

        $reflection = new ReflectionMethod($this->commit, 'toApiFormat');
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    public function testForRepositoryScopeMethodExists(): void
    {
        $this->assertTrue(method_exists($this->commit, 'scopeForRepository'));

        $reflection = new ReflectionMethod($this->commit, 'scopeForRepository');
        $parameters = $reflection->getParameters();

        $this->assertCount(4, $parameters);
        $this->assertEquals('query', $parameters[0]->getName());
        $this->assertEquals('platform', $parameters[1]->getName());
        $this->assertEquals('owner', $parameters[2]->getName());
        $this->assertEquals('repo', $parameters[3]->getName());
    }

    public function testMostRecentScopeMethodExists(): void
    {
        $this->assertTrue(method_exists($this->commit, 'scopeMostRecent'));

        $reflection = new ReflectionMethod($this->commit, 'scopeMostRecent');
        $parameters = $reflection->getParameters();

        $this->assertCount(2, $parameters);
        $this->assertEquals('query', $parameters[0]->getName());
        $this->assertEquals('limit', $parameters[1]->getName());

        $this->assertTrue($parameters[1]->isDefaultValueAvailable());
        $this->assertEquals(1000, $parameters[1]->getDefaultValue());
    }
}
