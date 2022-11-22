<?php

namespace PressbooksNetworkCatalog\Filters;

use Pressbooks\DataCollector\Book as DataCollector;
use PressbooksNetworkCatalog\Contracts\Filter;

class Publisher implements Filter
{
	public static function getPossibleValues(): array
	{
		$publishers = get_transient('pb-network-catalog-publishers');

		if ($publishers !== false) {
			return $publishers;
		}

		$codes = DataCollector::init()->getPossibleValuesFor(
			DataCollector::PUBLISHER,
			$in_catalog = true
		);

		$publishers = array_reduce($codes, function ($publishers, $key) {
			$publishers[$key] = $key;

			return $publishers;
		}, []);

		asort($publishers);

		set_transient('pb-network-catalog-publishers', $publishers, DAY_IN_SECONDS);

		return $publishers;
	}
}
