<?php

namespace App\Data;

use App\Entity\Categorie;

class SearchData
{
    /**
     * @var Categorie[]
     */
    public $categories = [];

    /**
     * @var null|integer
     */
    public $min;

    /**
     * @var null|integer
     */
    public $max;

    /**
     * @var boolean
     */
    public $expire = false;

    /**
     * @var boolean
     */
    public $epuise = false;
}