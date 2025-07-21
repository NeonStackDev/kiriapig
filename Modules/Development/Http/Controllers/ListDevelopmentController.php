<?php

namespace Modules\Development\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Development\Models\AddDevelopment;
use Modules\Development\Entities\DevelopmentModule;

class ListDevelopmentController extends Controller
{
    public function index()
    {
        // Get data for filters
        $documentNumbers = \Modules\Development\Models\AddDevelopment::pluck('doc_no')->unique();
        $usernames = \App\User::select('id', 'username')->get();
        $modules = \Modules\Development\Entities\DevelopmentModule::select('id', 'name')->get();
        $taskHeadings = \Modules\Development\Models\AddDevelopment::whereNotNull('task_heading')
            ->pluck('task_heading')
            ->unique();
        $relatedDocNos = \Modules\Development\Models\AddDevelopment::whereNotNull('related_doc_no')
            ->pluck('related_doc_no')
            ->unique();

        if (request()->ajax()) {
            // Return filter data if it's a filter request
            if (request()->has('get_filters')) {
                return response()->json([
                    'documentNumbers' => $documentNumbers,
                    'usernames' => $usernames,
                    'modules' => $modules,
                    'taskHeadings' => $taskHeadings,
                    'relatedDocNos' => $relatedDocNos
                ]);
            }

            // Handle DataTables request
            $query = \Modules\Development\Models\AddDevelopment::latest()->with([
                    'user' => function($q) {
                        $q->select('id', 'username', 'email');
                    },
                    'module' => function($q) {
                        $q->select('id', 'name');
                    }
                ])
                ->select([
                    'id',
                    'doc_no',
                    'datetime',
                    'task_heading',
                    'type',
                    'priority',
                    'status',
                    'user_id',
                    'development_module_id',
                    'related_doc_no'
                ]);
                
            // Apply filters
            if (request()->has('start_date') && !empty(request('start_date'))) {
                $startDate = request('start_date') . ' 00:00:00';
                $query->where('datetime', '>=', $startDate);
            }
            
            if (request()->has('end_date') && !empty(request('end_date'))) {
                $endDate = request('end_date') . ' 23:59:59';
                $query->where('datetime', '<=', $endDate);
            }
            
            if (request()->has('doc_no') && !empty(request('doc_no'))) {
                $query->where('doc_no', request('doc_no'));
            }
            
            if (request()->has('user_id') && !empty(request('user_id'))) {
                $query->where('user_id', request('user_id'));
            }
            
            if (request()->has('module_id') && !empty(request('module_id'))) {
                $query->where('development_module_id', request('module_id'));
            }
            
            if (request()->has('type') && !empty(request('type'))) {
                $query->where('type', request('type'));
            }
            
            if (request()->has('task_heading') && !empty(request('task_heading'))) {
                $query->where('task_heading', 'like', '%' . request('task_heading') . '%');
            }
            
            if (request()->has('related_doc_no') && !empty(request('related_doc_no'))) {
                $query->where('related_doc_no', request('related_doc_no'));
            }
            
            if (request()->has('status') && !empty(request('status'))) {
                $query->where('status', request('status'));
            }
                
            return Datatables::of($query)
                ->addColumn('module_name', function($row) {
                    return $row->module->name ?? null;
                })
                ->addColumn('username', function($row) {
                    return $row->user->username ?? null;
                })
                ->addColumn('task_heading', function($row) {
                    return $row->task_heading ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">';
                    $html .= '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" 
                            data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>';
                    $html .= '<ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    
                    $html .= '<li>';
                    $html .= '<a href="' . route('development.show', $row->id) . '" class="btn btn-xs btn-info">
                            <i class="fa fa-eye"></i> ' . __('messages.view') . '
                        </a>';
                    $html .= '</li>';

                    $html .= '<li>';
                    $html .= '<a href="' . route('development.edit', $row->id) . '" class="btn btn-xs btn-primary">
                            <i class="fa fa-edit"></i> ' . __('messages.edit') . '
                        </a>';
                    $html .= '</li>';

                    $html .= '<li>';
                    $html .= '<button data-href="' . route('development.destroy', $row->id) . '" 
                            class="btn btn-xs btn-danger delete_development">
                            <i class="fa fa-trash"></i> ' . __('messages.delete') . '
                        </button>';
                    $html .= '</li>';

                    $html .= '</ul></div>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('development::add-development.index', [
            'documentNumbers' => $documentNumbers,
            'usernames' => $usernames,
            'modules' => $modules,
            'taskHeadings' => $taskHeadings,
            'relatedDocNos' => $relatedDocNos
        ]);
    } 
}
