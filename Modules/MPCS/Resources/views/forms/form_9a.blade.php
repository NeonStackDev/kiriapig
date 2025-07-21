@extends('layouts.app')
@section('title', __('mpcs::lang.form_9_a'))

@section('content')
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if(auth()->user()->can('f9a_form'))
                    <li class="active">
                        <a href="#9a_form_tab" class="9a_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.form_9_a')</strong>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(abilities: 'f9a_settings_form'))
                    <li class="">
                        <a href="#9a_form_settings_tab" class="9a_form_settings_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.form_9_a_settings')</strong>
                        </a>
                    </li>
                    @endif
                </ul>
                <div class="tab-content">
                    @if(auth()->user()->can('f9a_form'))
                    <div class="tab-pane active" id="9a_form_tab">
                        @include('mpcs::forms.partials.9a_form')
                    </div>
                    @endif
                    @if(auth()->user()->can('f9a_settings_form'))
                    <div class="tab-pane" id="9a_form_settings_tab">
                        @include('mpcs::forms.partials.9a_settings_form')
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade form_9_a_settings_modal" id="form_9_a_settings_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade update_form_9_a_settings_modal" id="update_form_9_a_settings_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>

<!-- /.content -->

@endsection
@section('javascript')<script type="text/javascript">
    $(document).ready(function() {
    // Open modal when button is clicked
    $('#text_details_button').click(function() {
        $('.text_details_modal').modal('show');
    });
    
    // Save text details
    $('#save_text_details').click(function() {
        var textContent = $('#text_content').val();
         var formNumber = $('.9c_from_date').text(); // Get the form number
          var id = $('#id').val();
        if (textContent.trim() === '') {
            alert('Please enter some text');
            return;
        }
        
        $.ajax({
              method: 'POST',
             url: '/mpcs/get-text-store',          
            data: {
                _token: '{{ csrf_token() }}',
                text_content: textContent,
                form: formNumber, // Add form number to the data
                 id: id || undefined // Send id only when editing
            },
            success: function(response) {
                if (response.success) {
                    $('.text_details_modal').modal('hide');
                    $('#text_content').val('');
                      $('#id').val(''); // Clear the ID after save
                    loadTextDetails(); // Refresh the table
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
    
    // Function to load text details
    function loadTextDetails() {
        $.ajax({
              url: '/mpcs/get-text-get',
            method: 'GET',
            success: function(response) {
                var tableBody = $('#text_details_table tbody');
                tableBody.empty();
                
                response.data.forEach(function(item) {
                    var row = '<tr>' +
                        '<td>' +
                        '<button class="btn btn-xs btn-primary edit-text-detail" data-id="' + item.id + '"><i class="fa fa-edit"></i> Edit</button> ' +
                        '</td>' +
                        '<td>' + item.text_content + '</td>' +
                        '</tr>';
                    
                    tableBody.append(row);
                });
            }
        });
    }
    
    // Initial load
    loadTextDetails();
    
    // Edit functionality
    $(document).on('click', '.edit-text-detail', function() {
        var id = $(this).data('id');
        
        $.ajax({
             url: '/mpcs/get-text-edit',
            method: 'GET',
            data: { id: id },
            success: function(response) {
                $('#text_content').val(response.text_content);
                $('#text_details_form').append('<input type="hidden" id="id" name="id" value="' + id + '">');
                $('.text_details_modal').modal('show');
            }
        });
    });
    
    // Delete functionality
    $(document).on('click', '.delete-text-detail', function() {
        if (confirm('Are you sure you want to delete this text detail?')) {
            var id = $(this).data('id');
            
            $.ajax({
                 url: '/mpcs/get-9a-form_value',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        loadTextDetails(); // Refresh the table
                    }
                }
            });
        }
    });
});
    $(document).ready(function () {
    // Function to enable or disable the button based on ref_pre_form_number
    function toggleButtonState() {
        const refPreFormNumber = $('#ref_pre_form_number').val().trim(); // Get the value of ref_pre_form_number
        const addButton = $('#add_form_9_a_settings_button'); // Select the button

        if (refPreFormNumber !== '') {
            // If ref_pre_form_number is not empty, disable the button
            // addButton.prop('disabled', true);
        } else {
            // If ref_pre_form_number is empty, enable the button
            // addButton.prop('disabled', false);
        }
    }

    // Call the function on page load
    toggleButtonState();
    // Optionally, recheck the state if ref_pre_form_number changes dynamically
    $(document).on('change', '#ref_pre_form_number', function () {
        toggleButtonState();
    });
});
    $(document).ready(function() {
        // Fetch and display Form 9A data
        $('#9a_date_ranges').daterangepicker({
                    singleDatePicker: true, // For selecting a single date
                    showDropdowns: true, // To show the dropdown for predefined date ranges
                    locale: {
                        format: 'YYYY-MM-DD', // Adjust the date format according to your needs
                    },
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Custom Date Range': [moment().startOf('month'), moment().endOf(
                            'month')], // Default custom date range (this can be modified)
                    }
                }, function(start, end, label) {
                    if (label === 'Custom Date Range') {
                        // Show the modal for manual input
                        $('.custom_date_typing_modal').modal('show');
                        // $('.custom_date_typing_modal').modal('show'); // Uncomment if needed
                    }else{
                        // Set the selected date in the input
                        $('#9a_date_ranges').val(start.format('YYYY-MM-DD'));
                        get9AForm();
                        fetchPaymentsData();
                        
                    }
                    // Refresh DataTable with new date
                    //form_9a_tables.ajax.reload();
                });

                $('#custom_date_apply_button').on('click', function () {
                    let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                    let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                    if (startDate.length === 10 && endDate.length === 10) {
                        let formattedStartDate = moment(startDate).format(moment_date_format);
                        let formattedEndDate = moment(endDate).format(moment_date_format);
                        let fullRange = formattedStartDate + ' ~ ' + formattedEndDate;

                        // === Update #9c_date_range if it exists ===
                        if ($('#9a_date_ranges').length) {
                            $('#9a_date_ranges').val(fullRange);
                            $('#9a_date_ranges').data('daterangepicker').setStartDate(moment(startDate));
                            $('#9a_date_ranges').data('daterangepicker').setEndDate(moment(endDate));
                            $("#report_date_range").text("Date Range: " + fullRange);
                            if (typeof get9AForm === 'function') get9AForm();
                        }

                        // Hide the modal
                        $('.custom_date_typing_modal').modal('hide');
                    } else {
                        alert("Please select both start and end dates.");
                    }
                });

                // Reset the field when the cancel button is clicked
                $('#9a_date_ranges').on('cancel.daterangepicker', function(ev, picker) {
                    $('#9a_date_ranges').val('');
                });

                // Set the default selected date range when initializing the date picker
                $('#9a_date_ranges').data('daterangepicker').setStartDate(moment().startOf('day'));
                $(
                    '#9a_date_ranges').data('daterangepicker').setEndDate(moment().endOf('day'));

                // Display the selected date range on the page
                let date = $('#9a_date_ranges').val().split(' - ');

                $('.to_date').text(date[1]);
            
            $('#9a_date_ranges').change(function() {                
                  console.log("eccce");
                 // form_9a_tables.ajax.reload();
                 get9AForm();
                 fetchPaymentsData();
            });
 //form 9a list ;
        get9AForm();

        function get9AForm() {
    const start_date = $('input#9a_date_ranges').data('daterangepicker').startDate.format('YYYY-MM-DD');
    const end_date = $('input#9a_date_ranges').data('daterangepicker').endDate.format('YYYY-MM-DD');

    $.ajax({
        method: 'get',
        url: '/mpcs/get-9a-form_value',
        data: { start_date, end_date },
        success: function(result) {
            if (result) {
                $('#openingdate').text("Date: " + start_date);
                 updateTextDetails(result.text_details);
                // Format amount with 2 decimal places
            const formatAmount = (amount) => {
    if (amount === 0) return '';
    return amount.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};


                // Display values
                $('#cash_sales').text(formatAmount(result.cash_sales_rup, result.cash_sales_cent));
                $('#card_sales').text(formatAmount(result.card_sales_rup, result.card_sales_cent));
                
                const totalCashSale = Number(result.card_sales_rup) + Number(result.cash_sales_rup) + 
                                    (Number(result.card_sales_cent) + Number(result.cash_sales_cent)) / 100;
                $('#total_cash_sale').text(totalCashSale.toFixed(2));
                
                $('#total_credit_sale').text(formatAmount(result.credit_sales_rup, result.credit_sales_cent));
                
                const totalSale = Number(result.card_sales_rup) + Number(result.cash_sales_rup) + Number(result.credit_sales_rup) + 
                                (Number(result.card_sales_cent) + Number(result.cash_sales_cent) + Number(result.credit_sales_cent)) / 100;
                $('#total_sale').text(totalSale.toFixed(2));
                
                $('#total_sale_pre_day').text(formatAmount(result.previous_sales_rup, result.previous_sales_cent));
                
                let totalToday = Number(totalSale) + 
                 Number(result.previous_sales_rup) + 
                 (Number(result.previous_sales_cent) / 100);
$('#total_sale_today').text(formatAmount(totalToday));
                
                // Update receipt table
                updateReceiptTable(result);
            } else {
                clearFormValues();
            }
        },
        error: function(xhr, status, error) {
           // console.error("Error fetching Form 9A data:", error);
        }
    });
}

function updateReceiptTable(result) {
    // const formatCurrency = (rup, cent) => {
    //     if (rup === 0 && cent === 0) return '';
    //     const amount = Number(rup) + (Number(cent) / 100);
    //     return amount.toFixed(2);
    // };
     const formatCurrency = (amount) => {
    if (amount === 0) return '';
    return amount.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};
    // Previous Day
    $('#form_15a9ab_receipts_table tbody tr:eq(0) td:eq(0)').text(formatCurrency(result.previous_sales_rup, result.previous_sales_cent));
    $('#form_15a9ab_receipts_table tbody tr:eq(0) td:eq(2)').text(formatCurrency(result.cash_sales_rup, result.cash_sales_cent));
    const totalDay1 = Number(result.card_sales_rup) + Number(result.cash_sales_rup) + Number(result.previous_sales_rup) + 
                     (Number(result.card_sales_cent) + Number(result.cash_sales_cent) + Number(result.previous_sales_cent)) / 100;
    $('#form_15a9ab_receipts_table tbody tr:eq(0) td:eq(3)').text(formatCurrency(totalDay1));

    // Card Sales
    $('#form_15a9ab_receipts_table tbody tr:eq(1) td:eq(0)').text(formatCurrency(result.card_sales_rup, result.card_sales_cent));
    $('#form_15a9ab_receipts_table tbody tr:eq(1) td:eq(2)').text(formatCurrency(result.card_sales_rup, result.card_sales_cent));
    $('#form_15a9ab_receipts_table tbody tr:eq(1) td:eq(3)').text(formatCurrency(result.card_sales_rup, result.card_sales_cent));

    // Total
    const cashCardTotal = Number(result.card_sales_rup) + Number(result.cash_sales_rup) + 
                         (Number(result.card_sales_cent) + Number(result.cash_sales_cent)) / 100;
    $('#form_15a9ab_receipts_table tbody tr:eq(5) td:eq(0)').text(formatCurrency(result.previous_sales_rup, result.previous_sales_cent));
    $('#form_15a9ab_receipts_table tbody tr:eq(5) td:eq(2)').text(cashCardTotal.toFixed(2));
    $('#form_15a9ab_receipts_table tbody tr:eq(5) td:eq(3)').text((cashCardTotal + Number(result.previous_sales_rup) + (Number(result.previous_sales_cent) / 100)).toFixed(2));

    // Credit Sales
    const creditTotal = Number(result.credit_sales_rup) + (Number(result.credit_sales_cent) / 100);
    $('#form_15a9ab_receipts_table tbody tr:eq(6) td:eq(0)').text(creditTotal.toFixed(2));
    $('#form_15a9ab_receipts_table tbody tr:eq(6) td:eq(2)').text(creditTotal.toFixed(2));
    $('#form_15a9ab_receipts_table tbody tr:eq(6) td:eq(3)').text(creditTotal.toFixed(2));
}

function clearFormValues() {
    $('[id$="_sales"], [id^="total_"]').text('');
}

// Reload Form 9A data when the date changes
$('#9a_date_ranges').change(function() {
    get9AForm();
});

function updateReceiptTable(result) {
   // const formatCurrency = (value) => value ? Number(value).toFixed(2) : '';
       const formatCurrency = (amount) => {
    if (amount === 0) return '';
    return amount.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};
    // Previous Day
    $('#form_15a9ab_receipts_table tbody tr:eq(0) td:eq(0)').text(formatCurrency(result.previous_sales_rup));
    $('#form_15a9ab_receipts_table tbody tr:eq(0) td:eq(2)').text(formatCurrency(result.cash_sales_rup));
    $('#form_15a9ab_receipts_table tbody tr:eq(0) td:eq(3)').text(formatCurrency(Number(result.card_sales_rup) + Number(result.cash_sales_rup) + Number(result.previous_sales_rup)));

    // Card Sales
    $('#form_15a9ab_receipts_table tbody tr:eq(1) td:eq(0)').text(formatCurrency(result.card_sales_rup));
    $('#form_15a9ab_receipts_table tbody tr:eq(1) td:eq(2)').text(formatCurrency(result.card_sales_rup));
    $('#form_15a9ab_receipts_table tbody tr:eq(1) td:eq(3)').text(formatCurrency(result.card_sales_rup));

    // Total
    $('#form_15a9ab_receipts_table tbody tr:eq(5) td:eq(0)').text(formatCurrency(result.previous_sales_rup));
    $('#form_15a9ab_receipts_table tbody tr:eq(5) td:eq(2)').text(formatCurrency(Number(result.card_sales_rup) + Number(result.cash_sales_rup)));
    $('#form_15a9ab_receipts_table tbody tr:eq(5) td:eq(3)').text(formatCurrency(Number(result.card_sales_rup) + Number(result.cash_sales_rup) + Number(result.previous_sales_rup)));

    // Credit Sales
    $('#form_15a9ab_receipts_table tbody tr:eq(6) td:eq(0)').text(formatCurrency(Number(result.credit_sales_rup)));
    $('#form_15a9ab_receipts_table tbody tr:eq(6) td:eq(2)').text(formatCurrency(Number(result.credit_sales_rup)));
    $('#form_15a9ab_receipts_table tbody tr:eq(6) td:eq(3)').text(formatCurrency(Number(result.credit_sales_rup)));
}

function clearFormValues() {
    $('[id$="_rup"], [id$="_cent"]').text('');
}



        // Reload Form 9A data when the date changes
        $('#9a_date_ranges').change(function() {
            get9AForm();
        });

        // Initialize DataTable for Form 9A settings
        var form_9a_settings_table = $('#form_9a_settings_table').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            ajax: {
                type: "get",
                url: "/mpcs/get-form-9a-settings",
                dataSrc: "data", // Ensure this matches the key in your JSON response
                error: function(xhr, error, thrown) {
                    console.error("DataTables error:", xhr.responseText);
                }
            },
            columns: [
                { data: 'action', name: 'action', searchable: false, orderable: false },
                { data: 'starting_number', name: 'starting_number' },
                { data: 'total_sale_to_pre', name: 'total_sale_to_pre' },
                { data: 'pre_day_cash_sale', name: 'pre_day_cash_sale' },
                { data: 'pre_day_card_sale', name: 'pre_day_card_sale' },
                { data: 'pre_day_credit_sale', name: 'pre_day_credit_sale' },
                { data: 'pre_day_cash', name: 'pre_day_cash' },
                { data: 'pre_day_cheques', name: 'pre_day_cheques' },
                { data: 'pre_day_total', name: 'pre_day_total' },
                { data: 'pre_day_balance', name: 'pre_day_balance' },
                { data: 'pre_day_grand_total', name: 'pre_day_grand_total' }
            ]
        });

        // Handle Form 9A settings submission
        $(document).on('submit', 'form#add_9a_form_settings', function(e) {
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                        form_9a_settings_table.ajax.reload();
                        $('div#form_9_a_settings_modal').modal('hide');
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error submitting Form 9A settings:", error);
                },
                complete: function() {
                    $(this).find('button[type="submit"]').attr('disabled', false);
                }
            });
        });

        // Handle Form 9A settings update
        $(document).on('submit', 'form#update_9a_form_settings', function(e) {
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                        form_9a_settings_table.ajax.reload();
                        $('div#update_form_9_a_settings_modal').modal('hide');
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error updating Form 9A settings:", error);
                },
                complete: function() {
                    $(this).find('button[type="submit"]').attr('disabled', false);
                }
            });
        });

        // Print Form 9A
        $("#print_div").click(function() {
            printDiv();
        });

        function printDiv() {
            var w = window.open('', '_self');
            var html = `
                <html>
                    <head>
                        <style>
                            @page {
                                size: landscape;
                            }
                            body {
                                width: 100%;
                                margin: 0;
                                padding: 0;

                            }
                              h5 {
                margin: 0px 0;
                font-weight: bold;
                text-align: center;
            }   
                dropdown
                {
 text-align: center;
        }
                            @media print {
                                html, body {
                                    width: 100%;
                                    overflow: visible !important;
                                }
                                * {
                                    font-size: 8pt;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        ${document.getElementById("print_content").innerHTML}
                    </body>
                </html>
            `;
            $(w.document.body).html(html);
            w.print();
            w.close();
            window.location.href = "{{URL::to('/')}}/mpcs/form-9a";
        }
    });
