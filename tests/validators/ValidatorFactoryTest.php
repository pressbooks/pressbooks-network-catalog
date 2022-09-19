<?php

namespace Tests\Validators;

use InvalidArgumentException;
use PressbooksNetworkCatalog\Validators\DateValidator;
use PressbooksNetworkCatalog\Validators\FlagValidator;
use PressbooksNetworkCatalog\Validators\InArrayValidator;
use PressbooksNetworkCatalog\Validators\NumberValidator;
use PressbooksNetworkCatalog\Validators\StringValidator;
use PressbooksNetworkCatalog\Validators\ValidatorFactory;
use Tests\TestCase;

class ValidatorFactoryTest extends TestCase
{
	/**
	 * @test
	 * @group validators
	 */
	public function it_creates_a_string_validator_class(): void
	{
		$this->assertInstanceOf(StringValidator::class, ValidatorFactory::make('string'));
	}

	/**
	 * @test
	 * @group validators
	 */
	public function it_creates_an_in_array_validator_class(): void
	{
		$this->assertInstanceOf(InArrayValidator::class, ValidatorFactory::make('array'));
	}

	/**
	 * @test
	 * @group validators
	 */
	public function it_creates_a_number_validator_class(): void
	{
		$this->assertInstanceOf(NumberValidator::class, ValidatorFactory::make('number'));
	}

	/**
	 * @test
	 * @group validators
	 */
	public function it_creates_a_date_validator_class(): void
	{
		$this->assertInstanceOf(DateValidator::class, ValidatorFactory::make('date'));
	}

	/**
	 * @test
	 * @group validators
	 */
	public function it_creates_a_flag_validator_class(): void
	{
		$this->assertInstanceOf(FlagValidator::class, ValidatorFactory::make('flag'));
	}

	/**
	 * @test
	 * @group validators
	 */
	public function it_throws_an_exception_when_creating_a_non_existent_type(): void
	{
		$this->expectException(InvalidArgumentException::class);

		ValidatorFactory::make('not-a-real-validator');
	}
}
