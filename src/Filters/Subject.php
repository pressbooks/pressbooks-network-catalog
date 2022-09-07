<?php

namespace PressbooksNetworkCatalog\Filters;

use Pressbooks\DataCollector\Book as DataCollector;
use function Pressbooks\Metadata\get_subject_from_thema;
use PressbooksNetworkCatalog\Contracts\Filters;

class Subject implements Filters
{
	public static function getPossibleValues(): array
	{
		$subjects = get_transient('pb-network-catalog-subjects');

		if ($subjects) {
			return $subjects;
		}

		$codes = DataCollector::init()->getPossibleValuesFor(DataCollector::SUBJECTS_CODES);

		$subjects = array_reduce($codes, function ($subjects, $key) {
			$subjects[$key] = get_subject_from_thema($key, true);

			return $subjects;
		}, []);

		asort($subjects);

		set_transient('pb-network-catalog-subjects', $subjects, DAY_IN_SECONDS);

		return $subjects;
	}
}
