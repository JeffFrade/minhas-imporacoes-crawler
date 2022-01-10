<?php

namespace App\Repositories;

use App\Core\Support\AbstractRepository;
use App\Repositories\Models\Package;

class PackageRepository extends AbstractRepository
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->model = new Package();
    }
}
