<?php

namespace PressbooksNetworkCatalog\Contracts;

interface Validator
{
	public function validate($data): bool;

	public function rules(array $data): self;
}
