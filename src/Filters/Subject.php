<?php

namespace PressbooksNetworkCatalog\Filters;

use Pressbooks\DataCollector\Book as DataCollector;
use PressbooksNetworkCatalog\Contracts\Filters;

class Subject implements Filters
{
	public static function getPossibleValues(): array
	{
		$subjects = get_transient('pb-network-catalog-subjects');

		if ($subjects) {
			return $subjects;
		}

		$subjects = DataCollector::init()->getPossibleValuesFor(DataCollector::SUBJECTS_STRINGS);

		asort($subjects);

		set_transient('pb-network-catalog-subjects', $subjects, DAY_IN_SECONDS);

		return $subjects;
	}
}
