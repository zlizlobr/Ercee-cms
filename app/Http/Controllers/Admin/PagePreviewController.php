<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Content\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagePreviewController extends Controller
{
    public function __invoke(Page $page): View
    {
        return view('filament.pages.preview', [
            'page' => $page,
        ]);
    }
}
