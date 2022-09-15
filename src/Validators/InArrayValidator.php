<?php

namespace PressbooksNetworkCatalog\Validators;

use PressbooksNetworkCatalog\Contracts\Validator;

class InArrayValidator implements Validator
{
	private array $values;

	/**
	 * This method will look up in allowedValues array keys for the allowed value
	 * if not will try to look up in the extra parameters like subjects, licenses, etc.
	 * @return array
	 */
	public function getValues() : array
	{
		if (isset($this->values['allowedValues']) || array_key_exists('field', $this->values)) {
			return isset($this->values['allowedValues']) ? array_keys($this->values['allowedValues']) : array_keys($this->values[$this->values['field']]);
		}

		return [];
	}

	private function setValues(array $data): void
	{
		$this->values = $data;
	}

	public function validate($data): bool
	{
		// Returns true only if all the values are in the allowed values
		if (array_key_exists('field', $this->values)) {
			return array_diff($data, $this->getValues()) === [];
		}

		return \in_array($data, $this->getValues(), true);
	}

	public function rules(array $data): Validator
	{
		$this->setValues($data);

		return $this;
	}
}
