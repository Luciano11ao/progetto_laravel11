<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActionLog;

class ActionLogController extends Controller
{
    public function index()
    {
        $actionLogs = ActionLog::with(['note', 'user'])->orderBy('created_at', 'desc')->paginate();
        return view('note.actionlog', ['actionLogs' => $actionLogs]);
    }    
}
