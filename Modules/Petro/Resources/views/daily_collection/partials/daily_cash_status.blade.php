@php
    use App\Business;
    $business_id = request()->session()->get('user.business_id');
    $businessCurrencyPrecise = Business::where('id', $business_id)->first()->currency_precision ?? 2; @endphp
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('petro::lang.daily_cash_status')</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content" style="padding: 4px!important;">
    <div class="row">
        <div class="col-md-12">
            <!-- Your existing filter components remain unchanged -->
            @component('components.filters', ['title' => __('report.filters')])
                <div class="row justify-content-between align-items-end">


                    <!-- Date Picker -->
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group d-flex align-items-center gap-2">
                            {!! Form::label('cash_transaction_date', __('petro::lang.date') . ':', ['class' => 'mb-0']) !!}
                            {!! Form::text('cash_transaction_date', \Carbon\Carbon::now()->format('Y-m-d'), [
                                'class' => 'form-control date-picker',
                                'placeholder' => __('petro::lang.select_a_date'),
                                'autocomplete' => 'off',
                                'id' => 'cash_transaction_date',
                                'style' => 'width: auto; max-width: 100%;'
                            ]) !!}
                        </div>
                    </div>


                    <!-- Shift -->
                    <div class="col-md-3 col-sm-12 " >
                        <div class="col-12 d-flex gap-4 items-center" style="align-items: center; justify-content: space-evenly;">
                            <div class="form-group ">
                                {!! Form::label('shift', __('petro::lang.shift') . ':') !!}

                                {!! Form::select('shift', array_combine($dailyCashShiftNumbers, $dailyCashShiftNumbers), null, [
                                    'class' => 'form-control',
                                    'placeholder' => "Select Shift No",
                                    'id' => 'shift'
                                ]) !!}
                            </div>
                            <i class="fa fa-refresh cursor-pointer" id="refreshShift"></i>
                            <p id="refreshShiftText"></p>
                        </div>

                    </div>


                    <!-- Title -->
                    <div class="col-md-4 col-sm-12">
                        <p style="text-align: end; font-size: 16px; font-weight: 600;">
                            Daily Cash Status No: 1
                        </p>
                    </div>


                </div>
            @endcomponent

            <!-- Balance Display Section -->
            <div @style(['margin-top:10px', 'border: 1px solid #2974A6', 'padding: 40px', 'border-radius: 9px', 'position:relative'])>
                <div class="row d-flex flex-wrap overflow-none" style="gap: 10px; width: 100%;">
                    <!-- Cash Collection -->
                    <div class="flex-fill">
                        <div class="form-group">
                            {!! Form::label('cash_collection', __('petro::lang.cash_collection') . ':', ['class' => 'status-label']) !!}
                            {!! Form::text('cash_collection', null, [
                                'class' => 'form-control status-text',
                                'id' => 'cash_collection',
                                'readonly' => true,
                                'oninput' => 'calculateBalance()'
                            ]) !!}
                        </div>
                    </div>

                    <!-- Customer Payment Cash -->
                    <div class="flex-fill">
                        <div class="form-group" style="width: 170px;">
                            {!! Form::label('customer_payment_cash', __('petro::lang.customer_payment_cash') . ':', ['class' => 'status-label']) !!}
                            {!! Form::text('customer_payment_cash', null, [
                                'class' => 'form-control status-text',
                                'id' => 'customer_payment_cash',
                                'readonly' => true,
                                'oninput' => 'calculateBalance()'
                            ]) !!}
                        </div>
                    </div>

                    <!-- Cash Expenses -->
                    <div class="flex-fill">
                        <div class="form-group">
                            {!! Form::label('cash_expenses', __('petro::lang.cash_expenses') . ':', ['class' => 'status-label']) !!}
                            {!! Form::text('cash_expenses', null, [
                                'class' => 'form-control status-text',
                                'id' => 'cash_expense',
                                'readonly' => true,
                                'oninput' => 'calculateBalance()'
                            ]) !!}
                        </div>
                    </div>

                    <!-- Cash Deposit -->
                    <div class="flex-fill">
                        <div class="form-group">
                            {!! Form::label('cash_deposit', __('petro::lang.cash_deposit') . ':', ['class' => 'status-label']) !!}
                            {!! Form::text('cash_deposit', null, [
                                'class' => 'form-control status-text',
                                'id' => 'cash_deposit',
                                'readonly' => true,
                                'oninput' => 'calculateBalance()'
                            ]) !!}
                        </div>
                    </div>

                    <!-- Cash Total Given -->
                    <div class="flex-fill">
                        <div class="form-group">
                            {!! Form::label('cash_total_given', __('petro::lang.cash_total_given') . ':', ['class' => 'status-label']) !!}
                            {!! Form::text('cash_total_given', null, [
                                'class' => 'form-control status-text',
                                'id' => 'cash_total_given',
                                'readonly' => true,
                                'oninput' => 'calculateBalance()'
                            ]) !!}
                        </div>
                    </div>

                    <!-- Balance in Hand -->
                    <div class="flex-fill">
                        <div class="form-group">
                            {!! Form::label('balance_in_hand', __('petro::lang.balance_in_hand') . ':', ['class' => 'status-label', 'style' => 'color: #0000FF;']) !!}
                            {!! Form::text('balance_in_hand', null, [
                                'class' => 'form-control status-text',
                                'readonly' => true,
                                'id' => 'balance_in_hand',
                                'style' => 'font-weight: bold;'
                            ]) !!}
                        </div>
                    </div>
                </div>

                <!-- Cash to Settle Button -->
                <div class="row" style="margin-top: 20px; margin-left: 10px;">
                    <div class="form-group">
                        <button type="button" class="btn mt-2" style="background-color: #005A9C; color: aliceblue;" id="cashToSettleBtn" onclick="settleCash()">
                            <i class="fas fa-coins mr-2"></i>
                            {{ __('petro::lang.cash_to_settle') }}
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Print Section (hidden until needed) -->
        <div id="printSection" style="display: none;">
            <h2 style="color: #005A9C; text-align: center;">Cash Settlement Report</h2>
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2;">Field</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2;">Amount</th>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Cash Collection</td>
                    <td style="border: 1px solid #ddd; padding: 8px;" id="print_cash_collection"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Customer Payment Cash</td>
                    <td style="border: 1px solid #ddd; padding: 8px;" id="print_customer_payment_cash"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Cash Expenses</td>
                    <td style="border: 1px solid #ddd; padding: 8px;" id="print_cash_expense"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Cash Deposit</td>
                    <td style="border: 1px solid #ddd; padding: 8px;" id="print_cash_deposit"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Cash Total Given</td>
                    <td style="border: 1px solid #ddd; padding: 8px;" id="print_cash_total_given"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px; font-weight: bold; color: #0000FF;">Balance in Hand</td>
                    <td style="border: 1px solid #ddd; padding: 8px; font-weight: bold;" id="print_balance_in_hand"></td>
                </tr>
            </table>
            <div style="margin-top: 20px; text-align: center; font-style: italic;">
                Printed on: <span id="print_date"></span>
            </div>
        </div>

    </div>

        <div class="row" style="margin-top: 20px;">
            <!-- Left column (3 cols) -->
            <div class="col-md-3 cash-details" style="border-right:1px solid #71ADBC;">
                <p style="" class="cash-heading">Detail Cash Collection</p>
                <div class="status-details">
                    <div>
                        <p>Pump Operator</p>
                        <div id="operator_names">

                            <!-- @if (!empty($assigned_operators) && !$is_pending_shift)
                                @foreach($assigned_operators as $key => $operator)
                                    <p>{{ is_array($operator) ? $operator['name'] : $operator }}</p>
                                @endforeach
                            @else
                                <p>No operators assigned.</p>
                            @endif -->

                            <p>No operators assigned.</p>
                        </div>
                        <p>Total</p>

                    </div>
                    <div>
                        <p>Amount</p>
                        <div id="per_operator_total">
                        </div>
                        <p id="pump_operators_total" style="color: brown; font-weight: bolder"></p>
                    </div>
                </div>

            </div>

            <div class="col-md-3 cash-details" style="border-right:1px solid #71ADBC;">
                <p style="" class="cash-heading">Detail Customer Payments</p>
                <div class="status-details">
                    <div>
                        <p>Customer</p>
                        <div class="mt-4" id="customers_names">
                        </div>
                        <p>Total</p>
                    </div>
                    <div>
                        <p>Amount</p>
                        <div class="mt-4" id="customer_expense">
                        </div>
                        <p class="mt-2" id="customer_total" style="color: brown; font-weight: bolder"></p>
                    </div>
                </div>
            </div>

            <!-- Right column (3 cols) -->
            <div class="col-md-3 cash-details">
                <p style="" class="cash-heading">Cash Expenses</p>
                <div class="status-details">
                    <div>
                        <p>Expenses</p>
                        <div class="mt-4" id="cash_expense_name">
                        </div>
                        <p>Total</p>
                    </div>
                    <div>
                        <p>Amount</p>
                        <div class="mt-4" id="cash_expense_cost">
                        </div>
                        <p class="mt-2" id="cash_expense_total" style="color: brown; font-weight: bolder"></p>
                    </div>

                </div>
            </div>

            <div class="col-md-3 cash-details" style="border-left: 1px solid #e5e7eb; background: #f9fafb; padding: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="m-0 font-weight-semibold text-gray-800">
                        <i class="fas fa-money-bill-wave mr-2 text-blue-600"></i>
                        Cash Given Amount
                    </h5>
                    <span class="badge badge-light-blue" id="transaction-count">0</span>
                </div>

                <div class="transaction-card bg-white rounded-lg shadow-xs" style="border: 1px solid #e5e7eb;">
                    <!-- Header -->
                    <div class="transaction-header d-flex px-4 py-3 border-bottom" style="background: #f3f4f6;">
                        <div class="flex-fill font-medium text-xs text-gray-500 uppercase tracking-wider">Issued By</div>
                        <div class="text-right font-medium text-xs text-gray-500 uppercase tracking-wider" style="width: 100px;">Amount</div>
                        <div class="text-right font-medium text-xs text-gray-500 uppercase tracking-wider" style="width: 120px;">Date</div>
                    </div>

                    <!-- Transactions List -->
                    <div class="transaction-list" style="max-height: 250px; overflow-y: auto;">
                        <div id="cash_given_transactions">
                            <!-- Empty state -->
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-money-bill-transfer fa-2x text-gray-300"></i>
                                </div>
                                <p class="text-gray-500 mb-0">No cash disbursements</p>
                                <small class="text-gray-400">Transactions will appear here</small>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="transaction-footer d-flex px-4 py-3 border-top" style="background: #f3f4f6;">
                        <div class="flex-fill font-medium text-gray-700">Total Disbursed</div>
                        <div id="cash_given_total" class="font-semibold text-blue-600" style="width: 100px; text-align: right;">0.00</div>
                        <div style="width: 120px;"></div>
                    </div>
                </div>
            </div>

            <style>
                /* Professional styling */
                .transaction-card {
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
                }

                .transaction-header {
                    border-bottom: 1px solid #e5e7eb;
                }

                .transaction-list {
                    scrollbar-width: thin;
                    scrollbar-color: #d1d5db #f3f4f6;
                }

                .transaction-list::-webkit-scrollbar {
                    width: 6px;
                    height: 6px;
                }

                .transaction-list::-webkit-scrollbar-track {
                    background: #f3f4f6;
                    border-radius: 3px;
                }

                .transaction-list::-webkit-scrollbar-thumb {
                    background-color: #d1d5db;
                    border-radius: 3px;
                }

                .transaction-item {
                    display: flex;
                    align-items: center;
                    padding: 12px 16px;
                    border-bottom: 1px solid #f3f4f6;
                    transition: all 0.2s ease;
                }

                .transaction-item:last-child {
                    border-bottom: none;
                }

                .transaction-item:hover {
                    background-color: #f9fafb;
                }

                .transaction-user {
                    flex: 1;
                    font-weight: 500;
                    color: #111827;
                    display: flex;
                    align-items: center;
                }

                .transaction-user-avatar {
                    width: 28px;
                    height: 28px;
                    border-radius: 50%;
                    background-color: #e5e7eb;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 10px;
                    color: #6b7280;
                    font-size: 12px;
                    font-weight: 600;
                }

                .transaction-amount {
                    width: 100px;
                    text-align: right;
                    font-weight: 600;
                    color: #1e40af;
                    font-family: 'Roboto Mono', monospace;
                }

                .transaction-date {
                    width: 120px;
                    text-align: right;
                    font-size: 13px;
                    color: #6b7280;
                }

                .badge-light-blue {
                    background-color: #eff6ff;
                    color: #1d4ed8;
                    font-weight: 600;
                    padding: 4px 8px;
                    border-radius: 9999px;
                    font-size: 12px;
                }

                .text-blue-600 {
                    color: #2563eb;
                }

                .shadow-xs {
                    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                }
            </style>

            <script>
                function updateTransactionsDisplay() {
                    const container = $('#cash_given_transactions');
                    const totalElement = $('#cash_given_total');
                    const countElement = $('#transaction-count');
                    const currentShift = $('#shift').val();
                    const transactions = cashGivenTransactions.filter(t => t.shiftNumber === currentShift);

                    container.empty();

                    if (transactions.length === 0) {
                        container.append(`
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-money-bill-transfer fa-2x text-gray-300"></i>
                    </div>
                    <p class="text-gray-500 mb-0">No cash disbursements</p>
                    <small class="text-gray-400">Transactions will appear here</small>
                </div>
            `);
                        totalElement.text('0.00');
                        countElement.text('0');
                        return;
                    }

                    // Sort by date (newest first)
                    transactions.sort((a, b) => new Date(b.datetime) - new Date(a.datetime));

                    let total = 0;

                    transactions.forEach(t => {
                        const date = new Date(t.datetime);
                        const formattedDate = date.toLocaleDateString('en-US', {
                            month: 'short',
                            day: 'numeric'
                        });
                        const formattedTime = date.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });

                        total += t.amount;

                        // Get initials for avatar
                        const initials = t.user.split(' ').map(n => n[0]).join('').toUpperCase();

                        container.append(`
                <div class="transaction-item">
                    <div class="transaction-user">
                        <span class="transaction-user-avatar">${initials}</span>
                        ${t.user}
                    </div>
                    <div class="transaction-amount">${t.amount.toFixed({{ $businessCurrencyPrecise }})}</div>
                    <div class="transaction-date">
                        <div>${formattedDate}</div>
                        <small class="text-gray-400">${formattedTime}</small>
                    </div>
                </div>
            `);
                    });

                    totalElement.text(total.toFixed({{ $businessCurrencyPrecise }}));
                    countElement.text(transactions.length);
                }

                $(document).ready(function() {
                    updateTransactionsDisplay();
                    $('#shift').change(updateTransactionsDisplay);
                });
            </script>
        </div>


        <div>
            <button class="close-btn" id="close-shift-btn">
                Close Shift
            </button>
        </div>
{{--    </div>--}}


