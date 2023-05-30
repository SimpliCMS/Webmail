<?php

namespace Modules\Webmail\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Controller;

class ScriptController extends Controller {

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $response = $next($request);
            $response->header('Content-Type', 'application/javascript');
            return $response;
        });
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function mailboxJS() {
        return view('webmail-admin::script.mailbox');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function sidebarJS() {
        return view('webmail-admin::script.sidebar');
    }

}
