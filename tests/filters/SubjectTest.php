<?php

namespace Tests\Filters;

use Pressbooks\Book;
use Pressbooks\DataCollector\Book as DataCollector;
use Pressbooks\Metadata;
use PressbooksNetworkCatalog\Filters\Subject;
use Tests\TestCase;
use utilsTrait;

class SubjectTest extends TestCase
{
	use utilsTrait;

	protected DataCollector $collector;

	protected Metadata $metadata;

	public function setUp(): void
	{
		parent::setUp();

		$this->_book();

		$this->collector = new DataCollector;

		$this->metadata = new Metadata;
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_retrieves_possible_subject_values(): void
	{
		$this->assertEmpty(
			Subject::getPossibleValues()
		);

		update_site_meta( 1, DataCollector::SUBJECTS_CODES, 'AVRQ' );
		update_site_meta( 2, DataCollector::SUBJECTS_CODES, 'ABA' );

		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );
		update_site_meta( 2, DataCollector::IN_CATALOG, 0 );

		delete_transient('pb-network-catalog-subjects');

		$subjects = Subject::getPossibleValues();

		$expected = [
			'AVRQ' => 'Mechanical musical instruments',
		];

		$this->assertNotEmpty($subjects);

		$this->assertEquals($expected, $subjects);
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_caches_subjects_for_subsequent_queries(): void
	{
		$this->assertEmpty(get_transient('pb-network-catalog-subjects'));

		update_site_meta( 1, DataCollector::SUBJECTS_CODES, 'AVRQ' );
		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );

		Subject::getPossibleValues();

		$expected = [
			'AVRQ' => 'Mechanical musical instruments',
		];

		update_site_meta( 2, DataCollector::SUBJECTS_CODES, 'ABA' );
		update_site_meta( 2, DataCollector::IN_CATALOG, 1 );

		$this->assertNotEmpty(get_transient('pb-network-catalog-subjects'));

		$this->assertEquals($expected, get_transient('pb-network-catalog-subjects'));
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_does_not_query_subjects_when_there_are_cached_values(): void
	{
		update_site_meta( 1, DataCollector::SUBJECTS_CODES, 'AVRQ' );
		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );

		Subject::getPossibleValues();

		$expected = [
			'AVRQ' => 'Mechanical musical instruments',
		];

		update_site_meta( 2, DataCollector::SUBJECTS_CODES, 'ABA' );
		update_site_meta( 2, DataCollector::IN_CATALOG, 1 );

		$this->assertEquals($expected, Subject::getPossibleValues());
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_queries_subjects_when_cache_is_cleared(): void
	{
		update_site_meta( 1, DataCollector::SUBJECTS_CODES, 'AVRQ' );
		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );

		Subject::getPossibleValues();

		$expected = [
			'AVRQ' => 'Mechanical musical instruments',
			'ABA' => 'Theory of art',
		];

		update_site_meta( 2, DataCollector::SUBJECTS_CODES, 'ABA' );
		update_site_meta( 2, DataCollector::IN_CATALOG, 1 );

		delete_transient('pb-network-catalog-subjects');

		$this->assertEquals($expected, Subject::getPossibleValues());
	}
}
