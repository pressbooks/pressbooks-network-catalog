<?php

namespace PressbooksNetworkCatalog\Validators;

use PressbooksNetworkCatalog\Contracts\Validator;

class InArrayValidator implements Validator
{
	private array $values;

	public function getValues() : array
	{
		return $this->values[$this->values['field']];
	}

	private function setValues(array $data): void
	{
		$this->values = $data;
	}

	public function validate($data): bool
	{
		return \in_array($data, $this->getValues(), true);
	}

	public function rules(array $data): Validator
	{
		$this->setValues($data);

		return $this;
	}
}
