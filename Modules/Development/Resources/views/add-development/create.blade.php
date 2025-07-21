@extends('layouts.app')

@section('title', __('development::lang.add_development'))

@section('content')
<!-- Content Header -->
<section class="content-header">
    <h1>@lang('development::lang.add_development')</h1>
</section>

<style>
    .note-editor.note-frame.note-disabled {
        opacity: 0.6;
    }

    .form-control {
        height: 46px !important;
    }
    
    #details_quill, #status_notes_editor_quill {
        height: 200px;
        max-height: 200px;
        overflow-y: auto;
        margin-bottom: 24px;
    }
    .ql-editor {
        min-height: 150px;
        max-height: 170px; /* Prevents inner editor scrollbars from being huge */
        overflow-y: auto;
    }
    body {
        overflow-y: auto !important;
    }
    
    .cke_top {
        display: none!important;
    }
</style>

<!-- Main Content -->
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

    {!! Form::open(['route' => 'development.store', 'method' => 'POST', 'id' => 'add_development_form']) !!}
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                {{-- Date & Time --}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('datetime', __('development::lang.datetime') . ':*') !!}
                        {!! Form::text('datetime', old('datetime', now()), ['class' => 'form-control', 'readonly']) !!}
                    </div>
                </div>

                {{-- Doc No --}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('doc_no', __('development::lang.doc_no') . ':*') !!}
                        {!! Form::text('doc_no', old('doc_no', $doc_no), ['class' => 'form-control', 'readonly']) !!}
                    </div>
                </div>

                {{-- Logged in User --}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('user', __('development::lang.user')) !!}
                        {!! Form::text('user', old('user', auth()->user()->username), ['class' => 'form-control',
                        'readonly']) !!}
                    </div>
                </div>

                {{-- Type --}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('type', __('development::lang.type') . ':*') !!}
                        {!! Form::select('type', ['Task' => 'Task', 'Issue' => 'Issue'], old('type'), ['class' =>
                        'form-control', 'id' => 'type', 'required']) !!}
                    </div>
                </div>

                {{-- Task Heading --}}
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('task_heading', __('development::lang.task_heading') . ':*') !!}
                        {!! Form::text('task_heading', old('task_heading'), ['class' => 'form-control', 'required',
                        'placeholder' => __('development::lang.enter_task_heading')]) !!}
                    </div>
                </div>

                {{-- Module --}}
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('development_module_id', __('development::lang.module') . ':*') !!}
                        {!! Form::select('development_module_id', $modules, old('development_module_id'), ['class' =>
                        'form-control choices-select', 'required', 'placeholder' =>
                        __('development::lang.select_module')]) !!}
                    </div>
                </div>

                {{-- Related Doc No (conditional) --}}
                <div class="col-md-4" id="related_doc_div" style="display: none;">
                    <div class="form-group">
                        {!! Form::label('related_doc_no', __('development::lang.related_doc_no')) !!}
                        <div class="input-group">
                            {!! Form::select('related_doc_no', $related_doc_nos, old('related_doc_no'), [
                            'class' => 'form-control choices-select',
                            'id' => 'related_doc_select'
                            ]) !!}
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="add_related_doc"
                                    style="height: 46px;margin-top: -25px;">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>  

                {{-- Details --}}
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('details', __('development::lang.details') . ':*', ['class' => 'control-label']) !!}
                        <div id="details_quill" style="height:200px; margin-bottom:24px;"></div>
                        {!! Form::textarea('details', old('details'), [
                            'class' => 'form-control', 
                            'id' => 'details',
                            'style' => 'display: none;'
                        ]) !!}
                        <div class="help-block with-errors"></div>
                    </div>
                </div>

                {{-- Priority --}}
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('priority', __('development::lang.priority') . ':*') !!}
                        {!! Form::select('priority', [
                        'Urgent' => '🔴 Urgent',
                        'Priority' => '🟡 Priority',
                        'Normal' => '🔵 Normal'
                        ], old('priority'), ['class' => 'form-control choices-select', 'required']) !!}
                    </div>
                </div>

                {{-- Visible to Groups --}}
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('visible_to_groups', __('development::lang.visible_to_groups') . ':*') !!}
                        {!! Form::select('visible_to_groups[]', $user_groups, old('visible_to_groups', []), ['class' =>
                        'form-control choices-select', 'multiple', 'required']) !!}
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Handle form submission with Quill editor validation
            $('form').on('submit', function(e) {
                const quill = new Quill('#details_quill');
                const detailsContent = quill.root.innerHTML.trim();
                
                if (detailsContent === '' || detailsContent === '<p><br></p>') {
                    e.preventDefault();
                    alert('Please enter the details before submitting.');
                    return false;
                }
                
                // Update the hidden textarea with Quill content
                $('#details').val(detailsContent);
                return true;
            });
            
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
            
            // Set initial content if there's any old input
            @if(old('details'))
                quill.clipboard.dangerouslyPasteHTML('{!! addslashes(old('details')) !!}');
            @endif
            // Add status note functionality
            $('.add-status-note').on('click', function() {
                const container = $('#status-notes-container');
                const newNote = `
                    <div class="form-group">
                        <label>@lang('development::lang.status_notes')</label>
                        <textarea name="status_notes[]" class="form-control" rows="3" placeholder="@lang('development::lang.add_status_note')"></textarea>
                    </div>
                `;
                container.append(newNote);
            });

            // Remove status note functionality
            $(document).on('click', '.remove-status-note', function() {
                const noteContainer = $(this).closest('.form-group');
                noteContainer.next('.remove-status-note').remove(); // Remove the remove button
                noteContainer.remove(); // Remove the form-group
            });
        });
    </script>

    {{-- Submit Button --}}
    <div class="row">
        <div class="col-md-12 text-right">
            <button type="submit" class="btn btn-primary">@lang('development::lang.general.save')</button>
            <a href="{{ route('list-development.index') }}"
                class="btn btn-default">@lang('development::lang.general.cancel')</a>
        </div>
    </div>

    {!! Form::close() !!}
