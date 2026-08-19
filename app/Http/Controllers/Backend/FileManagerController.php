<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class FileManagerController extends Controller
{
    public function __construct()
    {
        $this->selectedMainMenu = 'file_manager';
        parent::__construct();

        if (!Gate::allows('file_manager')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function index()
    {
        return view('backend.filemanager.index');
    }

    public function ckfinder()
    {
        return view('backend.filemanager.ckfinder');
    }
}
