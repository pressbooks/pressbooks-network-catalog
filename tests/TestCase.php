<?php

namespace Tests;

use ReflectionClass;
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
		$class = new ReflectionClass($className);

		$instance = $class->getProperty('instance');

		$instance->setAccessible(true);
		$instance->setValue(null);
		$instance->setAccessible(false);
	}
}
