<?php

namespace Tests;

use ReflectionProperty;
use WP_UnitTestCase;

class TestCase extends WP_UnitTestCase
{
	/**
	 * Reset the given class singleton instance.
	 *
	 * @param string $className
	 * @return void
	 * @throws \ReflectionException
	 */
	protected function resetSingletonInstance(string $className): void
	{
		$property = new ReflectionProperty($className, 'instance');

		$property->setValue(null, null);
	}
}
