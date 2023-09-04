<?php

namespace App\Model;

class SearchData
{
    /** @var int */
    public $page = 1;

    /** @var string|null */
    public ?string $q = null;

    /** @var string|null */
    public ?string $sortBy = null; 

    /** @var string|null */
    public ?string $city = null; 

}

