<?php

namespace PressbooksNetworkCatalog\Validators;

use PressbooksNetworkCatalog\Contracts\Validator;

class DateValidator implements Validator
{
	public function validate($data): bool
	{
		return \is_string($data) && \strtotime($data) !== false;
	}

	public function rules(array $data): Validator
	{
		return $this;
	}
}
