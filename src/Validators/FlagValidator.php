<?php

namespace PressbooksNetworkCatalog\Validators;

use PressbooksNetworkCatalog\Contracts\Validator;

class FlagValidator implements Validator
{
	public function validate($data): bool
	{
		return $data === '1' || $data === '0';
	}

	public function rules(array $data): Validator
	{
		return $this;
	}
}
