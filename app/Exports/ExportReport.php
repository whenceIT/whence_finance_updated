<?php

/**
 * Created by PhpStorm.
 * User: Tj
 * Date: 5/3/2018
 * Time: 8:08 PM
 */

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportReport implements FromView
{
    public $view;
    public $data;

    public function __construct($view, $data = "")
    {
        $this->view = $view;
        $this->data = $data;
    }

    public function view(): View
    {
        return view($this->view,
            $this->data
        );
    }
}