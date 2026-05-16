<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class DocumentsController extends Controller
{
    public function index()
    {
        $events = Event::with('criterias')->orderBy('date', 'desc')->paginate(10);
        return view('admin.documents.index', compact('events'));
    }
}
