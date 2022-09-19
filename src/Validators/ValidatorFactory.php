<?php

namespace PressbooksNetworkCatalog\Validators;

use PressbooksNetworkCatalog\Contracts\Validator;

class ValidatorFactory
{
	protected static array $validators = [
		'string' => StringValidator::class,
		'array' => InArrayValidator::class,
		'number' => NumberValidator::class,
		'date' => DateValidator::class,
		'flag' => FlagValidator::class,
	];

	public static function make(string $type): Validator
	{
		$class = static::$validators[$type] ?? null;

		if (! $class) {
			throw new \InvalidArgumentException('Invalid validator');
		}

		return new $class();
	}
}
