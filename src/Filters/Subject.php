<?php

namespace PressbooksNetworkCatalog\Filters;

use Pressbooks\DataCollector\Book as DataCollector;
use PressbooksNetworkCatalog\Contracts\Filters;

class Subject implements Filters
{
	public static function getPossibleValues(): array
	{
		$values = get_transient('pb-network-catalog-subjects');

		if ($values) {
			return $values;
		}

		$subjects = DataCollector::init()->getPossibleValuesFor(DataCollector::SUBJECT);

		set_transient('pb-network-catalog-subjects', $subjects, DAY_IN_SECONDS);

		return $subjects;
	}
}