</section>

<script>
    // Global variables
    let cashGivenTransactions = JSON.parse(localStorage.getItem('cashGivenTransactions')) || [];
    let isPrinting = false;
    let shiftData = {};

    // Initialize form - shows nothing initially
    function initializeForm() {
        const shiftNumber = $('#shift').val();
        if (!shiftNumber) return;

        // Clear all display containers initially
        $('#cash_given_users').empty();
        $('#cash_given_dates').empty();
        $('#cash_given_amounts').empty();
        $('#cash_given_total').text('');

        // Load data but don't display until settlement
        const storedData = localStorage.getItem(`shift_${shiftNumber}`);
        if (storedData) {
            shiftData = JSON.parse(storedData);
            $('#cash_total_given').val(shiftData.accumulatedBalance || '0.00');
        } else {
            shiftData = {
                shiftNumber: shiftNumber,
                accumulatedBalance: 0,
                values: {
                    cash_collection: 0,
                    customer_payment_cash: 0,
                    cash_expense: 0,
                    cash_deposit: 0,
                    cash_total_given: 0,
                    balance_in_hand: 0
                },
                transactions: []
            };
        }

        // Reset input fields
        $('#cash_collection').val('0.00');
        $('#customer_payment_cash').val('0.00');
        $('#cash_expense').val('0.00');
        $('#cash_deposit').val('0.00');
        $('#balance_in_hand').val('');
    }
    function saveShiftData() {
        const shiftNumber = $('#shift').val();
        if (!shiftNumber) return;

        // Update values from form
        shiftData.values = {
            cash_collection: parseFloat($('#cash_collection').val()) || 0,
            customer_payment_cash: parseFloat($('#customer_payment_cash').val()) || 0,
            cash_expense: parseFloat($('#cash_expense').val()) || 0,
            cash_deposit: parseFloat($('#cash_deposit').val()) || 0,
            cash_total_given: parseFloat($('#cash_total_given').val()) || 0,
            balance_in_hand: parseFloat($('#balance_in_hand').val()) || 0
        };

        // Save to localStorage
        localStorage.setItem(`shift_${shiftNumber}_data`, JSON.stringify(shiftData));
    }

    function loadShiftData() {
        const shiftNumber = $('#shift').val();
        if (!shiftNumber) return;

        const savedData = localStorage.getItem(`shift_${shiftNumber}_data`);
        if (savedData) {
            shiftData = JSON.parse(savedData);

            // Update form fields
            $('#cash_collection').val(shiftData.values.cash_collection.toFixed({{ $businessCurrencyPrecise }}));
            $('#customer_payment_cash').val(shiftData.values.customer_payment_cash.toFixed({{ $businessCurrencyPrecise }}));
            $('#cash_expense').val(shiftData.values.cash_expense.toFixed({{ $businessCurrencyPrecise }}));
            $('#cash_deposit').val(shiftData.values.cash_deposit.toFixed({{ $businessCurrencyPrecise }}));
            $('#cash_total_given').val(shiftData.values.cash_total_given.toFixed({{ $businessCurrencyPrecise }}));
            $('#balance_in_hand').val(shiftData.values.balance_in_hand.toFixed({{ $businessCurrencyPrecise }}));

            // Update transactions
            cashGivenTransactions = shiftData.transactions || [];
            updateCashGivenDisplay();
        }
    }
    function addTransaction(transaction) {
        cashGivenTransactions.push(transaction);
        shiftData.transactions = cashGivenTransactions;
        saveShiftData();
        updateCashGivenDisplay();
    }


    // Update cash given amounts display (shows nothing until transactions exist)
    function updateCashGivenDisplay() {
        const currentShift = $('#shift').val();
        const transactions = cashGivenTransactions.filter(t => t.shiftNumber === currentShift);
        const container = $('#cash_given_transactions');
        const totalElement = $('#cash_given_total');

        container.empty();

        if (transactions.length === 0) {
            container.append('<div class="text-center py-5">No transactions</div>');
            totalElement.text('0.00');
            return;
        }

        // Sort by date (newest first)
        transactions.sort((a, b) => new Date(b.datetime) - new Date(a.datetime));

        // Calculate and display total
        const total = transactions.reduce((sum, t) => sum + t.amount, 0);
        totalElement.text(total.toFixed(2));

        // Add each transaction
        transactions.forEach(t => {
            const date = new Date(t.datetime);
            const formattedDate = date.toLocaleDateString() + ' ' +
                date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

            container.append(`
            <div class="transaction-item">
                <div class="transaction-user">${t.user}</div>
                <div class="transaction-amount">${t.amount.toFixed(2)}</div>
                <div class="transaction-date">${formattedDate}</div>
            </div>
        `);
        });

        shiftData.transactions = cashGivenTransactions;
        saveShiftData();

        // Ensure Cash Total Given matches the sum
        // $('#cash_total_given').val(total);
    }

    // Calculate balance function - UPDATED to properly update the field
    // Save balance to localStorage whenever it changes
    function calculateBalance() {
        const cashCollection = parseFloat($('#cash_collection').val()) || 0;
        const customerPaymentCash = parseFloat($('#customer_payment_cash').val()) || 0;
        const cashExpenses = parseFloat($('#cash_expense').val()) || 0;
        const cashDeposit = parseFloat($('#cash_deposit').val()) || 0;
        const cashTotalGiven = parseFloat($('#cash_total_given').val()) || 0;

        const balance = (cashCollection + customerPaymentCash) - (cashExpenses + cashDeposit + cashTotalGiven);
        const balanceValue = balance.toFixed({{ $businessCurrencyPrecise }});

        // Save to localStorage
        const shiftNumber = $('#shift').val();
        if (shiftNumber) {
            localStorage.setItem(`balance_shift_${shiftNumber}`, balanceValue);
        }

        $('#balance_in_hand').val(balanceValue);
        return balance;
    }

    // Load balance from localStorage on page load
    function loadBalance() {
        const shiftNumber = $('#shift').val();
        if (shiftNumber) {
            const savedBalance = localStorage.getItem(`balance_shift_${shiftNumber}`);
            if (savedBalance) {
                $('#balance_in_hand').val(savedBalance);
            }
        }
    }

    // Update your $(document).ready to call loadBalance()
    $(document).ready(function() {
        initializeForm();
        updateCashGivenDisplay();
        loadBalance(); // Load saved balance

        // Recalculate when fields change
        $('#cash_collection, #customer_payment_cash, #cash_expense, #cash_deposit, #cash_total_given').on('input change', function() {
            calculateBalance();
        });
    });

    // Print settlement functionality - UPDATED to use calculateBalance()
    function printSettlement() {
        const currentBalance = calculateBalance();

        // Update print section with current values
        $('#print_cash_collection').text($('#cash_collection').val() || '0.00');
        $('#print_customer_payment_cash').text($('#customer_payment_cash').val() || '0.00');
        $('#print_cash_expense').text($('#cash_expense').val() || '0.00');
        $('#print_cash_deposit').text($('#cash_deposit').val() || '0.00');
        $('#print_cash_total_given').text($('#cash_total_given').val() || '0.00');
        $('#print_balance_in_hand').text(currentBalance.toFixed({{ $businessCurrencyPrecise }}) || '0.00');
        $('#print_date').text(new Date().toLocaleString());

        // Show and print
        const printSection = $('#printSection');
        printSection.show();

        setTimeout(function() {
            window.print();
            printSection.hide();
        }, 100);
    }

    // Cash to Settle button click handler - UPDATED
    // Cash to Settle button click handler - UPDATED to ensure balance is recalculated
    // Cash to Settle button click handler - COMPLETELY REVISED
    $('#cashToSettleBtn').click(function() {
        console.log("--- Starting Settlement Process ---");

        // 1. Get ALL current values directly from input fields
        const values = {
            cashCollection: parseFloat($('#cash_collection').val()) || 0,
            customerPaymentCash: parseFloat($('#customer_payment_cash').val()) || 0,
            cashExpenses: parseFloat($('#cash_expense').val()) || 0,
            cashDeposit: parseFloat($('#cash_deposit').val()) || 0,
            cashTotalGiven: parseFloat($('#cash_total_given').val()) || 0
        };

        console.log("Current Values:", values);

        // 2. Calculate current balance MANUALLY
        const currentBalance = (values.cashCollection + values.customerPaymentCash) -
            (values.cashExpenses + values.cashDeposit + values.cashTotalGiven);

        console.log("Calculated Balance:", currentBalance);

        // 3. FORCE update the balance field IMMEDIATELY
        $('#balance_in_hand').val(currentBalance.toFixed({{ $businessCurrencyPrecise }}));
        console.log("Balance Field Updated To:", $('#balance_in_hand').val());

        // if (currentBalance <= 0) {
        //     alert('Balance must be positive to settle');
        //     return;
        // }

        if (confirm(`Settle amount: ${currentBalance.toFixed(2)}\nConfirm settlement?`)) {
            console.log("--- Settlement Confirmed ---");

            // 4. Create transaction record
            const newTransaction = {
                shiftNumber: $('#shift').val(),
                user: "{{ Auth::user()->name }}",
                datetime: new Date().toISOString(),
                amount: currentBalance,
                details: values
            };

            // 5. Update transactions storage
            cashGivenTransactions.push(newTransaction);
            localStorage.setItem('cashGivenTransactions', JSON.stringify(cashGivenTransactions));

            // 6. Update Cash Total Given
            const newCashTotalGiven = values.cashTotalGiven + currentBalance;
            $('#cash_total_given').val(newCashTotalGiven.toFixed({{ $businessCurrencyPrecise }}));
            console.log("Updated Cash Total Given:", $('#cash_total_given').val());

            // 7. Calculate FINAL balance after settlement
            const finalBalance = (values.cashCollection + values.customerPaymentCash) -
                (values.cashExpenses + values.cashDeposit + newCashTotalGiven);

            // 8. FORCE update balance field AGAIN
            $('#balance_in_hand').val(finalBalance.toFixed({{ $businessCurrencyPrecise }}));
            console.log("Final Balance Updated To:", $('#balance_in_hand').val());

            updateCashGivenDisplay();

            // 9. Print receipt
            printReceipt(values, newCashTotalGiven, finalBalance);

            // 10. Reset form fields
            setTimeout(() => {
                // $('#cash_collection').val('0.00');
                // $('#customer_payment_cash').val('0.00');
                // $('#cash_expense').val('0.00');
                // $('#cash_deposit').val('0.00');
                // $('#balance_in_hand').val('0.00');
                console.log("--- Fields Reset After Settlement ---");
                toastr.success('Settlement completed successfully');
            }, 100);
        }
    });
    function saveCurrentValues() {
        const shiftNumber = $('#shift').val();
        if (!shiftNumber) return;

        const values = {
            cashCollection: parseFloat($('#cash_collection').val()) || 0,
            customerPaymentCash: parseFloat($('#customer_payment_cash').val()) || 0,
            cashExpenses: parseFloat($('#cash_expense').val()) || 0,
            cashDeposit: parseFloat($('#cash_deposit').val()) || 0,
            cashTotalGiven: parseFloat($('#cash_total_given').val()) || 0
        };

        localStorage.setItem(`shift_${shiftNumber}_values`, JSON.stringify(values));
    }

    function addToCategory(category, amount) {
        const current = parseFloat($(`#${category}`).val()) || 0;
        $(`#${category}`).val((current + amount).toFixed({{ $businessCurrencyPrecise }}));
        saveCurrentValues();
    }
    // PRINT RECEIPT FUNCTION
    function printReceipt(values, totalGiven, finalBalance) {
        $('#print_cash_collection').text(values.cashCollection.toFixed({{ $businessCurrencyPrecise }}));
        $('#print_customer_payment_cash').text(values.customerPaymentCash.toFixed({{ $businessCurrencyPrecise }}));
        $('#print_cash_expense').text(values.cashExpenses.toFixed({{ $businessCurrencyPrecise }}));
        $('#print_cash_deposit').text(values.cashDeposit.toFixed({{ $businessCurrencyPrecise }}));
        $('#print_cash_total_given').text(totalGiven.toFixed({{ $businessCurrencyPrecise }}));
        $('#print_balance_in_hand').text(finalBalance.toFixed({{ $businessCurrencyPrecise }}));
        $('#print_date').text(new Date().toLocaleString());

        const printSection = $('#printSection');
        printSection.show();

        setTimeout(() => {
            window.print();
            printSection.hide();
        }, 100);
    }

    // DEBUGGING: Add event listener to monitor balance field changes
    $('#balance_in_hand').on('change input', function() {
        console.log("Balance Field Changed:", $(this).val());
    });

    // Document ready function - UPDATED with proper event handlers
    $(document).ready(function() {
        initializeForm();
        updateCashGivenDisplay();

        // Set up event handlers
        $('#shift').change(function() {
            initializeForm();
            setTimeout(calculateBalance, 500); // Small delay to allow AJAX to complete
        });

        // Recalculate balance when any relevant field changes
        $('#cash_collection, #customer_payment_cash, #cash_expense, #cash_deposit, #cash_total_given').on('input change', function() {
            calculateBalance();
        });

        $('#close-shift-btn').click(function() {
            if (confirm('Close this shift?')) {
                const shiftNumber = $('#shift').val();
                localStorage.removeItem(`shift_${shiftNumber}`);
                localStorage.removeItem('cashGivenTransactions');
                window.location.reload();
            }
        });
    });
