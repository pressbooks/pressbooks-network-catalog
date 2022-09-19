<?php

namespace Tests\Validators;

use PressbooksNetworkCatalog\Validators\NumberValidator;
use Tests\TestCase;

class NumberValidatorTest extends TestCase
{
	/**
	 * @test
	 * @group validators
	 */
	public function it_validates_a_date(): void
	{
		$validator = (new NumberValidator)->rules([]);

		$this->assertTrue($validator->validate('1'));
		$this->assertTrue($validator->validate(1));
		$this->assertFalse($validator->validate('not-a-real-number'));
	}
}