//    $(document).ready(function() {
//     // Initialize date range picker
//     $('#9a_date_ranges').daterangepicker({
//         locale: {
//             format: 'YYYY-MM-DD'
//         },
//         startDate: moment().startOf('month'),
//         endDate: moment().endOf('month')
//     });

//     // Fetch data when date range changes
//     $('#9a_date_ranges').on('apply.daterangepicker', function(ev, picker) {
//         fetchReceiptsData(picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
//     });

//     // Initial load
//     var startDate = moment().startOf('month').format('YYYY-MM-DD');
//     var endDate = moment().endOf('month').format('YYYY-MM-DD');
//     fetchReceiptsData(startDate, endDate);
// });

function fetchReceiptsData() {
      const startDate = $('input#9a_date_ranges').data('daterangepicker').startDate.format('YYYY-MM-DD');
            const endDate = $('input#9a_date_ranges').data('daterangepicker').endDate.format('YYYY-MM-DD');
    $.ajax({
        method: 'GET',
        url: '/mpcs/form-9a',
        data: {
            start_date: startDate,
            end_date: endDate
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateReceiptsTable(response.data);
                 
            } else {
                console.error('Error:', response.msg);
                alert('Error fetching data: ' + response.msg);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            alert('Failed to fetch data. Please try again.');
        }
    });
}

