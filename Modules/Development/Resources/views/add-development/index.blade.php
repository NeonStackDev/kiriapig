@extends('layouts.app')

@section('title', __('development::lang.developments_list'))


@section('content')
<style>
    /* Filter Section Styles */
    .filter-section {
        margin-bottom: 20px;
        background-color: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        padding: 15px;
    }
    .filter-section .box-header {
        border-bottom: 1px solid #e0e0e0;
        margin: -15px -15px 15px -15px;
        padding: 10px 15px;
        background-color: #f5f5f5;
    }
    .filter-section .box-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }
    .filter-section .form-group {
        margin-bottom: 15px;
    }
    .filter-section label {
        font-weight: 500;
        margin-bottom: 5px;
        display: block;
    }
    .filter-section .select2-container {
        width: 100% !important;
    }
    .filter-actions {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #eee;
    }
    /* Make sure date range picker is above modals */
    .daterangepicker {
        z-index: 9999 !important;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>@lang('development::lang.developments')</h1>
        <small>@lang('development::lang.manage_developments')</small>
    </section>

    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('development::lang.developments_list')])
        <div class="box">
            <div class="box-body">
                <!-- Filters Section -->
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-filter"></i> @lang('messages.filters')</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <!-- Date Range -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>@lang('messages.date_range')</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                                <input type="text" class="form-control" id="date_range" placeholder="@lang('messages.select_date_range')">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Document Number -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>@lang('development::lang.doc_no')</label>
                                            <select class="form-control select2" id="doc_no_filter" style="width: 100%;">
                                                <option value="">@lang('messages.all')</option>
                                                @foreach($documentNumbers as $docNo)
                                                    <option value="{{ $docNo }}">{{ $docNo }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Username -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>@lang('development::lang.created_by')</label>
                                            <select class="form-control select2" id="username_filter" style="width: 100%;">
                                                <option value="">@lang('messages.all')</option>
                                                @foreach($usernames as $username)
                                                    <option value="{{ $username->id }}">{{ $username->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Module -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>@lang('development::lang.module')</label>
                                            <select class="form-control select2" id="module_filter" style="width: 100%;">
                                                <option value="">@lang('messages.all')</option>
                                                @foreach($modules as $module)
                                                    <option value="{{ $module->id }}">{{ $module->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Type -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>@lang('development::lang.type')</label>
                                            <select class="form-control" id="type_filter" style="width: 100%;">
                                                <option value="">@lang('messages.all')</option>
                                                <option value="Task">@lang('development::lang.type_task')</option>
                                                <option value="Issue">@lang('development::lang.type_issue')</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Task Heading -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>@lang('development::lang.task_heading')</label>
                                            <select class="form-control select2" id="task_heading_filter" style="width: 100%;">
                                                <option value="">@lang('messages.all')</option>
                                                @foreach($taskHeadings as $heading)
                                                    <option value="{{ $heading }}">{{ $heading }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Related Document Number -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>@lang('development::lang.related_doc_no')</label>
                                            <select class="form-control select2" id="related_doc_no_filter" style="width: 100%;">
                                                <option value="">@lang('messages.all')</option>
                                                @foreach($relatedDocNos as $docNo)
                                                    <option value="{{ $docNo }}">{{ $docNo }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>@lang('development::lang.status')</label>
                                            <select class="form-control" id="status_filter" style="width: 100%;">
                                                <option value="">@lang('messages.all')</option>
                                                <option value="Pending">@lang('development::lang.status_pending')</option>
                                                <option value="Not Completed">@lang('development::lang.status_not_completed')</option>
                                                <option value="Completed">@lang('development::lang.status_completed')</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button type="button" class="btn btn-primary" id="apply_filters">
                                            <i class="fa fa-filter"></i> @lang('messages.apply_filters')
                                        </button>
                                        <button type="button" class="btn btn-default" id="reset_filters">
                                            <i class="fa fa-undo"></i> @lang('messages.reset')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="development_table">
                        <colgroup>
                            <col width="9%">
                            <col width="10%">
                            <col width="10%">
                            <col width="30%">
                            <col width="8.5%">
                            <col width="10%">
                            <col width="15%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>@lang('development::lang.doc_no')</th>
                                <th>@lang('development::lang.datetime')</th>
                                <th>@lang('development::lang.module')</th>
                                <th>@lang('development::lang.task_heading')</th>
                                <th>@lang('development::lang.priority')</th>
                                <th>@lang('development::lang.status')</th>
                                <th>@lang('development::lang.created_by')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        @endcomponent
    </section>
</div>
@endsection

@section('javascript')
<!-- Date Range Picker -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css') }}">
<script type="text/javascript" src="{{ asset('js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/daterangepicker.js') }}"></script>

<script type="text/javascript">
    // Initialize date range picker
    var dateRangePicker = $('#date_range').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: 'Apply',
            cancelLabel: 'Clear',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
        },
        startDate: moment().startOf('month'),
        endDate: moment().endOf('month'),
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        autoUpdateInput: false,
        alwaysShowCalendars: true,
        showDropdowns: true,
        autoApply: true,
        linkedCalendars: false
    });

    // Update the date range display
    $('#date_range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });

    // Clear the date range
    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    function format(d) {
        var isCreatorOrAdmin = {!! json_encode(auth()->user()->hasRole('Super Admin') || auth()->id() == 'user_id_placeholder') !!};
        
        var html = '<div class="row" style="margin: 15px 0 0 50px; width: 100%;">' +
            '<div class="col-md-12">' +
            '<div class="box box-default">' +
            '<div class="box-body">' +
            '<div class="row">' +
            '<div class="col-md-4">' +
            '<p><strong>Document No:</strong> ' + (d.doc_no || '-') + '</p>' +
            '<p><strong>Date & Time:</strong> ' + (d.datetime ? moment(d.datetime).format('YYYY-MM-DD HH:mm') : '-') + '</p>' +
            '<p><strong>Related Document No:</strong> ' + (d.related_doc_no || '-') + '</p>' +
            '</div>' +
            '</div>';
            
        // Add comments section if exists
        if (d.comments && d.comments.length > 0) {
            html += '<div class="row" style="margin-top: 15px;">' +
                '<div class="col-md-12">' +
                '<h5>Group Comments:</h5>';
                
            d.comments.forEach(function(comment) {
                html += '<div class="comment-box" style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">' +
                    '<p><strong>' + (comment.user_name || 'Unknown') + ':</strong> ' + comment.comment + '</p>' +
                    '<small class="text-muted">' + (comment.created_at ? moment(comment.created_at).format('YYYY-MM-DD HH:mm') : '') + '</small>' +
                    '</div>';
            });
            
            html += '</div></div>';
        }
        
        // Add action buttons
        html += '<div class="row" style="margin-top: 15px;">' +
            '<div class="col-md-12">' +
            '<button class="btn btn-xs btn-info view-btn" data-id="' + d.id + '" style="margin-right: 5px;">' +
            '<i class="fa fa-eye"></i> View' +
            '</button>';
            
        if (isCreatorOrAdmin) {
            html += '<button class="btn btn-xs btn-primary edit-btn" data-id="' + d.id + '" style="margin-right: 5px;">' +
                '<i class="fa fa-edit"></i> Edit' +
                '</button>';
        }
        
        html += '<button class="btn btn-xs btn-success add-comment-btn" data-id="' + d.id + '">' +
            '<i class="fa fa-comment"></i> Add Group Comment' +
            '</button>' +
            '</div>' +
            '</div>' +
            '</div>' + // box-body
            '</div>' + // box
            '</div>' + // col-md-12
            '</div>'; // row
            
        return html;
    }

    // Function to get filter values
    function getFilters() {
        var startDate = dateRangePicker.data('daterangepicker').startDate;
        var endDate = dateRangePicker.data('daterangepicker').endDate;
        
        return {
            start_date: startDate ? startDate.format('YYYY-MM-DD') : '',
            end_date: endDate ? endDate.format('YYYY-MM-DD') : '',
            doc_no: $('#doc_no_filter').val(),
            user_id: $('#username_filter').val(),
            module_id: $('#module_filter').val(),
            type: $('#type_filter').val(),
            task_heading: $('#task_heading_filter').val(),
            related_doc_no: $('#related_doc_no_filter').val(),
            status: $('#status_filter').val()
        };
    }

    $(document).ready(function() {
        // Apply filters
        $('#apply_filters').on('click', function() {
            table.ajax.reload(null, false); // false means don't reset paging
        });

        // Reset filters
        $('#reset_filters').on('click', function() {
            // Reset form
            $('#date_range').val('').trigger('change');
            $('#doc_no_filter').val('');
            $('#username_filter').val('').trigger('change');
            $('#module_filter').val('').trigger('change');
            $('#type_filter').val('').trigger('change');
            $('#task_heading_filter').val('');
            $('#related_doc_no_filter').val('');
            $('#status_filter').val('').trigger('change');
            
            // Reset the table
            table.ajax.reload();
        });
        // Initialize Select2 for all select2 elements
        $('.select2').select2({
            width: '100%',
            placeholder: 'Select an option',
            allowClear: true
        });

        var table = $('#development_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("list-development.index") }}',
                type: 'GET',
                data: function(d) {
                    // Add filter parameters to the DataTables request
                    var filters = getFilters();
                    d.start_date = filters.start_date;
                    d.end_date = filters.end_date;
                    d.doc_no = filters.doc_no;
                    d.user_id = filters.user_id;
                    d.module_id = filters.module_id;
                    d.type = filters.type;
                    d.task_heading = filters.task_heading;
                    d.related_doc_no = filters.related_doc_no;
                    d.status = filters.status;
                    
                    // Add custom parameters for filtering
                    $.extend(d, {
                        'start_date': filters.start_date,
                        'end_date': filters.end_date,
                        'doc_no': filters.doc_no,
                        'user_id': filters.user_id,
                        'module_id': filters.module_id,
                        'type': filters.type,
                        'task_heading': filters.task_heading,
                        'related_doc_no': filters.related_doc_no,
                        'status': filters.status
                    });
                }
            },
            columns: [
                {
                    className: 'details-control',
                    orderable: false,
                    data: 'doc_no',
                    render: function(data, type, row) {
                        return '<i class="fa fa-plus-circle text-primary" style="cursor: pointer; margin-right: 5px;"></i> '+ data;
                    }
                },
                {
                    data: 'datetime',
                    render: function(data) {
                        return data ? moment(data).format('YYYY-MM-DD HH:mm') : '';
                    }
                },
                { data: 'module_name', name: 'module_name' },
                { data: 'task_heading', name: 'task_heading' },
                {
                    data: 'priority',
                    render: function(data) {
                        if (!data) return '';
                        var colors = {
                            'Urgent': 'red',
                            'Priority': 'yellow',
                            'Normal': 'lightblue'
                        };
                        return '<span class="badge" style="background-color: ' + (colors[data] || 'lightgray') + '">' + data + '</span>';
                    }
                },
                {
                    data: 'status',
                    render: function(data) {
                        if (!data) return '';
                        return '<span class="label label-' + (data === 'Completed' ? 'success' : data === 'Not Completed' ? 'warning' : 'primary') + '">' + data + '</span>';
                    }
                },
                { data: 'username', name: 'username' }
            ]
        });
        
        // Handle view button click
        $(document).on('click', '.view-btn', function() {
            var id = $(this).data('id');
            window.location.href = '{{ route("development.show", "") }}/' + id;
        });
        
        // Handle edit button click
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            window.location.href = '{{ route("development.edit", "") }}/' + id;
        });
        
        // Handle add comment button click
        $(document).on('click', '.add-comment-btn', function() {
            var id = $(this).data('id');
            $('#commentModal').modal('show');
            // Set the form action with the development ID
            var actionUrl = '{{ url("developments") }}/' + id + '/add-comment';
            $('#commentForm').attr('action', actionUrl);
            $('#commentForm').data('development-id', id);
        });
        
        // Initialize Quill editor
        var quill = new Quill('#status_notes_editor_quill', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'header': 1 }, { 'header': 2 }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'direction': 'rtl' }],
                    [{ 'size': ['small', false, 'large', 'huge'] }],
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'font': [] }],
                    [{ 'align': [] }],
                    ['clean'],
                    ['link', 'image']
                ]
            },
            placeholder: '@lang('development::lang.enter_status_notes')',
        });
        
        // Set initial empty content to ensure proper validation
        quill.clipboard.dangerouslyPasteHTML('');

        // Handle adding new status note fields
        $(document).on('click', '.add-status-note', function() {
            var container = $('#status-notes-container');
            var noteFields = container.find('.status-note-group');
            var newNote = noteFields.first().clone();
            
            // Clear the textarea value
            newNote.find('textarea').val('');
            
            // Add remove button if it's the first note
            if (noteFields.length === 1) {
                newNote.append('<button type="button" class="btn btn-sm btn-danger remove-status-note mt-2"><i class="fa fa-trash"></i> Remove</button>');
            }
            
            // Add remove button to the new note
            if (noteFields.length > 0) {
                newNote.find('.remove-status-note').remove();
                newNote.append('<button type="button" class="btn btn-sm btn-danger remove-status-note mt-2"><i class="fa fa-trash"></i> Remove</button>');
            }
            
            container.append(newNote);
        });
        
        // Handle removing status note fields
        $(document).on('click', '.remove-status-note', function() {
            var container = $('#status-notes-container');
            var noteFields = container.find('.status-note-group');
            
            // Don't remove the last note field
            if (noteFields.length > 1) {
                $(this).closest('.status-note-group').remove();
            } else {
                // If it's the last field, just clear it
                noteFields.find('textarea').val('');
            }
        });
        
        // Handle comment form submission
        $('#commentForm').on('submit', function(e) {
            e.preventDefault();
            
            // Update hidden textarea with Quill content
            var quillContent = quill ? quill.root.innerHTML : '';
            $('#status_notes_editor').val(quillContent);
            
            // Collect all status notes
            var statusNotes = [];
            $('textarea[name="status_notes[]"]').each(function() {
                if ($(this).val().trim() !== '') {
                    statusNotes.push($(this).val().trim());
                }
            });
            
            // Create form data
            var formData = new FormData(this);
            
            // Add status notes to form data
            formData.delete('status_notes[]'); // Remove existing status_notes
            statusNotes.forEach(function(note, index) {
                formData.append('status_notes[]', note);
            });
            
            // Get the form action URL
            var url = $(this).attr('action');
            
            // Validate required fields first
            var commentType = $('select[name="group_comment[comment_type]"]').val();
            if (!commentType) {
                toastr.error('@lang('development::lang.comment_type_required')');
                $('select[name="group_comment[comment_type]"], #status_notes_editor_quill').addClass('is-invalid');
                return false;
            }
            
            // Get and validate comment content
            var editorContent = quill.root.innerHTML;
            var commentText = quill.getText().trim();
            
            if (editorContent === '<p><br></p>' || commentText === '') {
                toastr.error('@lang('development::lang.comment_required')');
                $('#status_notes_editor_quill').addClass('is-invalid');
                return false;
            }
            
            // Update the hidden textarea with Quill content if validation passes
            $('#status_notes_editor').val(editorContent);
            $('.is-invalid').removeClass('is-invalid');
            
            // Get the form and submit button
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var originalBtnText = submitBtn.html();
            
            // Disable submit button and show loading state
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            
            // Create new FormData and append all form fields
            var formData = new FormData();
            
            // Append all form fields
            form.find('input, select, textarea').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    if ($(this).is(':checkbox') || $(this).is(':radio')) {
                        if ($(this).is(':checked')) {
                            formData.append(name, $(this).val());
                        }
                    } else if ($(this).is('select[multiple]')) {
                        // Handle multiple select
                        var values = $(this).val() || [];
                        values.forEach(function(value) {
                            formData.append(name + '[]', value);
                        });
                    } else {
                        formData.append(name, $(this).val());
                    }
                }
            });
            
            // Append Quill content
            formData.set('group_comment[status_note]', editorContent);
            
            // Append status notes
            statusNotes.forEach(function(note, index) {
                formData.append('status_notes[]', note);
            });
            
            // Get the URL
            var id = form.data('development-id') || url.split('/').pop();
            var submitUrl = '{{ url("developments") }}/' + id + '/add-comment';
            
            // Log form data for debugging
            console.log('Submitting form to:', submitUrl);
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ', pair[1]);
            }
            
            // Send AJAX request
            $.ajax({
                url: submitUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Response received:', response);
                    if (response && response.success) {
                        toastr.success(response.message || 'Comment added successfully');
                        
                        // Reset form and Quill editor
                        form[0].reset();
                        if (quill) {
                            quill.root.innerHTML = '';
                        }
                        
                        // Close the modal
                        $('#commentModal').modal('hide');
                        
                        // Reload the DataTable
                        if (typeof table !== 'undefined') {
                            table.ajax.reload(null, false);
                        }
                    } else {
                        var errorMsg = response && response.message ? response.message : 'An unknown error occurred';
                        console.error('Error in response:', errorMsg);
                        toastr.error(errorMsg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response:', xhr.responseText);
                    
                    var errorMessage = 'An error occurred while saving the comment';
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response && response.message) {
                            errorMessage = response.message;
                        } else if (response && response.errors) {
                            // Handle validation errors
                            var errors = [];
                            for (var field in response.errors) {
                                if (response.errors.hasOwnProperty(field)) {
                                    errors.push(response.errors[field][0]);
                                }
                            }
                            errorMessage = errors.join('\n');
                        }
                    } catch (e) {
                        console.error('Error parsing error response:', e);
                        if (xhr.statusText) {
                            errorMessage = xhr.statusText;
                        }
                    }
                    
                    toastr.error(errorMessage);
                },
                complete: function() {
                    // Always re-enable the submit button and restore original text
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });

        // Add event listener for opening and closing details
        $('#development_table tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var icon = $(this).find('i');

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
                icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
            } else {
                // Open this row
                row.child(format(row.data())).show();
                tr.addClass('shown');
                icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
            }
        });

        // Handle click on plus/minus icon
        $('#development_table tbody').on('click', 'td.details-control i', function(e) {
            e.stopPropagation();
            $(this).closest('td').trigger('click');
        });
    });
