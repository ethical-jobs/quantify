<?php

namespace Tests;

use EthicalJobs\Quantify\ServiceProvider;


abstract class TestCase extends \Orchestra\Testbench\TestCase
{
	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function getEnvironmentSetUp($app)
	{
		$app['config']->set('quantify.testing-disabled', false);
	}	

	/**
	 * Inject package service provider
	 * 
	 * @param  Application $app
	 * @return Array
	 */
	protected function getPackageProviders($app)
	{
	    return [
	    	ServiceProvider::class,
	    ];
	}
}