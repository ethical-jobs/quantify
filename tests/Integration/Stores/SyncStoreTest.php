<?php

namespace Tests\Integration\Stores;

use Illuminate\Support\Facades\Redis;
use EthicalJobs\Quantify\Stores\SyncStore;

class SyncStoreTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Integration
     */
    public function it_can_store_and_retrieve_items()
    {
        $store = new SyncStore;

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
    public function it_can_update_values()
    {
        $store = new SyncStore;

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
        $store = new SyncStore;

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
        $store = new SyncStore;

        $store->setBucket('life');

        $store->set('mamals', [
            'whales' => 14,
            'dogs' => 292,
            'mice' => 2212,
        ]);

        $store->setBucket('universe');

        $store->set('planets', [
            'earth' => 3,
            'venus' => 2,
            'jupiter' => 5,
        ]);

        $this->assertArraySubset($store->all(), [
            'planets' => [
                'earth' => 3,
                'venus' => 2,
                'jupiter' => 5,
            ]
        ]);

        $store->setBucket('life');

        $this->assertArraySubset($store->all(), [
            'mamals' => [
                'whales' => 14,
                'dogs' => 292,
                'mice' => 2212,
            ]
        ]);
    }

    /**
     * @test
     * @group Integration
     */
    public function it_can_remove_all_keys()
    {
        $store = new SyncStore;

        $store->set('planets', [
            'earth' => 3,
            'venus' => 2,
            'jupiter' => 5,
        ]);

        $store->flush();

        $this->assertEmpty($store->all());
    }
}
