<?php

namespace App\Http\Controllers\Suppliers;

use Session;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

use Auth;
use App\Http\Repositories\BrandRepository;
use App\Http\Services\BrandService;

class BrandController extends Controller
{
    private $repository;
    private $service;

    public function __construct(
        BrandRepository $repository,
        BrandService $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Brand listing page
     *
     * @param Request $request
     *
     */
    public function index(Request $request)
    {
        $fields = $this->repository->getTableHeaders();
        $input = $request->except('page');
        $orderBy = $this->service->processOrderByKey($input);
        $filter = $this->service->processFilter($input);
        $filterBlade = $this->service->prepFilterForBlade($filter);

        $result = $this->service->getManufacturerList($filter, $orderBy);
        $countResult = $this->service->getListCount($filter, $orderBy);
        $filterData = $this->service->getFilterData();

        //append query string to URL for pagination.
        foreach ($request->except('page') as $key => $value) {
            $result = $result->appends([
                $key => $value
            ]);
        }

        return view('pages.brand.list', [
            'orderbyKey' => $orderBy['key'],
            'orderbyValue' => $orderBy['value'],
            'result' => $result,
            'filterBlade' => $filterBlade,
            'fields' => $fields,
            'countResult' => $countResult,
            'manufacturer' => $filterData['manufacturer'],
            'labels' => $filterData['label']
        ]);
    }

    /**
     * Brand adding page
     */
    public function create()
    {
        $filterData = $this->service->getFilterData();

        return view('pages.brand.form', [
            'labels' => $filterData['label']
        ]);
    }

    /**
     * Create new Brand
     *
     * @param Request $request
     *
     */
    public function store(Request $request)
    {
        $data = [
            'name' => $request->get('name'),
            'sp_id_manufacturer' => $request->get('sp_id_manufacturer'),
            'id_ns_brand' => $request->get('id_ns_brand'),
            'id_label' => $request->get('label'),
            'active' => 1
        ];

        $result = $this->service->createBranch($data);

        if ($result['success']) {
            return redirect('/brand');
        } else {
            return Redirect::back()->withErrors(['error' => $result['message']]);
        }
    }

    /**
     * Brand edit page
     *
     * @param integer $idManufacturer
     *
     */
    public function edit(int $idManufacturer)
    {
        $filterData = $this->service->getFilterData();
        $brand = $this->service->getBrand($idManufacturer);

        return view('pages.brand.form', [
            'labels' => $filterData['label'],
            'brand' => $brand
        ]);
    }

    /**
     * Update current Brand
     *
     * @param Request $request
     *
     */
    public function update(Request $request)
    {
        $data = [
            'id_manufacturer' => $request->get('id_manufacturer'),
            'name' => $request->get('name'),
            'sp_id_manufacturer' => $request->get('sp_id_manufacturer'),
            'id_ns_brand' => $request->get('id_ns_brand'),
            'id_label' => $request->get('label'),
        ];

        $result = $this->service->updateBranch($data);
        
        if ($result['success']) {
            return Redirect::back()->withErrors(['success' => 'Updated Successfully']);
        } else {
            return Redirect::back()->withErrors(['error' => $result['message']]);
        }
    }

    /**
     * Deactivate current Brand
     *
     * @param integer $idManufacturer
     * @return json
     */
    public function delete(int $idManufacturer)
    {
        $result = $this->service->deactivateBranch($idManufacturer);

        return response()->json($result);
    }
}
