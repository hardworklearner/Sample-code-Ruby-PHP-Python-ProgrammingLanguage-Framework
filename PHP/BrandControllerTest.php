<?php

namespace Tests\Controllers;

/** @Tools */
use Tests\TestCase;
use Mockery as m;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;

/** @Repositories */
use App\Http\Repositories\BrandRepository;

/** @Services */
use App\Http\Services\BrandService;

/** @controller */
use App\Http\Controllers\Suppliers\BrandController;

/**
 * @covers \App\Http\Controllers\Suppliers\BrandController
 */
class BrandControllerTest extends Testcase
{
    /**
     * @var properties
     */
    private $brandRepositoryMock;
    private $brandServiceMock;
    private $brandControllerMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->brandRepositoryMock = m::mock(BrandRepository::class);
        $this->brandServiceMock = m::mock(BrandService::class);
        $this->builderMock = m::mock(Builder::class);
        $this->requestMock = m::mock(Request::class);

        $this->brandController = m::mock(BrandController::class, [
            $this->brandRepositoryMock,
            $this->brandServiceMock
        ])->makePartial();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     *
     * @covers \App\Http\Controllers\Suppliers\BrandController::index()
     */
    public function test_index()
    {
        $request = [
            'manufacturer' => 111,
        ];
        $orderBy = [
            'key' => 'id_manufacturer',
            'value' => 'DESC',
        ];
        $tableHeaders = [
            [
                'key' => 'id_manufacturer',
                'text' => 'Brand ID'
            ],
        ];
        $filter = [
            'manufacturer' => [],
            'label' => [],
        ];

        $this->brandRepositoryMock->shouldReceive('getTableHeaders')->once()->andReturn($tableHeaders);
        $this->requestMock->shouldReceive('except')->twice()->andReturn($request);
        $this->brandServiceMock->shouldReceive('processOrderByKey')->once()->andReturn($orderBy);
        $this->brandServiceMock->shouldReceive('processFilter')->once()->andReturn([]);
        $this->brandServiceMock->shouldReceive('prepFilterForBlade')->once()->andReturn([]);
        $this->brandServiceMock->shouldReceive('getManufacturerList')->once()->andReturn($this->builderMock);
        $this->brandServiceMock->shouldReceive('getListCount')->once()->andReturn(1);
        $this->brandServiceMock->shouldReceive('getFilterData')->once()->andReturn($filter);
        $this->builderMock->shouldReceive('appends')->once()->andReturn($this->builderMock);

        $response = $this->brandController->index($this->requestMock);

        $this->assertInternalType('object', $response);
    }

    /**
     * @test
     *
     * @covers \App\Http\Controllers\Suppliers\BrandController::create()
     */
    public function test_create()
    {
        $filter = [
            'manufacturer' => [],
            'label' => [],
        ];

        $this->brandServiceMock->shouldReceive('getFilterData')->once()->andReturn($filter);

        $response = $this->brandController->create();

        $this->assertInternalType('object', $response);
    }

    /**
     * @test
     *
     * @covers \App\Http\Controllers\Suppliers\BrandController::store()
     */
    public function test_store()
    {
        $this->requestMock->shouldReceive('get')->with('name')->once()->andReturn('test');
        $this->requestMock->shouldReceive('get')->with('sp_id_manufacturer')->once()->andReturn('test');
        $this->requestMock->shouldReceive('get')->with('id_ns_brand')->once()->andReturn('test');
        $this->requestMock->shouldReceive('get')->with('label')->once()->andReturn('test');

        $this->brandServiceMock->shouldReceive('createBranch')->once()->andReturn(['success' => true]);

        $response = $this->brandController->store($this->requestMock);

        $this->assertInternalType('object', $response);
    }

    /**
     * @test
     *
     * @covers \App\Http\Controllers\Suppliers\BrandController::store()
     */
    public function test_store_fail()
    {
        $this->requestMock->shouldReceive('get')->with('name')->once()->andReturn('test');
        $this->requestMock->shouldReceive('get')->with('sp_id_manufacturer')->once()->andReturn('test');
        $this->requestMock->shouldReceive('get')->with('id_ns_brand')->once()->andReturn('test');
        $this->requestMock->shouldReceive('get')->with('label')->once()->andReturn('test');

        $this->brandServiceMock->shouldReceive('createBranch')->once()->andReturn(['success' => false, 'message' => 'error']);

        $response = $this->brandController->store($this->requestMock);

        $this->assertInternalType('object', $response);
    }

    /**
     * @test
     *
     * @covers \App\Http\Controllers\Suppliers\BrandController::edit()
     */
    public function test_edit()
    {
        $filter = [
            'manufacturer' => [],
            'label' => [],
        ];

        $this->brandServiceMock->shouldReceive('getFilterData')->once()->andReturn($filter);
        $this->brandServiceMock->shouldReceive('getBrand')->once()->andReturn($this->builderMock);

        $response = $this->brandController->edit(1);

        $this->assertInternalType('object', $response);
    }

    /**
     * @test
     *
     * @covers \App\Http\Controllers\Suppliers\BrandController::update()
     */
    public function test_update()
    {
        $this->requestMock->shouldReceive('get')->with('id_manufacturer')->once()->andReturn(1);
        $this->requestMock->shouldReceive('get')->with('name')->once()->andReturn('test');
        $this->requestMock->shouldReceive('get')->with('sp_id_manufacturer')->once()->andReturn('test');
        $this->requestMock->shouldReceive('get')->with('id_ns_brand')->once()->andReturn('test');
        $this->requestMock->shouldReceive('get')->with('label')->once()->andReturn('test');

        $this->brandServiceMock->shouldReceive('updateBranch')->once()->andReturn(['success' => true]);

        $response = $this->brandController->update($this->requestMock);

        $this->assertInternalType('object', $response);
    }

    /**
     * @test
     *
     * @covers \App\Http\Controllers\Suppliers\BrandController::update()
     */
    public function test_update_fail()
    {
        $this->requestMock->shouldReceive('get')->with('id_manufacturer')->once()->andReturn(1);
        $this->requestMock->shouldReceive('get')->with('name')->once()->andReturn('test');
        $this->requestMock->shouldReceive('get')->with('sp_id_manufacturer')->once()->andReturn('test');
        $this->requestMock->shouldReceive('get')->with('id_ns_brand')->once()->andReturn('test');
        $this->requestMock->shouldReceive('get')->with('label')->once()->andReturn('test');

        $this->brandServiceMock->shouldReceive('updateBranch')->once()->andReturn(['success' => false, 'message' => 'error']);

        $response = $this->brandController->update($this->requestMock);

        $this->assertInternalType('object', $response);
    }

    /**
     * @test
     *
     * @covers \App\Http\Controllers\Suppliers\BrandController::delete()
     */
    public function test_delete()
    {
        $this->brandServiceMock->shouldReceive('deactivateBranch')->once()->andReturn(true);

        $response = $this->brandController->delete(1);

        $this->assertInternalType('object', $response);
    }
}
