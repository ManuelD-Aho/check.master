<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\WorkflowValidator;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour WorkflowValidator
 * 
 * Note: Ces tests utilisent des mocks car le validateur dépend de modèles DB
 */
class WorkflowValidatorTest extends TestCase
{
    private WorkflowValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new WorkflowValidator();
    }

    /**
     * @test
     */
    public function testGetErrorsRetourneTableau(): void
    {
        $errors = $this->validator->getErrors();
        $this->assertIsArray($errors);
    }

    /**
     * @test
     */
    public function testGetFirstErrorRetourneNullAuDepart(): void
    {
        $this->assertNull($this->validator->getFirstError());
    }

    /**
     * @test
     * Note: Test basique - les tests complets nécessitent des mocks de DossierEtudiant
     */
    public function testValidatorInstantiation(): void
    {
        $this->assertInstanceOf(WorkflowValidator::class, $this->validator);
    }
}
