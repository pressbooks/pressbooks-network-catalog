<?php

namespace Tests\Validators;

use PressbooksNetworkCatalog\Validators\DateValidator;
use Tests\TestCase;

class DateValidatorTest extends TestCase
{
	/**
	 * @test
	 * @group validators
	 */
	public function it_validates_a_date(): void
	{
		$validator = (new DateValidator)->rules([]);

		$this->assertTrue($validator->validate('2022-01-31'));
		$this->assertFalse($validator->validate('not-a-real-date'));
	}
}