function populateReceiptsTable(data) {
    // Current period data (today)
    const current = data.current;
    // Previous period data (previous day)
    const previous = data.previous;
    // Combined data (total as of today)
    const combined = data.combined;

    // Populate each row with the corresponding data
    // Cash Sales
    $('#form_15a9ab_receipts_table tbody tr:eq(0) td:eq(0)').text(formatCurrency(previous.cash_sales));
    $('#form_15a9ab_receipts_table tbody tr:eq(0) td:eq(2)').text(formatCurrency(current.cash_sales));
    $('#form_15a9ab_receipts_table tbody tr:eq(0) td:eq(3)').text(formatCurrency(combined.cash_sales));

    // Card Sales
    $('#form_15a9ab_receipts_table tbody tr:eq(1) td:eq(0)').text(formatCurrency(previous.card_sales));
    $('#form_15a9ab_receipts_table tbody tr:eq(1) td:eq(2)').text(formatCurrency(current.card_sales));
    $('#form_15a9ab_receipts_table tbody tr:eq(1) td:eq(3)').text(formatCurrency(combined.card_sales));

    // Transport (assuming this is part of other_sales)
    $('#form_15a9ab_receipts_table tbody tr:eq(2) td:eq(0)').text(formatCurrency('')); // Adjust as needed
    $('#form_15a9ab_receipts_table tbody tr:eq(2) td:eq(2)').text(formatCurrency('')); // Adjust as needed
    $('#form_15a9ab_receipts_table tbody tr:eq(2) td:eq(3)').text(formatCurrency('')); // Adjust as needed

    // Empty Cans
    $('#form_15a9ab_receipts_table tbody tr:eq(3) td:eq(0)').text(formatCurrency(previous.empty_barrels));
    $('#form_15a9ab_receipts_table tbody tr:eq(3) td:eq(2)').text(formatCurrency(current.empty_barrels));
    $('#form_15a9ab_receipts_table tbody tr:eq(3) td:eq(3)').text(formatCurrency(combined.empty_barrels));

    // Other
    $('#form_15a9ab_receipts_table tbody tr:eq(4) td:eq(0)').text(formatCurrency(previous.other_sales));
    $('#form_15a9ab_receipts_table tbody tr:eq(4) td:eq(2)').text(formatCurrency(current.other_sales));
    $('#form_15a9ab_receipts_table tbody tr:eq(4) td:eq(3)').text(formatCurrency(combined.other_sales));

    // Total
    $('#form_15a9ab_receipts_table tbody tr:eq(5) td:eq(0)').text(formatCurrency(previous.total_amount));
    $('#form_15a9ab_receipts_table tbody tr:eq(5) td:eq(2)').text(formatCurrency(current.total_amount));
    $('#form_15a9ab_receipts_table tbody tr:eq(5) td:eq(3)').text(formatCurrency(combined.total_amount));

    // Credit Sales
    $('#form_15a9ab_receipts_table tbody tr:eq(6) td:eq(0)').text(formatCurrency(previous.credit_sales));
    $('#form_15a9ab_receipts_table tbody tr:eq(6) td:eq(2)').text(formatCurrency(current.credit_sales));
    $('#form_15a9ab_receipts_table tbody tr:eq(6) td:eq(3)').text(formatCurrency(combined.credit_sales));

    // Changes (if applicable)
    $('#form_15a9ab_receipts_table tbody tr:eq(7) td:eq(0)').text(''); // Adjust as needed
    $('#form_15a9ab_receipts_table tbody tr:eq(7) td:eq(2)').text(''); // Adjust as needed
    $('#form_15a9ab_receipts_table tbody tr:eq(7) td:eq(3)').text(''); // Adjust as needed

    // MPCS Branches (if applicable)
    $('#form_15a9ab_receipts_table tbody tr:eq(8) td:eq(0)').text(''); // Adjust as needed
    $('#form_15a9ab_receipts_table tbody tr:eq(8) td:eq(2)').text(''); // Adjust as needed
    $('#form_15a9ab_receipts_table tbody tr:eq(8) td:eq(3)').text(''); // Adjust as needed
}