</script>

<!-- Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="commentModalLabel">Add Comment</h4>
            </div>
            <form id="commentForm" method="POST" class="form-horizontal">
                @csrf
                <div class="modal-body" style="padding: 30px 25px;">
                    <div class="box box-solid">
                        <div class="box-body">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <h4 class="box-title" style="margin-top: 0;">
                                        <i class="fa fa-comments"></i> @lang('development::lang.group_comments')
                                    </h4>
                                    <hr style="margin: 10px 10px 20px 10px;">
                                </div>
                                
                                <!-- Comment Type -->
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('development::lang.comment_type') <span class="text-danger">*</span></label>
                                        <select name="group_comment[comment_type]" class="form-control select2" required>
                                            <option value="">@lang('development::lang.select_comment_type')</option>
                                            <option value="Issue Existing">@lang('development::lang.issue_existing')</option>
                                            <option value="No Issue">@lang('development::lang.no_issue')</option>
                                            <option value="New Task">@lang('development::lang.new_task')</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div class="col-sm-6 ml-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('development::lang.status')</label>
                                        <select name="group_comment[status]" class="form-control select2">
                                            <option value="Pending">@lang('development::lang.pending')</option>
                                            <option value="Not Completed">@lang('development::lang.not_completed')</option>
                                            <option value="Completed">@lang('development::lang.completed')</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Status Note Heading -->
                                <div class="col-sm-12 mt-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('development::lang.status_note_heading')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-header"></i>
                                            </span>
                                            <input type="text" name="group_comment[status_note_heading]" 
                                                id="status_note_heading" class="form-control" 
                                                placeholder="@lang('development::lang.optional_heading')">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Status Notes Editor -->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('development::lang.status_notes')</label>
                                        <div class="box box-solid" style="border: 1px solid #d2d6de;">
                                            <div id="status_notes_editor_quill" style="min-height: 150px;"></div>
                                            <textarea id="status_notes_editor" name="group_comment[status_note]" style="display: none;"></textarea>
                                            <script>
                                                $(document).ready(function() {
                                                    // Moved Quill initialization to document ready
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <h4 class="box-title" style="margin-top: 0; margin-bottom: 20px;">
                                            <i class="fa fa-info-circle"></i> @lang('development::lang.status')
                                        </h4>
                                        <hr style="margin: 10px 10px 20px 10px;">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">@lang('development::lang.status')</label>
                                                <select name="status" class="form-control select2">
                                                    <option value="Pending">@lang('development::lang.pending')</option>
                                                    <option value="Not Completed">@lang('development::lang.not_completed')</option>
                                                    <option value="Completed">@lang('development::lang.completed')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="status-notes-container">
                                                <div class="status-note-group">
                                                    <div class="form-group">
                                                        <label class="control-label">@lang('development::lang.status_notes')</label>
                                                        <textarea name="status_notes[]" class="form-control" rows="3"
                                                            placeholder="@lang('development::lang.add_status_note')"></textarea>
                                                    </div>
                                                </div>
                                                <div class="mt-3 mb-3">
                                                    <button type="button" class="btn btn-sm btn-primary add-status-note">
                                                        <i class="fa fa-plus"></i> @lang('development::lang.add_another_note')
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 5px 10px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> @lang('messages.close')
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> @lang('messages.save')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

 <!-- Quill rich text editor -->
 <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
 <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
@endsection
