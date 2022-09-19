<?php

namespace PressbooksNetworkCatalog\Validators;

use PressbooksNetworkCatalog\Contracts\Validator;

class StringValidator implements Validator
{
	public function validate($data): bool
	{
		return \is_string($data);
	}

	public function rules(array $data): Validator
	{
		return $this;
	}
}
