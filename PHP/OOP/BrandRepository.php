<?php

namespace App\Http\Repositories;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use App\Manufacturer;

class BrandRepository
{
    private $db;
    private $manufacturer;
    private $selectField = [
        'manufacturer.id_manufacturer',
        'manufacturer.name',
        'manufacturer.sp_id_manufacturer',
        'manufacturer.id_ns_brand',
        'label.name AS brand_label'
    ];

    public function __construct(
        DatabaseManager $db,
        Manufacturer $manufacturer
    ) {
        $this->db = $db;
        $this->manufacturer = $manufacturer;
    }

    /**
     * Get active Brand list
     *
     * @param array $filter
     * @param array $order
     * @return void
     */
    public function getManufacturerList(array $filter = [], array $order = [])
    {
        $query = $this->queryResult($filter, $order);

        return $query->paginate(50);
    }

    /**
     * Query for Brand list
     *
     * @param array $filter
     * @param array $order
     * @return collection
     */
    public function queryResult(array $filter = [], array $order = [])
    {
        $query = $this->db->table('manufacturer')
            ->leftJoin('label', 'label.id_label', '=', 'manufacturer.id_label')
            ->where('manufacturer.active', 1);

        if (count($filter) > 0) {
            foreach ($filter as $f) {
                $fieldName = $this->explodeAndCheckSelectField($this->selectField, $f['key']);
                if ($fieldName) {
                    if (is_array($f['value'])) {
                        $query->whereIn($fieldName, $f['value']);
                    } else {
                        $query->where($fieldName, 'like', '%' . $f['value'] . '%');
                    }
                }
            }
        }

        if (count($order) > 0) {
            $query->orderBy($order['key'], $order['value']);
        } else {
            $query->orderBy('manufacturer.id_manufacturer', 'DESC');
        }

        return $query->select($this->selectField);
    }

    /**
     * Explode array
     *
     * @param array $selectField
     * @param string $filterWord
     * @return string
     */
    public function explodeAndCheckSelectField(array $selectField, string $filterWord)
    {
        foreach ($selectField as $field) {
            if (strpos($field, $filterWord) > 0) {
                return explode(' ', $field)[0];
            }
        }

        return $filterWord;
    }

    /**
     * Get Table header information for Brand Page
     *
     * @return array
     */
    public function getTableHeaders(): array
    {
        return [
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
    }

    /**
     * Get all active Manufacturer
     *
     * @return array
     */
    public function getManufacturer()
    {
        return $this->manufacturer
            ->where('active', 1)
            ->get();
    }

    /**
     * Get all Label
     *
     * @return array
     */
    public function getLabel()
    {
        return $this->db->table('label')->get();
    }

    /**
     * Create new Brand
     *
     * @param array $data
     * @return int
     */
    public function createBrand(array $data)
    {
        return $this->manufacturer->create($data);
    }

    /**
     * Get current Brand
     *
     * @param integer $idManufacturer
     * @return \App\Manufacturer
     */
    public function getBrand(int $idManufacturer)
    {
        return $this->manufacturer->find($idManufacturer);
    }

    /**
     * Update current Branch
     *
     * @param array $data
     * @return int
     */
    public function updateBrand(array $data)
    {
        return $this->manufacturer
            ->where('id_manufacturer', $data['id_manufacturer'])
            ->update([
                'name' => $data['name'],
                'sp_id_manufacturer' => $data['sp_id_manufacturer'],
                'id_ns_brand' => $data['id_ns_brand'],
                'id_label' => $data['id_label'],
            ]);
    }

    /**
     * Deactivate current Branch
     *
     * @param integer $idManufacturer
     * @return int
     */
    public function deactivateBranch(int $idManufacturer)
    {
        return $this->manufacturer
            ->where('id_manufacturer', $idManufacturer)
            ->update(['active' => 0]);
    }
}
