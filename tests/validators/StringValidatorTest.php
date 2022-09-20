<?php

namespace Tests\Validators;

use PressbooksNetworkCatalog\Validators\StringValidator;
use Tests\TestCase;

class StringValidatorTest extends TestCase
{
	/**
	 * @test
	 * @group validators
	 */
	public function it_validates_a_date(): void
	{
		$validator = (new StringValidator)->rules([]);

		$this->assertTrue($validator->validate('1'));
		$this->assertTrue($validator->validate('some random string'));
		$this->assertFalse($validator->validate(1));
	}
}
