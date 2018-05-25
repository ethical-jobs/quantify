<?php

namespace Tests;


abstract class TestCase extends \Orchestra\Testbench\TestCase
{
	/**
	 * Inject package service provider
	 * 
	 * @param  Application $app
	 * @return Array
	 */
	protected function getPackageProviders($app)
	{
	    return [];
	}
}