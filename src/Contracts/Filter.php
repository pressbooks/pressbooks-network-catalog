<?php

namespace PressbooksNetworkCatalog\Contracts;

interface Filter
{
	public static function getPossibleValues(): array;
}
