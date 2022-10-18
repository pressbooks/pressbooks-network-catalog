<?php

namespace PressbooksNetworkCatalog\Validators;

use Illuminate\Http\Request;
use PressbooksNetworkCatalog\Contracts\Validator;

class DateValidator implements Validator
{
	private array $values;

	public function validate($data): bool
	{
		return \is_string($data) && \strtotime($data) !== false && $this->extraRules($data);
	}

	private function extraRules($data): bool
	{
		$request = Request::capture();
		if (isset($this->values['greaterThanOrEqualTo']) && $request->get($this->values['greaterThanOrEqualTo'])) {
			return \strtotime($request->get($this->values['greaterThanOrEqualTo'])) <= \strtotime($data);
		}

		return true;
	}

	private function setValues(array $data): void
	{
		$this->values = $data;
	}

	public function rules(array $data): Validator
	{
		$this->setValues($data);

		return $this;
	}

	public function validateRange(string $from, string $to): bool
	{
		return $this->validate($from) && $this->validate($to) && \strtotime($from) <= \strtotime($to);
	}
}
