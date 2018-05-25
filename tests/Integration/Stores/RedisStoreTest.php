<?php

namespace Tests\Integration\Stores;

use Illuminate\Support\Facades\Redis;
use Illuminate\Redis\RedisManager;
use EthicalJobs\Quantify\Stores\RedisStore;

class RedisStoreTest extends \Tests\TestCase
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
    public function it_can_store_and_retrieve_items()
    {
        $redis = resolve(RedisManager::class);

        $store = new RedisStore($redis);

        $store->set('my-key', [
            'foo' => 'bar',
        ]);

        $this->assertEquals($store->get('my-key'), [
            'foo' => 'bar',
        ]);
    }

    // /**
    //  * @test
    //  * @group Integration
    //  */
    // public function it_prefixes_its_storage_keys()
    // {
    //     Store::set('my-key', 'my-value');

    //     $this->assertEquals('my-value', Redis::get(Store::$prefix . 'my-key'));
    // }

    // /**
    //  * @test
    //  * @group Integration
    //  */
    // public function it_can_dynamically_call_redis_functions()
    // {
    //     Store::set('my-key', 10);

    //     Store::incr('my-key');

    //     $this->assertEquals(11, Store::get('my-key'));
    // }

    // /**
    //  * @test
    //  * @group Integration
    //  */
    // public function it_can_set_and_get_complex_values()
    // {
    //     Store::encodeSet('my-key', [
    //         'foo' => 'bar',
    //         'bar' => 'foo',
    //     ]);

    //     $this->assertEquals(Store::encodeGet('my-key'), [
    //         'foo' => 'bar',
    //         'bar' => 'foo',
    //     ]);
    // }

    // /**
    //  * @test
    //  * @group Integration
    //  */
    // public function it_can_get_all_stored_items()
    // {
    //     Store::set('birds:seagulls', 22);
    //     Store::set('birds:magpies', 11);
    //     Store::set('birds:peewee', 13);
    //     Store::set('mamals:whales', 5);
    //     Store::set('mamals:dogs', 533);
    //     Store::set('mamals:mice', 5933);

    //     $this->assertArraySubset(Store::all(), [
    //         'birds' => [
    //             'seagulls' => "22",
    //             'magpies' => "11",
    //             'peewee' => "13",
    //         ],
    //         'mamals' => [
    //             'whales' => "5",
    //             'dogs' => "533",
    //             'mice' => "5933",
    //         ],
    //     ]);
    // }

    // /**
    //  * @test
    //  * @group Integration
    //  */
    // public function it_can_get_all_stored_items_without_namespaces()
    // {
    //     Store::set('birds', 22);
    //     Store::set('birds', 11);
    //     Store::set('birds', 13);
    //     Store::set('mamals', 5);
    //     Store::set('mamals', 533);
    //     Store::set('mamals', 5933);

    //     $this->assertArraySubset(Store::all(), [
    //         'birds' => 13,
    //         'mamals' => 5933,
    //     ]);
    // }

    // /**
    //  * @test
    //  * @group Integration
    //  */
    // public function it_can_remove_all_keys()
    // {
    //     Store::set('my-key-01', 'value-01');
    //     Store::set('my-key-02', 'value-02');
    //     Store::set('my-key-03', 'value-03');
    //     Store::set('my-key-04', 'value-04');
    //     Store::set('my-key-05', 'value-05');

    //     Store::flush();

    //     $this->assertEmpty(Store::all());
    // }
}
