<?php

namespace Tests;

use Illuminate\Http\Request;
use PressbooksNetworkCatalog\Books;
use PressbooksNetworkCatalog\BooksRequestManager;

class BooksRequestManagerTest extends TestCase
{
	/** @test */
	public function publication_date_select_and_date_condition_are_built_correctly()
	{
		// Build Books to get the fields configuration
		$books = new Books([]);

		// Ensure the SQL fields include FROM_UNIXTIME for publicationDate
		$sqlFields = $this->invokePrivateMethod($books, 'getSqlQueryFields');

		$this->assertTrue(strpos($sqlFields, 'FROM_UNIXTIME') !== false, 'publicationDate select should use FROM_UNIXTIME to handle unix timestamps');

		// Create a fake request that contains published_from
		$_GET = ['published_from' => '2020-01-01'];
		$request = Request::create('/', 'GET', $_GET);

		// Pull private fields from Books via reflection so we can pass them to BooksRequestManager
		$ref = new \ReflectionClass($books);
		$prop = $ref->getProperty('fields');
		$prop->setAccessible(true);
		$fields = $prop->getValue($books);

		$manager = new BooksRequestManager($fields, $request);

		$conditions = $manager->getSqlConditionsForCatalogQuery();

		$this->assertTrue(strpos($conditions, 'DATE(publicationDate)') !== false, 'Date conditions should target DATE(publicationDate)');
		$this->assertTrue(strpos($conditions, '2020-01-01') !== false, 'The provided published_from date should appear in the generated SQL condition');
	}

	private function invokePrivateMethod($object, string $method)
	{
		$ref = new \ReflectionClass($object);
		$m = $ref->getMethod($method);
		$m->setAccessible(true);

		return $m->invoke($object);
	}
}