// Helper function to format currency
function formatCurrency(amount) {
    return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}


function fetchPaymentsData() {

     const startDate = $('input#9a_date_ranges').data('daterangepicker').startDate.format('YYYY-MM-DD');
            const endDate = $('input#9a_date_ranges').data('daterangepicker').endDate.format('YYYY-MM-DD');
    $.ajax({
        method: 'GET',
        url: '/mpcs/get-payments-data', // Your payments endpoint
        data: {
            start_date: startDate,
            end_date: endDate
        },
        dataType: 'json',
        success: function(response) {
            console.log("response populatePaymentsTable",response);
            if (response.success) {
                populatePaymentsTable(response.data);
              
            } else {
                console.error('Error:', response.msg);
                alert('Error fetching payments data: ' + response.msg);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            alert('Failed to fetch payments data. Please try again.');
        }
    });
}
function updateTextDetails(data) {
    // Create a more detailed display of the text details
    console.log("updateTextDetails",data.text_content);
   
    
    $('#textdetail').html(data.text_content);
}
function formatCurrency(value) {
    return parseFloat(value || 0).toFixed(2);
}

// Calculate and update totals
function updateTotals() {
    // Get input values
    const cashToday = parseFloat($('#cash').val()) || 0;
    const cardToday = parseFloat($('#card').val()) || 0;
    
    // Get previous day values
    const prevCash = parseFloat($('#prev-cash').text()) || 0;
    const prevCard = parseFloat($('#prev-card').text()) || 0;
    const prevTotal = parseFloat($('#prev-total').text()) || 0;
    const prevBalance = parseFloat($('#prev-balance').text()) || 0;
    const prevGrandTotal = parseFloat($('#prev-grand-total').text()) || 0;
    
    // Calculate totals
    const todayTotal = cashToday + cardToday;
    const totalCash = prevCash + cashToday;
    const totalCard = prevCard + cardToday;
    const runningTotal = prevTotal + todayTotal;
    
    // For balance and grand total - adjust these calculations based on your business logic
    const balance = prevBalance + todayTotal; // Example calculation
    const totalBalance = prevBalance + runningTotal;
    const todayGrandTotal = todayTotal + balance; // Example calculation
    const grandTotal = prevGrandTotal + todayGrandTotal;
    
    // Update the table
    $('#total-cash').text(formatCurrency(totalCash));
    $('#total-card').text(formatCurrency(totalCard));
    $('#today-total').text(formatCurrency(todayTotal));
    $('#running-total').text(formatCurrency(runningTotal));
    $('#balance').text(formatCurrency(balance));
    $('#total-balance').text(formatCurrency(totalBalance));
    $('#today-grand-total').text(formatCurrency(todayGrandTotal));
    $('#grand-total').text(formatCurrency(grandTotal));
}

 function populatePaymentsTable(data) {
    // Current period data (today)
   

    const current = data.current;
    // Previous period data (previous day)
    const previous = data.previous;
    // Combined data (total as of today)
    const combined = data.combined;

    // Set previous day values
    $('#prev-cash').text(formatCurrency(previous.cash_payments));
    $('#prev-card').text(formatCurrency(previous.cheque_card_payments));
    $('#prev-total').text(formatCurrency(previous.total_payments));
    $('#prev-balance').text(formatCurrency(previous.balance_in_hand));
    $('#prev-grand-total').text(formatCurrency(previous.grand_total));

    // Set initial input values (today)
    $('#cash').val(formatCurrency(current.cash_payments));
    $('#card').val(formatCurrency(current.cheque_card_payments));

    // Calculate initial totals
    updateTotals();
}

// Helper function to format currency
function formatCurrency(amount) {
    return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}
// Event listeners for input changes
$(document).ready(function() {
    $('#cash, #card').on('input', updateTotals);
    
    // Example of how to call populatePaymentsTable with sample data
    /*
    const sampleData = {
        previous: {
            cash_payments: 1000,
            cheque_card_payments: 500,
            total_payments: 1500,
            balance_in_hand: 2000,
            grand_total: 3500
        },
        current: {
            cash_payments: 0,
            cheque_card_payments: 0
        },
        combined: {
            // Not used in this implementation as we calculate dynamically
        }
    };
    populatePaymentsTable(sampleData);
    */
});
</script>
@endsection