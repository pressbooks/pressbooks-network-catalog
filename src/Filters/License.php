<?php

namespace PressbooksNetworkCatalog\Filters;

use Pressbooks\DataCollector\Book as DataCollector;
use Pressbooks\Licensing;
use PressbooksNetworkCatalog\Contracts\Filters;

class License implements Filters
{
	public static function getPossibleValues(): array
	{
		$values = get_transient('pb-network-catalog-licenses');

		if ($values) {
			return $values;
		}

		$supportedLicenses = (new Licensing)->getSupportedTypes();
		$currentLicenses = DataCollector::init()->getPossibleValuesFor(DataCollector::LICENSE);

		$licenses = array_reduce($currentLicenses, function ($carry, $key) use ($supportedLicenses) {
			$license = $supportedLicenses[$key] ?? ['desc' => strtoupper($key)];

			$carry[$key] = $license['desc'];

			return $carry;
		}, []);

		ksort($licenses);

		set_transient('pb-network-catalog-licenses', $licenses, DAY_IN_SECONDS);

		return $licenses;
	}
}
