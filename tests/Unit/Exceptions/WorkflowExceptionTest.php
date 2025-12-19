<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use Src\Exceptions\WorkflowException;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour WorkflowException
 */
class WorkflowExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function testHttpCode(): void
    {
        $exception = new WorkflowException('Erreur workflow');
        $this->assertEquals(422, $exception->getHttpCode());
    }

    /**
     * @test
     */
    public function testErrorCode(): void
    {
        $exception = new WorkflowException();
        $this->assertEquals('WORKFLOW_ERROR', $exception->getErrorCode());
    }
}
