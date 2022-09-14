<?php

use Illuminate\Support\Collection;

require_once dirname(__DIR__).'/vendor/autoload.php';

$tests_dir = env('WP_TESTS_DIR', '/tmp/wordpress-tests-lib');

require_once "{$tests_dir}/includes/functions.php";

tests_add_filter('muplugins_loaded', function () {
	require_once dirname(__DIR__).'/wp-content/plugins/pressbooks/pressbooks.php';
	require_once dirname(__DIR__).'/wp-content/plugins/pressbooks/tests/utils-trait.php';
});

require_once "{$tests_dir}/includes/bootstrap.php";

// If this macro could be helpful elsewhere in the code, we could add this globally
Collection::macro('containsAll', function (array $values = []): bool {
	$items = (new Collection($values))->unique();

	return $this->intersect($values)->count() === $items->count();
});

Collection::macro('containsAny', function (array $values = []): bool {
	$values = (new Collection($values))->unique();

	return $this->intersect($values)->count() > 0;
});
