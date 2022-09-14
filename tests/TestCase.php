<?php

namespace Tests;

use ReflectionClass;
use WP_UnitTestCase;

class TestCase extends WP_UnitTestCase
{
	protected function invalidateSingletonInstance(string $className): void
	{
		$class = new ReflectionClass($className);

		$instance = $class->getProperty('instance');

		$instance->setAccessible(true);
		$instance->setValue(null);
		$instance->setAccessible(false);
	}
}
