<?php

namespace Tests\Integration\Reporters;

use Illuminate\Support\Facades\Redis;
use EthicalJobs\Quantify\Reporters\Metrics;

class MetricsTest extends \Tests\TestCase
{
    /**
     * Post test execution
     *
     * @return void
     */
    public function tearDown()
    {
        Redis::FLUSHALL();
    }

   /**
     * @test
     * @group Integration
     */
    public function it_has_correct_bucket_name()
    {
    	$this->assertTrue(
    		Metrics::getBucket() === 'metrics'
    	);
    }        

    /**
     * @test
     * @group Integration
     */
    public function it_always_returns_an_array()
    {
    	$reporter = resolve(Metrics::class);

    	$this->assertTrue(is_array($reporter->report()));

    	$reporter->start('my-operation');

    	$reporter->complete('my-operation');

    	$this->assertTrue(is_array($reporter->report()));
    }   


    /**
     * @test
     * @group Integration
     */
    public function it_measures_correct_metrics()
    {
    	$reporter = resolve(Metrics::class);

    	$reporter->start('my-operation');

    	$reporter->complete('my-operation');

    	$this->assertTrue(array_has($reporter->report()[0], [
    		'metric', 'count', 'total-time', 'average-time',
    	]));
    }       

    /**
     * @test
     * @group Integration
     */
    public function it_can_measure_average_time_elapsed()
    {
    	$reporter = resolve(Metrics::class);

    	$reporter->start('my-operation');
    	usleep(50000);
    	$reporter->complete('my-operation');

    	$reporter->start('my-operation');
    	usleep(20000);
    	$reporter->complete('my-operation');

    	$reporter->start('my-operation');
    	usleep(30000);
    	$reporter->complete('my-operation');    	    	

    	$report = $reporter->report();

    	$average = $report[0]['average-time'];

    	$this->assertTrue(
    		is_float($average)
    	);

    }        

   /**
     * @test
     * @group Integration
     */
    public function it_can_measure_total_time_elapsed()
    {
    	$reporter = resolve(Metrics::class);

    	$reporter->start('my-operation');
    	usleep(5000);
    	$reporter->complete('my-operation');

    	$reporter->start('my-operation');
    	usleep(5000);
    	$reporter->complete('my-operation');

    	$report = $reporter->report();

    	$total = $report[0]['total-time'];

    	$this->assertTrue(
            is_float($total)
    	);

    }       

   /**
     * @test
     * @group Integration
     */
    public function it_can_measures_total_times_metric_was_recorded()
    {
    	$reporter = resolve(Metrics::class);

    	$reporter->start('my-operation');
    	$reporter->complete('my-operation');

    	$reporter->start('my-operation');
    	$reporter->complete('my-operation');

        $reporter->start('my-operation');
    	$reporter->complete('my-operation');	

    	$report = $reporter->report();

    	$count = $report[0]['count'];

    	$this->assertTrue(
    		$count === 3
    	);

    }          
}