</section>

<script>
    $(document).ready(function () {
        function toggleRelatedDocField() {
            const isTask = $('#type').val() === 'Task';
            const $relatedDocDiv = $('#related_doc_div');
            const $relatedDocSelect = $('#related_doc_select');

            if (isTask) {
                $relatedDocDiv.show();
                $relatedDocSelect.prop('disabled', false).removeAttr('required');
            } else {
                $relatedDocDiv.hide();
                $relatedDocSelect.prop('disabled', true).val('').removeAttr('required');
            }
        }

        $('#type').on('change', toggleRelatedDocField);
        toggleRelatedDocField();

        $('#add_related_doc').on('click', function () {
        let newDoc = prompt("Enter new Related Doc No (e.g., DOC-001, ABC123):");
        
        // If user clicks cancel or closes the prompt
        if (newDoc === null) return;
        
        // Trim whitespace
        newDoc = newDoc.trim();
        
        // If empty after trimming
        if (!newDoc) {
            alert('Please enter a document number');
            return;
        }
        
        // Show loading state
        const $button = $(this);
        const originalText = $button.html();
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        // Get the selected type from the form
        const docType = $('#type').val();
        
        // Make AJAX request to save the document
        $.ajax({
            url: '{{ route('development.save-doc-no') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                doc_no: newDoc,
                type: docType || 'Task' // Default to 'Task' if no type selected
            },
            success: function(response) {
                if (response.success) {
                    const $select = $('#related_doc_select');
                    // Check if option already exists
                    if (!$select.find(`option[value="${response.doc_no}"]`).length) {
                        // Add the new option and select it
                        $select.append(new Option(response.doc_no, response.doc_no, true, true));
                        // Update the input field with the new value
                        $('#related_doc_input').val(response.doc_no);
                        // Trigger change event in case other components are listening
                        $select.trigger('change');
                    } else {
                        $select.val(response.doc_no);
                        $('#related_doc_input').val(response.doc_no);
                    }
                    toastr.success(response.message || 'Document number saved successfully');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while saving the document number.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join(' ');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);
            },
            complete: function() {
                // Restore button state
                $button.prop('disabled', false).html(originalText);
            }
        });
    });

});
</script>

