<?php

namespace Tests\Repositories;

use Tests\TestCase;
use Mockery as m;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\DatabaseManager;
use App\Manufacturer;
use App\Http\Repositories\BrandRepository;

/**
 * @covers \App\Http\Repositories\BrandRepository
*/
class BrandRepositoryTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->dbMock = m::mock(DatabaseManager::class);
        $this->builderMock = m::mock(Builder::class);
        $this->manufacturerMock = m::mock(Manufacturer::class);


        $this->repository = new BrandRepository(
            $this->dbMock,
            $this->manufacturerMock
        );
    }

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    /**
     * @test
     * @covers \App\Http\Repositories\BrandRepository::getManufacturerList()
     */
    public function test_get_manufacturer_list()
    {
        $params = [
            [
                'key' => 'name',
                'value' => ['Alita']
            ],
            [
                'key' => 'sp_id_manufacturer',
                'value' => 102
            ],
            [
                'key' => 'id_ns_brand',
                'value' => 171
            ]
        ];

        $order = [
            'key' => 'id_manufacturer',
            'value' => 'DESC'
        ];

        $this->dbMock->shouldReceive('table')->with('manufacturer')->once()->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('leftJoin')->once()->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('where')->times(3)->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('whereIn')->once()->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('orderBy')->once()->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('select')->once()->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('paginate')->once()->andReturn($this->builderMock);

        $this->repository->getManufacturerList($params, $order);
    }

    /**
     * @test
     * @covers \App\Http\Repositories\BrandRepository::queryResult()
     */
    public function test_query_result_empty_order()
    {
        $params = [
            [
                'key' => 'name',
                'value' => ['Alita']
            ],
            [
                'key' => 'sp_id_manufacturer',
                'value' => 102
            ],
            [
                'key' => 'id_ns_brand',
                'value' => 171
            ]
        ];

        $order = [];

        $this->dbMock->shouldReceive('table')->with('manufacturer')->once()->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('leftJoin')->once()->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('where')->times(3)->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('whereIn')->once()->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('orderBy')->once()->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('select')->once()->andReturn($this->builderMock);

        $this->repository->queryResult($params, $order);
    }

    /**
     * @test
     * @covers \App\Http\Repositories\BrandRepository::queryResult()
     */
    public function test_query_result()
    {
        $params = [
            [
                'key' => 'name',
                'value' => ['Alita']
            ],
            [
                'key' => 'sp_id_manufacturer',
                'value' => 102
            ],
            [
                'key' => 'id_ns_brand',
                'value' => 171
            ]
        ];

        $order = [
            'key' => 'id_manufacturer',
            'value' => 'DESC'
        ];

        $this->dbMock->shouldReceive('table')->with('manufacturer')->once()->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('leftJoin')->once()->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('where')->times(3)->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('whereIn')->once()->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('orderBy')->once()->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('select')->once()->andReturn($this->builderMock);

        $this->repository->queryResult($params, $order);
    }

    /**
     * @test
     * @covers \App\Http\Repositories\BrandRepository::explodeAndCheckSelectField()
     */
    public function test_explode_and_check_select_field()
    {
        $selectField = [
            'manufacturer.id_manufacturer',
            'manufacturer.name',
            'manufacturer.sp_id_manufacturer',
            'manufacturer.id_ns_brand',
            'label.name AS brand_label'
        ];

        $filterWord = 'name';

        $this->repository->explodeAndCheckSelectField($selectField, $filterWord);
    }

    /**
     * @test
     * @covers \App\Http\Repositories\BrandRepository::explodeAndCheckSelectField()
     */
    public function test_explode_and_check_select_field_null_params()
    {
        $selectField = [];

        $filterWord = 'name';

        $this->repository->explodeAndCheckSelectField($selectField, $filterWord);
    }

    /**
     * @test
     * @covers \App\Http\Repositories\BrandRepository::getTableHeaders()
     */
    public function test_get_table_headers()
    {
        $expected = [
            [
                'key' => 'id_manufacturer',
                'text' => 'Brand ID'
            ],
            [
                'key' => 'name',
                'text' => 'Brand Name'
            ],
            [
                'key' => 'sp_id_manufacturer',
                'text' => 'Apollo Manufacturer ID'
            ],
            [
                'key' => 'id_ns_brand',
                'text' => 'Netsuite Brand ID'
            ],
            [
                'key' => 'brand_label',
                'text' => 'Label'
            ],
        ];

        $result = $this->repository->getTableHeaders();

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @covers \App\Http\Repositories\BrandRepository::getManufacturer()
     */
    public function test_get_manufacturer()
    {
        $this->manufacturerMock->shouldReceive('where')->once()->andReturn($this->manufacturerMock);
        $this->manufacturerMock->shouldReceive('get')->once()->andReturn($this->manufacturerMock);

        $this->repository->getManufacturer();
    }

    /**
     * @test
     * @covers \App\Http\Repositories\BrandRepository::getLabel()
     */
    public function test_get_label()
    {
        $this->dbMock->shouldReceive('table')->with('label')->once()->andReturn($this->builderMock);
        $this->builderMock->shouldReceive('get')->once()->andReturn($this->builderMock);

        $this->repository->getLabel();
    }

    /**
     * @test
     * @covers \App\Http\Repositories\BrandRepository::createBrand()
     */
    public function test_create_brand()
    {
        $data = [
            'name' => 'test',
            'sp_id_manufacturer' => 1,
            'id_ns_brand' => 1,
            'id_label' => 1,
            'active' => 1,
        ];

        $this->manufacturerMock->shouldReceive('create')->once()->andReturn($this->manufacturerMock);

        $this->repository->createBrand($data);
    }

    /**
     * @test
     * @covers \App\Http\Repositories\BrandRepository::getBrand()
     */
    public function test_get_brand()
    {
        $idManufacturer = 1;

        $this->manufacturerMock->shouldReceive('find')->once()->andReturn($this->manufacturerMock);

        $this->repository->getBrand($idManufacturer);
    }

    /**
     * @test
     * @covers \App\Http\Repositories\BrandRepository::updateBrand()
     */
    public function test_update_brand()
    {
        $data = [
            'name' => 'test',
            'sp_id_manufacturer' => 1,
            'id_ns_brand' => 1,
            'id_label' => 1,
            'id_manufacturer' => 1
        ];

        $this->manufacturerMock->shouldReceive('where')->once()->andReturn($this->manufacturerMock);
        $this->manufacturerMock->shouldReceive('update')->once()->andReturn($this->manufacturerMock);

        $this->repository->updateBrand($data);
    }

    /**
     * @test
     * @covers \App\Http\Repositories\BrandRepository::deactivateBranch()
     */
    public function test_deactivate_branch()
    {
        $idManufacturer = 1;

        $this->manufacturerMock->shouldReceive('where')->once()->andReturn($this->manufacturerMock);
        $this->manufacturerMock->shouldReceive('update')->once()->andReturn($this->manufacturerMock);

        $this->repository->deactivateBranch($idManufacturer);
    }
}
