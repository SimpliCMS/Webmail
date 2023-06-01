<?php

namespace Modules\Webmail\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Webmail\Http\Controllers\Admin\WebmailController;

class ScriptController extends WebmailController {

    public function __construct() {
        parent::__construct();
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
        $folders = $this->imapClient->getFolders();
        return view('webmail-admin::script.mailbox', compact('folders'));
    }

}