</script>

<style>
    .cash-details {
        padding: 10px;
        height: 100%;
    }

    .cash-heading {
        font-weight: bold;
        margin-bottom: 15px;
        font-size: 16px;
        color: #333;
    }

    .status-details {
        display: flex;
        gap: 15px;
    }

    .status-details > div {
        flex: 1;
    }

    .status-details p {
        margin-bottom: 8px;
        font-size: 14px;
        color: #333;
    }

    #cash_given_users div,
    #cash_given_dates div,
    #cash_given_amounts div {
        margin-bottom: 12px;
        font-size: 13px;
    }

    #cash_given_amounts div {
        text-align: right;
        padding-right: 8px;
    }

    #cash_given_total {
        font-size: 14px;
        margin-top: 5px;
        text-align: right;
        padding-right: 8px;
    }

    .mt-4 {
        margin-top: 1rem;
    }

    .mt-2 {
        margin-top: 0.5rem;
    }
</style>



<script>

    $(document).ready(function () {
        $('#shift').on('change', function () {
            let shift = $(this).val();

            if (shift) {
                $.ajax({
                    url: "{{ route('daily.cash.status.data') }}",
                    type: 'GET',
                    data: {shift: shift},
                    success: function (response) {
                        // Assuming the API returns an object like { cash_collection: 1234.56 }
                        if (response.cash_collection !== undefined) {
                            $('input[name="cash_collection"]').val(response.cash_collection);
                        } else {
                            $('input[name="cash_collection"]').val('');
                        }
                        if (response.operators) {
                            const operators = response.operators.operators_payment || [];
                            const totalAmount = response.operators.total_amount || 0;

                            let nameHtml = '';
                            let amountHtml = '';

                            if (operators.length > 0) {
                                operators.forEach(op => {
                                    nameHtml += `<p>${op.name}</p>`;
                                    amountHtml += `<p>${parseFloat(op.total_amount).toFixed({{$businessCurrencyPrecise}})}</p>`;
                                });
                            } else {
                                nameHtml = '<p>No operators assigned.</p>';
                            }

                            $('#operator_names').html(nameHtml);
                            $('#per_operator_total').html(amountHtml);
                            $('#pump_operators_total').html(`<strong>${totalAmount}</strong>`);

                        }

                        // Top summary
                        $('#cash_collection').val(parseFloat(response.cash_collection).toFixed({{$businessCurrencyPrecise}}));
                        $('#other_income_cash').val(parseFloat(response.other_income_cash).toFixed({{$businessCurrencyPrecise}}));
                        {{--$('#customer_payment_cash').val(parseFloat(response.customer_payment).toFixed({{$businessCurrencyPrecise}})); // ❗ You might want to make this dynamic?--}}
                        $('#customer_payment_cash').val(parseFloat(response.customer_payment_list.total).toFixed({{$businessCurrencyPrecise}})); // ❗ You might want to make this dynamic?

                        $('#cash_expense').val(parseFloat(response.cash_expenses).toFixed({{$businessCurrencyPrecise}}));
                        $('#cash_deposit').val(parseFloat(response.cash_deposit).toFixed({{$businessCurrencyPrecise}}));
                        $('#balance_in_hand').val(parseFloat(response.balance_in_hand).toFixed({{$businessCurrencyPrecise}}));

                        // 1st col: Pump Operators
                        let pump_total = response.operators.total_amount.toFixed({{$businessCurrencyPrecise}})
                        $('#pump_operators_total').text(pump_total);
                        // response.operators.operators_payment.forEach(item => {
                        //     $('#operator_names').append(`<p>${item.name}</p>`);
                        //     $('#per_operator_total').append(`<p>${parseFloat(item.total_amount).toFixed({{$businessCurrencyPrecise}})}</p>`);
                        // });

                        // 2nd col: Customers
                        let customer = response.customer_payment_list.total.toFixed({{$businessCurrencyPrecise}})
                        $('#customer_total').text(customer);
                        $('#customers_names').empty()
                        $('#customer_expense').empty()
                        response.customer_payment_list.list.forEach(item => {
                            $('#customers_names').append(`<p>${item.customer_name}</p>`);
                            $('#customer_expense').append(`<p>${parseFloat(item.amount).toFixed({{$businessCurrencyPrecise}})}</p>`);
                        });
                        // 3rd col: Expenses
                        let cash_expense = response.expense_total.expense_all.toFixed({{$businessCurrencyPrecise}})
                        $('#cash_expense_total').text(cash_expense);
                        $('#cash_expense_total2').text(cash_expense);
                        $('#cash_expense_name').empty();
                        $('#cash_expense_cost').empty()
                        response.expense_total.expenses.forEach(item => {
                            $('#cash_expense_name').append(`<p>${item.expense_name}</p>`);
                            $('#cash_expense_cost').append(`<p>${parseFloat(item.amount).toFixed({{$businessCurrencyPrecise}})}</p>`);
                        });

                    },
                    error: function () {
                        $('input[name="cash_collection"]').val('');
                        alert('Error fetching cash collection data.');
                    }
                });
            } else {
                $('input[name="cash_collection"]').val('');
            }
        });
        $("#refreshShift").click(function (){
            $('#refreshShiftText').text('refreshing...')
            $.ajax({
                url: "{{ action('\Modules\Petro\Http\Controllers\DailyShiftController@fetchOpenShift') }}",
                type: 'GET',
                success: function (response) {
                    $('#refreshShiftText').empty()
                    const $select = $('#shift');
                    $select.empty(); // clear existing options

                    $select.append('<option value="">Select an option</option>'); // optional default

                    $.each(response, function(key, value) {
                        $select.append('<option value="' + value + '">' + value + '</option>');
                    });

                },
                error: function () {
                    alert('Error fetching  data.');
                }
            });

        })

        $('#close-shift-btn').on('click', function () {
            const shiftId = $('#shift').val();

            if (!shiftId) {
                alert('Please select a shift number first.');
                return;
            }
            $('#close-shift-btn').prop('disabled', true)
                .html('<i class="fa fa-spinner fa-spin"></i> closing...');
            $.ajax({
                url: "{{ route('daily_shift_status.close') }}",
                method: 'POST',
                data: {
                    shift_id: shiftId,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    $(`#shift option[value='${shiftId}']`).remove();
                    toastr.success('Shift Saved Successfully');
                    $('#close-shift-btn').prop('disabled', false)
                        .html('Close Shift');
                    window.location.reload()
                },
                error: function (xhr) {
                    alert(xhr.responseJSON.message || 'Failed to close shift.');
                }
            });


        });

    });
</script>

