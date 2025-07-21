<?php

namespace Modules\Development\Http\Controllers;

use App\User;
use App\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Modules\Development\Models\AddDevelopment;
use Modules\Development\Entities\DevelopmentModule;

class AddDevelopmentController extends Controller
{
    protected function generateNextDocNo(): string
    {
        // Generate a random number between 1 and 999
        $number = rand(1, 999);
        
        // Format as NSNSD followed by 3-digit number with leading zeros
        $docNo = 'NSND' . str_pad($number, 5, '0', STR_PAD_LEFT);
        
        // Check if this doc_no already exists
        $exists = \Modules\Development\Models\AddDevelopment::where('doc_no', $docNo)->exists();
        
        // If it exists, try again with a new random number
        if ($exists) {
            return $this->generateNextDocNo();
        }
        
        return $docNo;
    }

    public function create()
    {        
        $doc_no = $this->generateNextDocNo();
        $modules = DevelopmentModule::pluck('name', 'id');
        $user_groups = UserGroup::pluck('name', 'id')->toArray();
        $related_doc_nos = \Modules\Development\Models\AddDevelopment::where('status', '!=', 'Completed')
            ->orderBy('doc_no', 'asc')
            ->pluck('doc_no', 'doc_no');
        $users = User::get();

        return view('development::add-development.create', compact('doc_no', 'modules', 'user_groups', 'related_doc_nos', 'users'));
    }

    public function saveDocNo(Request $request)
    {
        $request->validate([
            'doc_no' => [
                'required',
                'string',
                'max:50',
                'unique:add_developments,doc_no',
            ],
            'type' => 'required|string|in:Task,Enhancement,New Development',
        ]);

        // Get the authenticated user ID
        $userId = auth()->id();
        
        // Create a new development record with required fields
        $development = new \Modules\Development\Models\AddDevelopment([
            'doc_no' => $request->doc_no, // Use the provided doc_no as is
            'type' => $request->type,
            'status' => 'Draft', // Default status
            'created_by' => $userId,
            'user_id' => $userId, // Add user_id to satisfy the foreign key constraint
            'status_notes' => json_encode([]), // Initialize as empty array
            'group_comments' => json_encode([]), // Initialize as empty array
            'visible_to_groups' => json_encode([]), // Initialize as empty array
            'related_doc_no' => null, // Initialize as null
            'development_module_id' => null, // Initialize as null
            'priority' => null, // Default priority
        ]);

        $development->save();

        return response()->json([
            'success' => true,
            'message' => 'Document number saved successfully',
            'doc_no' => $development->doc_no
        ]);
    }

