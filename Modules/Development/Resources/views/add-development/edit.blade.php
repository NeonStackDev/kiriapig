@extends('layouts.app')

@section('title', __('development::lang.edit_development'))

@section('content')
<section class="content-header">
    <h1>@lang('development::lang.edit_development')</h1>
</section>

{{-- CSS & JavaScript Libraries --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<!-- Quill.js -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<style>
.note-editor.note-frame.note-disabled {
    opacity: 0.6;
}
.form-control{
    height: 46px!important;
}
</style>

<section class="content">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
    @endif

    {!! Form::open(['route' => ['development.update', $development->id], 'method' => 'PUT', 'id' => 'edit_development_form']) !!}
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="row">
                    {{-- Date & Time --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('datetime', __('development::lang.datetime') . ':*') !!}
                            {!! Form::text('datetime', old('datetime', $development->datetime), ['class' => 'form-control', 'readonly']) !!}
                        </div>
                    </div>

                    {{-- Doc No --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('doc_no', __('development::lang.doc_no') . ':*') !!}
                            {!! Form::text('doc_no', old('doc_no', $development->doc_no), ['class' => 'form-control', 'readonly']) !!}
                        </div>
                    </div>

                    {{-- Module (Choices.js) --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('development_module_id', __('development::lang.module') . ':*') !!}
                            {!! Form::select('development_module_id', $modules, old('development_module_id', $development->development_module_id), ['class' => 'form-control choices-select', 'required', 'placeholder' => __('development::lang.select_module')]) !!}
                        </div>
                    </div>

                    {{-- Type (no Choices.js) --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('type', __('development::lang.type') . ':*') !!}
                            {!! Form::select('type', ['Task' => 'Task', 'Issue' => 'Issue'], old('type', $development->type), ['class' => 'form-control', 'id' => 'type', 'required']) !!}
                        </div>
                    </div>

                    {{-- Related Doc No (Choices.js, conditional) --}}
                    <div class="col-md-6" style="display: {{ $development->type === 'Task' ? 'block' : 'none' }};">
                        <div class="form-group">
                            {!! Form::label('related_doc_no', __('development::lang.related_doc_no')) !!}
                            {!! Form::select('related_doc_no', $related_doc_nos, old('related_doc_no', $development->related_doc_no), [
                                'class' => 'form-control choices-select',
                                'id' => 'related_doc_select'
                            ]) !!}                        </div>
                    </div>

                    {{-- Details (Quill Editor) --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('details', __('development::lang.details') . ':*') !!}
                            <div id="details_quill" style="height: 200px;"></div>
                            {!! Form::textarea('details', old('details', $development->details), ['id' => 'details', 'style' => 'display: none;']) !!}
                        </div>
                    </div>
                    
                    <script>
                    $(document).ready(function() {
                        // Initialize Quill editor
                        const quill = new Quill('#details_quill', {
                            theme: 'snow',
                            modules: {
                                toolbar: [
                                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                                    ['bold', 'italic', 'underline', 'strike'],
                                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                    ['link', 'image'],
                                    ['clean']
                                ]
                            },
                            placeholder: 'Enter details here...',
                        });
                        
                        // Set initial content
                        @if(old('details', $development->details))
                            quill.clipboard.dangerouslyPasteHTML('{!! addslashes(old('details', $development->details)) !!}');
                        @endif
                        
                        // Handle form submission
                        $('form').on('submit', function() {
                            const detailsContent = quill.root.innerHTML.trim();
                            $('#details').val(detailsContent);
                            return true;
                        });
                    });
                    </script>

                    {{-- Priority (Choices.js) --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('priority', __('development::lang.priority') . ':*') !!}
                            {!! Form::select('priority', [
                                'Urgent' => '🔴 Urgent',
                                'Priority' => '🟡 Priority',
                                'Normal' => '🔵 Normal'
                            ], old('priority', $development->priority), ['class' => 'form-control choices-select', 'required']) !!}
                        </div>
                    </div>

                    {{-- Visible to Groups (Choices.js multi-select) --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('visible_to_groups', __('development::lang.visible_to_groups') . ':*') !!}
                            {!! Form::select('visible_to_groups[]', $user_groups, old('visible_to_groups', $development->visible_to_groups), ['class' => 'form-control choices-select', 'multiple', 'required']) !!}
                        </div>
                    </div>

                    {{-- Group Comments: SINGLE SECTION --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <h4 class="mb-4">@lang('development::lang.group_comments')</h4>
                            <div class="row">
                                {{-- Select Comment Type (NO Choices.js) --}}
                                <div class="col-md-3">
                                    <label>@lang('development::lang.select_comment_type')</label>
                                    <select name="group_comment[comment_type]" class="form-control" required>
                                        @php
                                            $latestComment = collect($development->group_comments ?? [])->first();
                                            $selectedType = old('group_comment.comment_type', $latestComment['comment_type'] ?? '');
                                        @endphp
                                        <option value="" disabled>@lang('development::lang.select_comment_type')</option>
                                        <option value="Issue Existing" {{ $selectedType == 'Issue Existing' ? 'selected' : '' }}>
                                            @lang('development::lang.issue_existing')
                                        </option>
                                        <option value="No Issue" {{ $selectedType == 'No Issue' ? 'selected' : '' }}>
                                            @lang('development::lang.no_issue')
                                        </option>
                                        <option value="New Task" {{ $selectedType == 'New Task' ? 'selected' : '' }}>
                                            @lang('development::lang.new_task')
                                        </option>
                                    </select>
                                </div>
                                {{-- Status Dropdown (NO Choices.js) --}}
                                <div class="col-md-3">
                                    <label>@lang('development::lang.status')</label>
                                    @php
                                        // Get status from group comment or fall back to main status
                                        $selectedStatus = old('group_comment.status', 
                                            $latestComment['status'] ?? 
                                            old('status', $development->status ?? 'Pending')
                                        );
                                        
                                        // Ensure the status is one of the allowed values
                                        $selectedStatus = in_array($selectedStatus, ['Pending', 'Not Completed', 'Completed']) 
                                            ? $selectedStatus 
                                            : 'Pending';
                                    @endphp
                                    <select name="group_comment[status]" class="form-control">
                                        <option value="Pending" {{ $selectedStatus == 'Pending' ? 'selected' : '' }}>@lang('development::lang.pending')</option>
                                        <option value="Not Completed" {{ $selectedStatus == 'Not Completed' ? 'selected' : '' }}>@lang('development::lang.not_completed')</option>
                                        <option value="Completed" {{ $selectedStatus == 'Completed' ? 'selected' : '' }}>@lang('development::lang.completed')</option>
                                    </select>
                                    {{-- Hidden field to update main status as well --}}
                                    <input type="hidden" name="status" value="{{ $selectedStatus }}">
                                </div>
                                {{-- Status Note Heading --}}
                                <div class="col-md-3">
                                    <label>@lang('development::lang.status_note_heading')</label>
                                    <input type="text" name="group_comment[status_note_heading]" id="status_note_heading"
                                           class="form-control"
                                           placeholder="@lang('development::lang.optional_heading')"
                                           value="{{ old('group_comment.status_note_heading', $latestComment['status_note_heading'] ?? '') }}">
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('development::lang.status')</label>
                                        <select name="status" class="form-control">
                                            <option value="Pending" {{ $development->status == 'Pending' ? 'selected' : '' }}>@lang('development::lang.pending')</option>
                                            <option value="Not Completed" {{ $development->status == 'Not Completed' ? 'selected' : '' }}>@lang('development::lang.not_completed')</option>
                                            <option value="Completed" {{ $development->status == 'Completed' ? 'selected' : '' }}>@lang('development::lang.completed')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div id="status-notes-container">
                                        @php
                                            $notes = old('status_notes', $development->status_notes ?? []);
                                            // Ensure we have an array of notes
                                            if (!is_array($notes)) {
                                                $notes = [];
                                            }
                                            // If we have old input, it will be an array of strings
                                            // If we have model data, it might be an array of arrays with 'note' key
                                            $processedNotes = [];
                                            foreach ($notes as $note) {
                                                if (is_array($note) && isset($note['note'])) {
                                                    $processedNotes[] = $note['note'];
                                                } elseif (is_string($note)) {
                                                    $processedNotes[] = $note;
                                                }
                                            }
                                            // If no notes, add an empty one
                                            if (empty($processedNotes)) {
                                                $processedNotes = [''];
                                            }
                                        @endphp
                                        @foreach($processedNotes as $index => $note)
                                            <div class="form-group status-note-group">
                                                <label>@lang('development::lang.status_notes') @if($index > 0)<button type="button" class="close remove-status-note" style="margin-left: 10px;"><span>&times;</span></button>@endif</label>
                                                <textarea name="status_notes[]" class="form-control" rows="3" placeholder="@lang('development::lang.add_status_note')">{{ is_string($note) ? $note : '' }}</textarea>
                                            </div>
                                        @endforeach
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-primary add-status-note">
                                                @lang('development::lang.add_another_note')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    {{-- Submit Button --}}
    <div class="row">
        <div class="col-md-12 text-right">
            <button type="submit" class="btn btn-primary">@lang('development::lang.general.save')</button>
            <a href="{{ route('list-development.index') }}" class="btn btn-default">@lang('development::lang.general.cancel')</a>
        </div>
    </div>

    {!! Form::close() !!}
</section>

<script>
$(document).ready(function() {
    // Initialize Choices.js on all .choices-select except excluded ones
    document.querySelectorAll('.choices-select').forEach(function (el) {
        // Exclude comment type, status, group_comment[status], group_comment[comment_type], type
        var name = el.getAttribute('name');
        if (
            name !== 'type' &&
            name !== 'status' &&
            name !== 'group_comment[status]' &&
            name !== 'group_comment[comment_type]'
        ) {
            new Choices(el, {
                removeItemButton: true,
                searchEnabled: true,
                placeholderValue: el.getAttribute('placeholder') || 'Select an option',
            });
        }
    });

    // Related doc toggle
    function toggleRelatedDocField() {
        const isTask = $('#type').val() === 'Task';
        const $relatedDocDiv = $('#related_doc_div');
        const $relatedDocSelect = $('#related_doc_select');

        if (isTask) {
            $relatedDocDiv.show();
            $relatedDocSelect.prop('disabled', false);
        } else {
            $relatedDocDiv.hide();
            $relatedDocSelect.prop('disabled', true).val('');
        }
    }

    $('#type').on('change', toggleRelatedDocField);
    toggleRelatedDocField();

    // Add related doc option
    $('.add-related-doc').on('click', function() {
        const newDocNo = prompt('Enter new document number:');
        if (newDocNo) {
            const $select = $('#related_doc_select');
            $select.append(new Option(newDocNo, newDocNo));
            $select.val(newDocNo).trigger('change');
        }
    });

    // Handle adding new status note
    $(document).on('click', '.add-status-note', function() {
        const $container = $('#status-notes-container');
        const $lastNoteGroup = $container.find('.status-note-group').last();
        const $newNoteGroup = $lastNoteGroup.clone();
        
        // Clear the textarea in the new note
        $newNoteGroup.find('textarea').val('');
        
        // Make sure the remove button is visible for the new note
        $newNoteGroup.find('.remove-status-note').show();
        
        // Insert the new note before the add button
        $newNoteGroup.insertBefore($(this).closest('.mt-3'));
    });

    // Handle removing status note
    $(document).on('click', '.remove-status-note', function() {
        // Don't remove the last note group
        if ($('.status-note-group').length > 1) {
            $(this).closest('.status-note-group').remove();
        }
    });

    // Summernote for details
    $('#details_quill').summernote({
        height: 200,
        placeholder: 'Enter task details...',
        focus: true,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

    // Summernote for group_comment status_note (disabled until heading filled)
    $('#status_notes_editor').summernote({
        height: 150,
        placeholder: 'Enter status notes...',
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['codeview']]
        ],
        disable: true
    });

    $('#status_note_heading').on('input', function () {
        const hasValue = $(this).val().trim() !== '';
        if (hasValue) {
            $('#status_notes_editor').summernote('enable');
        } else {
            $('#status_notes_editor').summernote('disable');
        }
    });

    if ($('#status_note_heading').val().trim() !== '') {
        $('#status_notes_editor').summernote('enable');
    } else {
        $('#status_notes_editor').summernote('disable');
    }
});
</script>
@endsection
