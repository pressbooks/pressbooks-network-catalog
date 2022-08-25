<?php

namespace PressbooksNetworkCatalog\Filters;

use function Pressbooks\Metadata\get_institutions_flattened;
use PressbooksNetworkCatalog\Contracts\Filters;

class Institution implements Filters
{
	public static function getPossibleValues(): array
	{
		// TODO: we might want to add institutions to the blogmeta?
		$institutions = get_institutions_flattened();

		sort($institutions);

		return $institutions;
	}
}
