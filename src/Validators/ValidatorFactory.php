<?php

namespace PressbooksNetworkCatalog\Validators;

use PressbooksNetworkCatalog\Contracts\Validator;

class ValidatorFactory
{
	public static function make(string $type): Validator
	{
		switch ($type) {
			case 'string':
				return new StringValidator();
			case 'date':
				return new DateValidator();
			case 'flag':
				return new FlagValidator();
			case 'array':
				return new InArrayValidator();
			case 'number':
				return new NumberValidator();
			default:
				throw new \InvalidArgumentException('Invalid validator type');
		}
	}
}
