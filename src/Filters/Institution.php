<?php

namespace PressbooksNetworkCatalog\Filters;

use Pressbooks\DataCollector\Book as DataCollector;
use PressbooksNetworkCatalog\Contracts\Filter;

class Institution implements Filter
{
	public static function getPossibleValues(): array
	{
		$institutions = get_transient('pb-network-catalog-institutions');

		if ($institutions) {
			return $institutions;
		}

		$codes = DataCollector::init()->getPossibleValuesFor(DataCollector::INSTITUTIONS);

		$institutions = array_reduce($codes, function ($institutions, $key) {
			$institutions[$key] = $key;

			return $institutions;
		}, []);

		asort($institutions);

		set_transient('pb-network-catalog-institutions', $institutions, DAY_IN_SECONDS);

		return $institutions;
	}
}
