<?php namespace App\Pages\{{pageNamespace}};

use App\Controllers\BaseController;

class PageController extends BaseController 
{
    public function getIndex()
    {
        $data = [];
        return pageView('{{pageName}}/index', $data);
    }
}
