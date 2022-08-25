<?php

namespace PressbooksNetworkCatalog\Filters;

use Pressbooks\DataCollector\Book as DataCollector;
use PressbooksNetworkCatalog\Contracts\Filters;

class Subject implements Filters
{
	public static function getPossibleValues(): array
	{
		return DataCollector::init()->getPossibleValuesFor(DataCollector::SUBJECT);
	}
}
