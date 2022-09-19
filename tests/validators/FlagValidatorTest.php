<?php

namespace Tests\Validators;

use PressbooksNetworkCatalog\Validators\FlagValidator;
use Tests\TestCase;

class FlagValidatorTest extends TestCase
{
	/**
	 * @test
	 * @group validators
	 */
	public function it_validates_a_flag(): void
	{
		$validator = (new FlagValidator)->rules([]);

		$this->assertTrue($validator->validate('1'));
		$this->assertTrue($validator->validate('0'));
		$this->assertFalse($validator->validate('not-a-real-flag'));
	}
}
