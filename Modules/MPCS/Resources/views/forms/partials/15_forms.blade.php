<section class="content">


    <style>
        .editable {
            display: inline-block;
            min-width: 80px;
            padding: 3px 6px;
            border-bottom: 1px dotted #999;
            cursor: text;
            color: #000;
        }

        .editable:empty::before {
            content: '.............';
            color: #ccc;
        }

        .editable:focus {
            outline: none;
            background-color: #fdfdfd;
        }

        #form_f15_table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            color: #333;
            margin-top: 20px;
        }


        #form_f15_table thead {

            color: #333;
            font-weight: bold;
        }

        #form_f15_table th,
        #form_f15_table td {
            padding: 5px 12px; /* reduced from 7px 15px */
            text-align: center;
            border: 1px solid #ddd;
        }


        #form_f15_table th {}

        @media (max-width: 768px) {

            #form_f15_table th,
            #form_f15_table td {
                font-size: 12px;
                padding: 8px 10px;
            }
        }

        .total-row {
            background-color: #f1f1f1;
            font-weight: bold;
            color: #000;
        }

        .card-sale-header {
            font-style: italic;
            background-color: #f9f9f9;
        }

        .card-sale-detail {
            font-style: italic;
            background-color: #f9f9f9;
            padding-left: 50px;
        }

        .total-row td {
            text-align: right;
        }

        #form_f15_table td {
            font-size: 14px;
        }

        #form_f15_table th,
        #form_f15_table td {
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }

        #form_f15_table tbody tr {
            border-bottom: 2px solid #f0f0f0;
        }


        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td {
            height: 10px;
        }

        table td[align="right"] {
            text-align: right;
            padding-right: 10px;
        }

        .no-border-table {
            border-collapse: collapse;
            width: 100%;
        }

        .no-border-table tr {
            border: none !important;
        }

        .no-border-table td {
            border: none !important;
            padding: 8px;
        }

        .no-border-table td,
        .no-border-table th {
            border-bottom: none !important;
        }

        .note-container {
            width: 100%;
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
            /*border: 1px solid black;*/
            box-sizing: border-box;
        }

        .dataTables_filter,
        .dataTables_info {
            display: none;
        }

        .dots {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .dots::before {
            content: "";
            flex-grow: 1;
            border-bottom: 1px dotted black;
            margin: 0 5px;
        }

        /* Styling for print */
        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
            }

            /* Hide non-printable elements */
            #printButton,
            .dataTables_filter,
            .dataTables_info,
            .table-responsive .no-print {
                display: none;
            }

            /* Adjust table layout */
            .table-responsive {
                width: 100%;
                margin: 0;
            }

            /* Style adjustments for tables */
            .no-border-table {
                width: 100%;
                margin-top: 20px;
            }

            /* Ensure all the content fits within the page */
            @page {
                margin: 20mm;
            }

            .no-print,
            .no-print * {
                display: none !important;
            }

            .header-area,
            .header-area * {
                display: none !important;
            }

            .nav,
            .nav-tabs * {
                display: none !important;
            }

            .page-title-area * {
                display: none !important;
            }

            .dots {
                display: flex;
                justify-content: space-between;
                width: 100%;
            }

            .dots::before {
                content: "";
                flex-grow: 1;
                border-bottom: 1px dotted black;
                margin: 0 5px;
            }
        }

        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-width: 250px;
            max-width: 400px;
        }

        .custom-alert.success {
            background-color: #28a745;
        }

        .custom-alert.error {
            background-color: #dc3545;
        }

        .custom-alert button {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            margin-left: 10px;
            cursor: pointer;
        }
    </style>@if(session('success') || session('error'))
    <div id="custom-alert" class="custom-alert {{ session('success') ? 'success' : 'error' }}">
        <span>{{ session('success') ?? session('error') }}</span>
        <button onclick="closeAlert()">×</button>
    </div>
    @endif



    <!-- Filter Section (Hidden for Print) -->
    <div class="row d-flex justify-content-center no-print" style="">
        <div class="col-md-3 justify-content-center" style="">
            <!-- <input type="text" class="form-control datepicker" id="date" placeholder="Select Date"> -->
            <div class="form-group">
                {!! Form::text(
                'date_range',
                @format_date('first day of this month') .
                ' ~ ' .
                @format_date('last day of this month'),
                [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'form_15_date_range',
                'readonly',
                ],
                ) !!}
            </div>

        </div>


        <div class="col-md-2" style="padding-top: 0; margin-top: 0;">
            <div class="form-group text-center">
                <button class="btn btn-primary form-control" id="searchButton">Search</button>
            </div>
        </div>
        <div class="col-md-2" style="padding-top: 0; margin-top: 0;">
            <div class="form-group text-center">
                <button class="btn btn-success form-control" id="printButton">Print</button>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-12">
            <div class="container">
                <div class="row" style="margin-top: 5px;">
                    <div class="col-sm-10 text-center">

                        <h3>{{ $business_name }}</h3>
                        <h5>Filling Station <br></h5>
                    </div>
                    <!-- date and time Ref form number added on table -->

                    <!-- end date and time Ref form -->

                    <h3 style="text-align: right;">F15</h3>
                    <h5 style="font-size: 15px;text-align: right;font-weight: bolder;margin-top: 8px;">Form No. &nbsp; {{ $next_form_number }}</h5>

                    <table width="100%">
                        <tr>
                            <td width="20%"></td>
                            <td width="18%" style="text-align: left;">Amount</td>
                            <td width="22%"></td>
                            <td width="20%">Purchases</td>
                            <td width="20%">Amount</td>
                        </tr>
                        <tr>
                            <td>Balance in Hand</td>
                            <td><input type="number" class="form-control form-input" placeholder="0.00" step="0.01" /></td>
                            <td></td>
                            <td>Other Payments</td>
                            <td><input type="number" class="form-control form-input" placeholder="0.00" step="0.01" /></td>
                        </tr>
                        <tr>
                            <td>Received</td>
                            <td><input type="number" class="form-control form-input" placeholder="0.00" step="0.01" /></td>
                            <td></td>
                            <td>Balance in Hand</td>
                            <td><input type="number" class="form-control form-input" placeholder="0.00" step="0.01" /></td>
                        </tr>
                        <tr>
                            <td>Balance Stock Note</td>
                            <td><input type="number" class="form-control form-input" placeholder="0.00" step="0.01" /></td>
                            <td></td>
                            <td>For the Sale Price</td>
                            <td><input type="number" class="form-control form-input" placeholder="0.00" step="0.01" /></td>
                        </tr>
                    </table>
                </div>
            </div>




            <table id="form_f15_table">
                <thead>
                <tr>
                    <th>Sale Maximum Limit</th>
                    <th colspan="1">. . . . . . . .</th>
                    <th colspan="1"></th>
                    <th>Minimum</th>
                    <th colspan="1">. . . . . . . .</th>
                </tr>
                <tr>
                    <th>Description</th>
                    <th>Ref Book No</th>
                    <th>Up to Previous Date<br><small>(Rs / Cts)</small></th>
                    <th>Today<br><small>(Rs / Cts)</small></th>
                    <th>As of Today<br><small>(Rs / Cts)</small></th>
                </tr>
                </thead>
                <tbody>
                <!-- Data will be filled via DataTable -->
                </tbody>
            </table>


            <!-- Remove Note outline -->

            <div class="note-container">
                <div class="form-group" style="margin-top: 80px;">
                    <textarea rows="3" cols="160" class="form-control" required style="padding: 20px;">Note </textarea>
                </div>
            </div>


            <!-- End -->







            <table width="100%" style="margin-top: 100px;" class="no-border-table">
                <tr>
                    <td align="center" width="50%">.............................. <br> Checked By</td>
                    <td align="center" width="50%">.............................. <br> Manager</td>
                </tr>
            </table>
        </div>

    </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // Print functionality
        $('#printButton').on('click', function() {
            window.print();
        });
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
        });

        function closeAlert() {
            document.getElementById("custom-alert").style.display = "none";
        }

        // Auto-hide the alert after 3 seconds
        setTimeout(function() {
            if (document.getElementById("custom-alert")) {
                document.getElementById("custom-alert").style.display = "none";
            }
        }, 3000);


        let f15_table = $('#form_f15_table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            bPaginate: false,
            ordering: false,
            searching: false,
            info: false,
            ajax: {
                url: '/mpcs/f15/header/get-form-f15-data',
                type: 'GET',
                data: function (d) {
                    d.startDate = $('#startDate').val();
                    d.endDate = $('#endDate').val();
                },
                dataSrc: function (json) {
                    const structure = [
                        { description: 'Store Purchase', type: 'normal' },
                        { description: 'Direct Purchase', type: 'normal' },
                        { description: 'Sub Total', type: 'normal' },
                        { description: 'Price Increment', type: 'normal' },
                        { description: 'Changes', type: 'normal' },
                        { description: 'Total', type: 'total' },
                        { description: 'Opening Stock', type: 'normal' },
                        { description: 'Grand Total', type: 'normal' },
                        { description: 'Cash Sale', type: 'sale' },
                        { description: 'Card Sale', type: 'card-sale-header' },
                        { description: '', type: 'card-sale-detail' },
                        { description: '', type: 'card-sale-detail' },
                        { description: '', type: 'card-sale-detail' },
                        { description: 'Credit Sale', type: 'normal' },
                        { description: 'Total', type: 'total' },
                        { description: 'Changes (18)', type: 'normal' },
                        { description: 'Price Reduction', type: 'normal' },
                        { description: 'Damaged', type: 'normal' },
                        { description: 'Others', type: 'normal' },
                        { description: 'Total Return', type: 'normal' },
                        { description: 'Total', type: 'total' },
                        { description: 'Balance Stock in Sale Price', type: 'normal' },
                        { description: 'Grand Total', type: 'total' }
                    ];

                    return structure.map(item => ({
                        description: item.description,
                        ref_book_no: '',
                        previous_date_rupees: '',
                        previous_date_cts: '',
                        today_rupees: '',
                        today_cts: '',
                        as_of_today_rupees: '',
                        as_of_today_cts: '',
                        type: item.type
                    }));
                }
            },
            columns: [
                {
                    data: 'description',
                    render: function (data, type, row) {
                        if (row.type === 'total') {
                            return `<span style="float: right; padding-right: 10px;">${data}</span>`;
                        }
                        return data;
                    }
                },
                {
                    data: 'ref_book_no',
                    render: function (data, type, row) {
                        if (row.type === 'card-sale-detail') {
                            return '';
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return mergedAmountInput(row.previous_date_rupees, row.previous_date_cts);
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return mergedAmountInput(row.today_rupees, row.today_cts);
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return mergedAmountInput(row.as_of_today_rupees, row.as_of_today_cts);
                    }
                }
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.type === 'total') {
                    $(row).addClass('total-row');
                    $(row).css('font-weight', 'bold');
                }

                if (data.type === 'card-sale-detail') {
                    $(row).find('td:first').attr('colspan', '5');
                    $(row).find('td:not(:first)').remove();
                    $(row).find('td:first').css({
                        'padding-left': '50px',
                        'font-style': 'italic'
                    });
                }

                if (data.type === 'card-sale-header') {
                    $(row).find('td:nth-child(2)').css('font-style', 'italic');
                }
            }
        });

// Helper function to merge rupees and cents into one input field
        function mergedAmountInput(rupees, cts) {
            rupees = rupees || '0';
            cts = cts || '00';
            const value = `${parseFloat(rupees).toFixed(0)}.${cts.toString().padStart(2, '0')}`;
            // return `<input type="text" class="form-control" style="width:80px;" value="${value}" />`;
            return value == '0.00' ? '' : value;
        }


        // Format numbers to add decimal and commas
        function formatNumber(number) {
            if (number === '' || isNaN(number)) {
                return '';
            }

            let formatted = parseFloat(number).toFixed(2); // Fix to 2 decimals
            return new Intl.NumberFormat('en-US').format(formatted); // Add commas
        }

        // Handle date range filtering
        $('#searchButton').on('click', function() {
            f15_table.ajax.reload();
        });
    });
</script>