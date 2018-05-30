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

    /**
     * @test
     * @group Integration
     */
    public function it_prefixes_its_storage_keys_without()
    {
        $redis = resolve(RedisManager::class);

        $store = new RedisStore($redis);

        $store->set('foo', ['foo' => 1]);

        $keys = Redis::keys('*');

        $key = array_shift($keys);

        $this->assertEquals('ej:quantify:foo', $key);
    }

    /**
     * @test
     * @group Integration
     */
    public function it_prefixes_its_storage_keys_with()
    {
        $store->setBucket('my-bucket');

        $store->set('bar', ['bar' => 1]);

        $keys = Redis::keys('*');

        $key = array_shift($keys);

        $this->assertEquals('ej:quantify:my-bucket:bar', $key);
    }    

    /**
     * @test
     * @group Integration
     */
    public function it_can_update_values()
    {
        $redis = resolve(RedisManager::class);

        $store = new RedisStore($redis);

        $store->set('my-key', [
            'foo' => 'bar',
            'bar' => 'foo',
        ]);

        $store->update('my-key', [
            'foo' => 'foo',
            'big' => 'small',
        ]);        

        $this->assertEquals($store->get('my-key'), [
            'foo' => 'foo',
            'bar' => 'foo',
            'big' => 'small',
        ]);
    }

    /**
     * @test
     * @group Integration
     */
    public function it_can_check_keys_existence()
    {
        $redis = resolve(RedisManager::class);

        $store = new RedisStore($redis);

        $store->set('my-key', [
            'foo' => 'bar',
            'bar' => 'foo',
        ]);

        $this->assertTrue($store->has('my-key'));        

        $this->assertFalse($store->has('your-key')); 
    }    

    /**
     * @test
     * @group Integration
     */
    public function it_can_return_all_items_in_a_bucket()
    {
        $redis = resolve(RedisManager::class);

        $store = new RedisStore($redis);

        $store->setBucket('life');

        $store->set('mamals', [
            'whales'    => 14,
            'dogs'      => 292,
            'mice'      => 2212,
        ]);

        $store->set('birds', [
            'seagulls'  => 22,
            'magpies'   => 11,
            'peewee'    => 13,
        ]);      

        $store->setBucket('universe');   

        $store->set('planets', [
            'earth'     => 3,
            'venus'     => 2,
            'jupiter'   => 5,
        ]);                

        $this->assertArraySubset($store->all(), [
            [
                'earth'     => 3,
                'venus'     => 2,
                'jupiter'   => 5,
            ]            
        ]);
    }    

    /**
     * @test
     * @group Integration
     */
    public function it_can_remove_all_keys()
    {
        $redis = resolve(RedisManager::class);

        $redis->set('dont:delete', 1983);

        $store = new RedisStore($redis);

        $store->setBucket('life');

        $store->set('mamals', [
            'whales'    => 14,
            'dogs'      => 292,
            'mice'      => 2212,
        ]);

        $store->set('birds', [
            'seagulls'  => 22,
            'magpies'   => 11,
            'peewee'    => 13,
        ]);      

        $store->setBucket('universe');   

        $store->set('planets', [
            'earth'     => 3,
            'venus'     => 2,
            'jupiter'   => 5,
        ]);    

        $store->flush();

        $this->assertEmpty($store->all());

        $store->setBucket('life');

        $this->assertNotEmpty($store->all());

        $store->flush();

        $this->assertEmpty($store->all());

        $this->assertEquals(1983, $redis->get('dont:delete'));
    }
}
