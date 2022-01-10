<?php

namespace App\Services;

use App\Repositories\PackageRepository;

class Package
{
    /**
     * @var PackageRepository
     */
    private $packageRepository;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->packageRepository = new PackageRepository();
    }

    /**
     * @param string $trackingNumber
     * @return mixed
     */
    public function getPackageByTrackingNumber(string $trackingNumber)
    {
        return $this->packageRepository->findFirst('tracking_number', $trackingNumber);
    }

    /**
     * @param string $trackingNumber
     * @param string $status
     * @param string $date
     * @return void
     */
    public function storePackage(string $trackingNumber, string $status, string $date)
    {
        $data = [
            'tracking_number' => $trackingNumber,
            'status' => $status,
            'date' => $date
        ];

        $this->packageRepository->create($data);
    }
}
