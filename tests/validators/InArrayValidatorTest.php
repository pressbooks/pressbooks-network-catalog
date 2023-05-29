<?php

namespace Tests\Validators;

use PressbooksNetworkCatalog\Validators\InArrayValidator;
use Tests\TestCase;

class InArrayValidatorTest extends TestCase
{
	/**
	 * @test
	 * @group validators
	 */
	public function it_validates_values_are_allowed_for_a_given_field(): void
	{
		$validator = (new InArrayValidator)->rules([
			'field' => 'some-field',
			'allowedValues' => [
				'key-0' => 'value 0',
				'key-1' => 'value 1',
			],
		]);

		$this->assertTrue($validator->validate(['key-0', 'key-1']));
		$this->assertFalse($validator->validate(['key-2']));
	}

	/**
	 * @test
	 * @group validators
	 */
	public function it_validates_values_are_allowed_if_no_field_is_given(): void
	{
		$validator = (new InArrayValidator)->rules([
			'allowedValues' => [
				'key-0' => 'value 0',
				'key-1' => 'value 1',
				"key with fancy 'quotes'" => "value with fancy 'quotes'",
			],
		]);

		$this->assertTrue($validator->validate('key-0'));
		$this->assertFalse($validator->validate('key-2'));
		$this->assertTrue($validator->validate('key with fancy \'quotes\''));
	}

	/**
	 * @test
	 * @group validators
	 */
	public function it_fails_if_no_values_are_allowed(): void
	{
		$validator = (new InArrayValidator)->rules([
			'field' => 'some-field',
		]);

		$this->assertFalse($validator->validate(['key-0', 'key-1']));
	}
}
