<?php

namespace Tests\Integration\Stores;

use Illuminate\Support\Facades\Redis;
use EthicalJobs\Quantify\Stores\NullStore;

class NullStoreTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Integration
     */
    public function it_can_store_and_retrieve_items()
    {
        $store = new NullStore;

        $store->set('my-key', [
            'foo' => 'bar',
        ]);

        $this->assertEquals($store->get('my-key'), []);
    }

    /**
     * @test
     * @group Integration
     */
    public function it_can_update_values()
    {
        $store = new NullStore;

        $store->set('my-key', [
            'foo' => 'bar',
            'bar' => 'foo',
        ]);

        $store->update('my-key', [
            'foo' => 'foo',
            'big' => 'small',
        ]);

        $this->assertEquals($store->get('my-key'), []);
    }

    /**
     * @test
     * @group Integration
     */
    public function it_can_check_keys_existence()
    {
        $store = new NullStore;

        $store->set('my-key', [
            'foo' => 'bar',
            'bar' => 'foo',
        ]);

        $this->assertFalse($store->has('my-key'));

        $this->assertFalse($store->has('your-key'));
    }

    /**
     * @test
     * @group Integration
     */
    public function it_can_return_all_items_in_a_bucket()
    {
        $store = new NullStore;

        $store->setBucket('life');

        $store->set('mamals', [
            'whales' => 14,
            'dogs' => 292,
            'mice' => 2212,
        ]);

        $store->set('birds', [
            'seagulls' => 22,
            'magpies' => 11,
            'peewee' => 13,
        ]);

        $store->setBucket('universe');

        $store->set('planets', [
            'earth' => 3,
            'venus' => 2,
            'jupiter' => 5,
        ]);

        $this->assertArraySubset($store->all(), []);
    }

    /**
     * @test
     * @group Integration
     */
    public function it_can_remove_all_keys()
    {
        $store = new NullStore;

        $store->flush();

        $this->assertEmpty($store->all());
    }
}