    public function addComment(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'group_comment.comment_type' => 'required|string|max:255',
                'group_comment.status_note' => 'required|string',
                'group_comment.status_note_heading' => 'nullable|string|max:255',
                'group_comment.status' => 'nullable|string|in:Pending,Not Completed,Completed',
                'status_notes' => 'nullable|array',
                'status_notes.*' => 'nullable|string'
            ]);

            $development = AddDevelopment::findOrFail($id);
            
            // Get existing group comments or initialize as empty array
            $groupComments = $development->group_comments ?? [];
            
            // Add new comment with all group comment data
            $newComment = [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->username,
                'comment_type' => $request->input('group_comment.comment_type'),
                'status_note' => $request->input('group_comment.status_note'),
                'status_note_heading' => $request->input('group_comment.status_note_heading'),
                'status' => $request->input('group_comment.status', 'Pending'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Add to the beginning of the array to show newest first
            array_unshift($groupComments, $newComment);
            
            // Update the development record
            $updateData = [
                'group_comments' => $groupComments,
                'status' => $request->input('group_comment.status', $development->status)
            ];
            
            // Add status notes if provided
            if ($request->has('status_notes')) {
                $statusNotes = $development->status_notes ?? [];
                foreach ($request->input('status_notes') as $note) {
                    if (!empty(trim($note))) {
                        $statusNotes[] = [
                            'note' => $note,
                            'user_id' => auth()->id(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
                $updateData['status_notes'] = $statusNotes;
            }
            
            // Save the updated development record
            $development->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => __('development::lang.comment_added_successfully'),
                'data' => [
                    'comment' => $newComment,
                    'status_notes' => $request->input('status_notes', [])
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error adding comment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the comment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {        
        // try {
            // Validate the incoming request
            $validatedData = $request->validate([
                'datetime' => 'required|date',
                'doc_no' => 'required|unique:add_developments',
                'task_heading' => 'required|string|max:255',
                'type' => 'required|in:Task,Issue',
                'development_module_id' => 'required|exists:development_modules,id',
                'details' => 'required|string',
                'priority' => 'required|in:Urgent,Priority,Normal',
                'visible_to_groups' => 'required|array',
                'related_doc_no' => 'nullable|required_if:type,Task|exists:add_developments,doc_no',
                'status_notes' => 'nullable|array',
                'status_notes.*' => 'nullable|string|max:1000'
            ]);

            DB::beginTransaction();
                $development = \Modules\Development\Models\AddDevelopment::create([
                    'datetime' => $validatedData['datetime'],
                    'doc_no' => $validatedData['doc_no'],
                    'user_id' => auth()->id(),
                    'type' => $validatedData['type'],
                    'task_heading' => $validatedData['task_heading'],
                    'development_module_id' => $validatedData['development_module_id'],
                    'details' => $validatedData['details'],
                    'related_doc_no' => $validatedData['type'] === 'Task' ? $validatedData['related_doc_no'] : null,
                    'priority' => $validatedData['priority'],
                    'visible_to_groups' => $validatedData['visible_to_groups'],
                    'status' => 'Pending',
                    'status_notes' => $validatedData['status_notes'] ?? [],
                ]);

                DB::commit();

                $output = [
                    'success' => 1,
                    'msg' => __('development::lang.add_development_added_successfully'),
                ];

                return redirect()
                    ->route('list-development.index')
                    ->with('status', $output);
            
    }

    public function edit($id)
    {
        $development = \Modules\Development\Models\AddDevelopment::with(['module'])->findOrFail($id);
        $modules = DevelopmentModule::pluck('name', 'id');
        $user_groups = UserGroup::pluck('name', 'id')->toArray();
        $related_doc_nos = \Modules\Development\Models\AddDevelopment::where('status', '!=', 'Completed')
            ->where('id', '!=', $id)
            ->orderBy('doc_no', 'asc')
            ->pluck('doc_no', 'doc_no');
        $users = User::get();

        // Prepare status notes for view
        $status_notes = $development->status_notes ?? [];

        // Prepare group comments for view
        $group_comments = [];
        foreach ($user_groups as $groupId => $groupName) {
            $group_comments[$groupId] = [
                'comment_type' => isset($development->group_comments[$groupId]) ? $development->group_comments[$groupId]['comment_type'] : null,
                'user_id' => isset($development->group_comments[$groupId]) ? $development->group_comments[$groupId]['user_id'] : null
            ];
        }

        return view('development::add-development.edit', compact('development', 'modules', 'user_groups', 'related_doc_nos', 'users', 'group_comments', 'status_notes'));
    }
    
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'datetime' => 'required|date',
            'doc_no' => 'required|unique:add_developments,doc_no,' . $id,
            'development_module_id' => 'required|exists:development_modules,id',
            'type' => 'required|in:Task,Issue',
            'details' => 'required|string',
            'priority' => 'required|in:Urgent,Priority,Normal',
            'visible_to_groups' => 'required|array',
            'related_doc_no' => 'nullable|required_if:type,Task|exists:add_developments,doc_no',
            'status' => 'required|in:Pending,Not Completed,Completed',

            // For group comment (single group section)
            'group_comment' => 'nullable|array',
            'group_comment.comment_type' => 'nullable|in:Issue Existing,No Issue,New Task',
            'group_comment.status' => 'nullable|in:Pending,Not Completed,Completed',
            'group_comment.status_note_heading' => 'nullable|string|max:255',
            'group_comment.status_note' => 'nullable|string',

            // For status notes (multiple possible)
            'status_notes' => 'nullable|array',
            'status_notes.*' => 'nullable|string|max:1000',
        ]);

        $development = \Modules\Development\Models\AddDevelopment::findOrFail($id);
        $development->update([
            'datetime' => $validatedData['datetime'],
            'doc_no' => $validatedData['doc_no'],
            'development_module_id' => $validatedData['development_module_id'],
            'type' => $validatedData['type'],
            'details' => $validatedData['details'],
            'related_doc_no' => $validatedData['type'] === 'Task' ? $validatedData['related_doc_no'] : null,
            'priority' => $validatedData['priority'],
            'visible_to_groups' => $validatedData['visible_to_groups'],
            'status' => $validatedData['status'],
            'status_notes' => $validatedData['status_notes'] ?? [],
            'group_comment' => $validatedData['group_comment'] ?? [],
        ]);

        DB::commit();

        $output = [
            'success' => 1,
            'msg' => __('development::lang.add_development_updated_successfully'),
        ];

        return redirect()
            ->route('list-development.index')
            ->with('status', $output);
      
    }


    public function show($id)
    {
        $development = \Modules\Development\Models\AddDevelopment::with([
            'module',
            'user',
            'user.userGroup'
        ])->findOrFail($id);

        // Get user groups for this development
        $user_groups = \App\UserGroup::whereIn('id', $development->visible_to_groups)->get()->pluck('name', 'id')->toArray();

        // Pass the raw group comments to the view
        $group_comments = $development->group_comments ?? [];

        return view('development::add-development.show', compact('development', 'group_comments', 'user_groups'));
    }
}
