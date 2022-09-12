<?php

$tests_dir = env('WP_TESTS_DIR', '/tmp/wordpress-tests-lib');

require_once "{$tests_dir}/includes/functions.php";

function _manually_load_plugin()
{
	require_once dirname(__DIR__).'/../pressbooks/pressbooks.php';
	require_once dirname(__DIR__).'/../pressbooks/tests/utils-trait.php';
}

tests_add_filter('muplugins_loaded', '_manually_load_plugin');

require_once "{$tests_dir}/includes/bootstrap.php";
