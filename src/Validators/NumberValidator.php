<?php

namespace PressbooksNetworkCatalog\Validators;

use PressbooksNetworkCatalog\Contracts\Validator;

class NumberValidator implements Validator
{
	public function validate($data): bool
	{
		return \is_numeric($data);
	}

	public function rules(array $data): Validator
	{
		return $this;
	}
}