{{-- CSS & JS Libraries --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.choices-select').forEach(function (el) {
            const choices = new Choices(el, {
                removeItemButton: true,
                searchEnabled: true,
                placeholderValue: el.getAttribute('placeholder') || 'Select an option',
            });

            const container = el.closest('.choices');
            if (container) {

                const dropdownList = container.querySelector('.choices__list--dropdown');
                if (dropdownList) {
                    dropdownList.style.zIndex = '1000';
                }
            }
        });
    });
</script>

<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var dq = document.getElementById('details_quill');
  var sq = document.getElementById('status_notes_editor_quill');
  if (dq) dq.innerHTML = '';
  if (sq) sq.innerHTML = '';

  // One static topbar: all controls for text, alignment, lists, media, etc.
  var toolbarOptions = [
    [
      { 'font': [] },                                 // Font family
      { 'size': ['small', false, 'large', 'huge'] },  // Font size
      'bold', 'italic', 'underline', 'strike',        // Font styles
      { 'color': [] }, { 'background': [] },          // Colors
      { 'script': 'sub'}, { 'script': 'super' },      // Sub/superscript
      { 'header': [1, 2, 3, false] },                 // Headers
      { 'align': [] },                                // Alignment
      'blockquote', 'code-block',                     // Blocks
      { 'list': 'ordered' }, { 'list': 'bullet' },    // Lists
      { 'indent': '-1'}, { 'indent': '+1' },          // Indent
      { 'direction': 'rtl' },                         // Text direction
      'link', 'image', 'video',                       // Media
      'formula',                                      // Math formulas (if KaTeX loaded)
      'clean'                                         // Remove formatting
    ]
  ];

  // Only initialize once!
  if (!window.quillDetails) {
    window.quillDetails = new Quill('#details_quill', {
      theme: 'snow',
      modules: { toolbar: toolbarOptions },
      placeholder: 'Enter details...'
    });
    window.quillStatus = new Quill('#status_notes_editor_quill', {
      theme: 'snow',
      modules: { toolbar: toolbarOptions },
      placeholder: 'Enter status notes...',
      readOnly: true
    });

    function enableQuillImageDrop(quill) {
      quill.root.addEventListener('drop', function(e) {
        e.preventDefault();
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
          var file = e.dataTransfer.files[0];
          var reader = new FileReader();
          reader.onload = function(e) {
            var range = quill.getSelection();
            var img = e.target.result;
            quill.insertEmbed(range ? range.index : 0, 'image', img, "user");
          };
          reader.readAsDataURL(file);
        }
      });
    }
    enableQuillImageDrop(window.quillDetails);
    enableQuillImageDrop(window.quillStatus);

    // Set Quill content from textarea if editing
    var detailsText = document.getElementById('details').value;
    if (detailsText && detailsText.trim().length > 0) {
      window.quillDetails.root.innerHTML = detailsText;
    }
    var statusText = document.getElementById('status_notes_editor').value;
    if (statusText && statusText.trim().length > 0) {
      window.quillStatus.root.innerHTML = statusText;
    }

    // Enable status notes editor when heading is filled
    var heading = document.getElementById('status_note_heading');
    heading.addEventListener('input', function() {
      var enabled = heading.value.trim().length > 0;
      window.quillStatus.enable(enabled);
    });
    window.quillStatus.enable(heading.value.trim().length > 0);

    // On form submit, copy Quill contents back to textareas
    document.querySelector('form').addEventListener('submit', function(e) {
      document.getElementById('details').value = window.quillDetails.root.innerHTML;
      document.getElementById('status_notes_editor').value = window.quillStatus.root.innerHTML;
    });
  }
});
</script>
@endsection