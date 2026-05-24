<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    // 构造函数，要不给默认值，或者直接用set的形式来传参，不然就不能用反射来实力化
    public $id;

    public function index()
    {
        echo $this->id;
        echo "index";
    }

    public function setId($id = null)
    {
        $this->id = $id;
    }
}
