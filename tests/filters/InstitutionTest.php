<?php

namespace Tests\Filters;

use Pressbooks\DataCollector\Book as DataCollector;
use PressbooksNetworkCatalog\Filters\Institution;
use Tests\TestCase;
use utilsTrait;

class InstitutionTest extends TestCase
{
	use utilsTrait;

	/**
	 * @test
	 * @group filters
	 */
	public function it_retrieves_possible_institution_values(): void
	{
		$this->assertEmpty(
			Institution::getPossibleValues()
		);

		update_site_meta( 1, DataCollector::INSTITUTIONS, 'Algoma University' );
		update_site_meta( 2, DataCollector::INSTITUTIONS, 'Algonquin College' );

		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );
		update_site_meta( 2, DataCollector::IN_CATALOG, 0 );

		delete_transient('pb-network-catalog-institutions');

		$institutions = Institution::getPossibleValues();

		$expected = [
			'Algoma University' => 'Algoma University',
		];

		$this->assertNotEmpty($institutions);

		$this->assertEquals($expected, $institutions);
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_caches_institutions_for_subsequent_queries(): void
	{
		$this->assertEmpty(get_transient('pb-network-catalog-institutions'));

		update_site_meta( 1, DataCollector::INSTITUTIONS, 'Algoma University' );
		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );

		Institution::getPossibleValues();

		$expected = [
			'Algoma University' => 'Algoma University',
		];

		$this->assertNotEmpty(get_transient('pb-network-catalog-institutions'));

		$this->assertEquals($expected, get_transient('pb-network-catalog-institutions'));
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_does_not_query_institutions_when_there_are_cached_values(): void
	{
		update_site_meta( 1, DataCollector::INSTITUTIONS, 'Algoma University' );
		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );

		Institution::getPossibleValues();

		$expected = [
			'Algoma University' => 'Algoma University',
		];

		update_site_meta( 2, DataCollector::INSTITUTIONS, 'Algonquin College' );
		update_site_meta( 2, DataCollector::IN_CATALOG, 1 );

		$this->assertEquals($expected, Institution::getPossibleValues());
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_queries_institutions_when_cache_is_cleared(): void
	{
		update_site_meta( 1, DataCollector::INSTITUTIONS, 'Algoma University' );
		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );

		Institution::getPossibleValues();

		$expected = [
			'Algoma University' => 'Algoma University',
			'Algonquin College' => 'Algonquin College',
		];

		update_site_meta( 2, DataCollector::INSTITUTIONS, 'Algonquin College' );
		update_site_meta( 2, DataCollector::IN_CATALOG, 1 );

		delete_transient('pb-network-catalog-institutions');

		$this->assertEquals($expected, Institution::getPossibleValues());
	}
}
