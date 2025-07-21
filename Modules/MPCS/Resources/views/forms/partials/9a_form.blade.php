
<style>
    #form_9a_tables {
        border-collapse: collapse;
        width: 100%;
    }

    #form_9a_tables thead th {
        position: sticky;
        top: 0;
        background: #fff; /* or any color you want */
        z-index: 10;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4); /* Optional: a little shadow */
    }
</style>


<!-- Main content -->
<section class="content"  style="padding:0px;">    
     
<div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3" id="location_filter">
                    <div class="form-group">
                        {!! Form::label('form_9a_location_id', __('purchase.business_location') . ':') !!}

                        {!! Form::select('form_9a_location_id', $business_locations, $business_locations->keys()->first(), [
                            'id' => 'form_9a_location_id',
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}


                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('form_16a_date', __('report.date') . ':') !!}
                        <div class="dropdown">
                     

                        {!! Form::text(
                    'date_range',
                    @format_date('first day of this month') .
                        ' ~ ' .
                        @format_date('last                                                                                                                                                                                    day of this month'),
                    [
                        'placeholder' => __('lang_v1.select_a_date_range'),
                        'class' => 'form-control',
                        'id' => '9a_date_ranges',
                        'readonly',
                    ],
                ) !!}
                        </div>
                    </div>

                </div>


                
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            @slot('tool')
            <div class="box-tools">
                <!-- Standard Print button -->
                <button class="btn btn-primary print_report pull-right" id="print_div">
                    <i class="fa fa-print"></i> @lang('messages.print')</button>
            </div>
            @endslot
            <div class="col-md-12">

<div class="row" style="margin-top: 20px;" id="print_content">
                    <style>
                    </style>
                    <div class="col-md-12">
                    <div class="col-md-10">

                        <h4 class="text-center">
                        {{ request()->session()->get('business.name') }}                               
                        
                       </h4>
                       
                    </div>
                   <div class="col-md-1">
                   <b>Form  No: <span class="9c_from_date">{{$form_number}}</span> </b>
                    </div>
                    <div class="col-md-1">

                    <h4 class="text-right">
                        F9A
                    <h2>

                    </div>
                    <div class="col-md-10">
                        <h4 class="text-center" style="margin:10px;">@lang('mpcs::lang.daily_sales_report')</h4>
                    </div>
                    
                    <div class="row">
                    <div xlass="col-md-12">
                    <div class="col-md-4 text-right"  >                                              
                    </div>
                    <div class="col-md-4 ">
                   
                    <div class="dropdown-date">
                     
                    <h5 style="font-weight: bold;"><span id="openingdate"></span></h5>
                        
</div>
           </div>
           <div class="col-md-3">
               <div class="text-right">
                   <h5 style="font-weight: bold;">Today Filling Station Report </h5>
               </div>
           </div>
           <div class="col-md-1">
               <div class="text-center pull-left">
                  
               </div>
           </div>
                    </div>
                    </div>
                        <div class="row">
                            <div class="col-md-12">
                               
                                <div class="table-responsive">
                               
                                  <table class="table table-bordered table-striped" id="form_9a_tables" style="max-height: 600px; overflow-y: auto;">
    <thead class="align-middle">
        <tr class="align-middle text-center">
            <th class="align-middle text-center" rowspan="2">@lang('mpcs::lang.description')</th>
            <th class="align-middle text-center">Total Sale</th>
            <th class="align-middle text-center">Card Sale</th>
            <th class="align-middle text-center">Cash Sale</th>
            <th class="align-middle text-center">Empty Barrels</th>
            <th class="align-middle text-center">Others</th>
            <th class="align-middle text-center">Total</th>
            <th class="align-middle text-center">With Taxes</th>
            <th class="align-middle text-center">Without Taxes</th>
            <th class="align-middle text-center" rowspan="2">Office Use</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Cash</th>
            <td></td>
            <td id="card_sales"></td>
            <td id="cash_sales"></td>
            <td></td>
            <td></td>
            <td id="total_cash_sale"></td>
            <td></td>
            <td></td>
            <td rowspan="7">
                <p>Received On ...........................</p>
                <p>Checked ................................</p>
                <p>Approved .............................</p>
                <p style="text-align: center;">After Checking</p>
                <p style="display: flex; justify-content: center; gap: 10px;">
                    <span style="text-decoration-line: underline;"> Short Money </span>
                    <span style="text-decoration-line: underline;"> Excess Money </span>
                </p>
                <p>Today ...................................</p>
                <p>Previous Day ........................</p>
                <p>As of Today ...........................</p>
            </td>
        </tr>
        <tr>
            <th>Deposit / Credit Sales</th>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td id="total_credit_sale"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th> &nbsp; </th>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th>MPCS Branches</th>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th>Today Sale</th>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td id="total_sale"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th>Total Sale up to Previous Day</th>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td id="total_sale_pre_day"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th>Total Sale as of Today</th>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td id="total_sale_today"></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
<div class="col-md-12 row">
            <!-- Left Side - Receipts Table and Numbered Items -->
            <div class="col-md-6">
                
                
                <!-- Receipts Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="form_15a9ab_receipts_table">
                        <thead>
                            <tr>
                                <th colspan="4" class="text-center">
                                    <h4>Receipts</h4>
                                </th>
                            </tr>
                            <tr>
                                <th>Previous Day</th>
                        <th>Description</th>
                        <th>Today</th>
                        <th>Total as of Today</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td>Cash Sales</td>                                
                                <td></td>
                                <td></td>
                                
                            </tr>
                            <tr>
                                <td></td>
                                <td>Card Sales</td>                                
                                <td></td>
                                <td></td>
                                
                            </tr>
                            <tr>
                                <td></td>
                                <td>Transport</td>                              
                                <td></td>
                                <td></td>
                                
                            </tr>
                            <tr>
                                <td></td>
                                <td>Empty Cans</td>
                                <td></td>
                                <td></td>
                                
                            </tr>
                            <tr>
                                <td></td>
                                <td>Others Total</td>
                                <td></td>
                                <td></td>
                                
                            </tr>
                            <tr>
                                <td></td>
                                <td>Balance in Hand Total</td>
                                <td></td>
                                <td></td>
                                
                            </tr>
                            <tr>
                                <td></td>
                                <td>Credit Sales</td>
                                <td></td>
                                <td></td>
                                
                            </tr>
                            <tr>
                                <td></td>
                                <td>Changes</td>
                                <td></td>
                                <td></td>
                                
                            </tr>
                            <tr>
                                <td></td>
                                <td>MPCS Branches</td>
                                <td></td>
                                <td></td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
        <!-- Numbered Items -->
            <div class="form-group" style="margin-bottom: 20px;">
                <div style="margin-bottom: 10px;">
                    1. Cash Bill No ................<br>
                    2. Deposit Bill No ................<br>
                    3. Credit Bill No ................<br>
                    4. MPCS Branches ................<br>
                    Date ..............
                </div>
            </div>
    </div>

    <!-- Right Side - Payments Table and Signature Area -->
    <div class="col-md-6">
        <!-- Payments Table -->
        <div class="table-responsive">
              <table class="table table-bordered table-striped" id="form_15a9ab_payments_table">
    <thead>
        <tr>
            <th colspan="5" class="text-center">
                <h4>Payments</h4>
            </th>
        </tr>
        <tr>
            <th>Previous Day</th>
            <th colspan="2">Description</th>
            <th>Today</th>
            <th>Total as of Today</th>
        </tr>
    </thead>
    <tbody>
        <!-- Row 1 - Cash -->
        <tr>
            <td class="text-right" id="prev-cash">0.00</td>
            <td class="text-left">Cashier Receipt No</td>
            <td class="text-left">Cash</td>
            <td class="text-center">
                <input type="number" class="form-control text-center" id="cash" placeholder="0.00">
            </td>
            <td class="text-center" id="total-cash">0.00</td>
        </tr>
        
        <!-- Row 2 - Card/Cheque -->
        <tr>
            <td class="text-right" id="prev-card">0.00</td>
            <td class="text-center">
                <input type="text" class="form-control text-center" id="receiptno" placeholder="--------------">
            </td>
            <td class="text-left">Cheques/Cards</td>
            <td class="text-center">
                <input type="number" class="form-control text-center" id="card" placeholder="0.00">
            </td>
            <td class="text-center" id="total-card">0.00</td>
        </tr>
        
        <!-- Empty Rows -->
        <tr>
            <td></td>
            <td></td>
            <td class="text-center"></td>
            <td></td>
            <td class="text-center"></td>
        </tr>
        
        <tr>
            <td></td>
            <td></td>
            <td class="text-center"></td>
            <td></td>
            <td class="text-center"></td>
        </tr>
        
        <!-- Summary Rows -->
        <tr class="font-weight-bold">
            <td class="text-right" id="prev-total">0.00</td>
            <td class="text-center"></td>
            <td class="text-right">Total</td>
            <td class="text-center" id="today-total">0.00</td>
            <td class="text-center" id="running-total">0.00</td>
        </tr>
        
        <tr class="font-weight-bold">
            <td class="text-right" id="prev-balance">0.00</td>
            <td class="text-center"></td>
            <td class="text-right">Balance in Hand</td>
            <td class="text-center" id="balance">0.00</td>
            <td class="text-center" id="total-balance">0.00</td>
        </tr>
        
        <tr class="font-weight-bold bg-light">
            <td class="text-right" id="prev-grand-total">0.00</td>
            <td class="text-center"></td>
            <td class="text-right">Grand Total</td>
            <td class="text-center" id="today-grand-total">0.00</td>
            <td class="text-center" id="grand-total">0.00</td>
        </tr>
    </tbody>
</table>

        </div>
<div   class="mt-3 mb-4">
    <!-- Content will be dynamically inserted here -->
   
       <span id="textdetail"></span>
   
</div>
        <!-- Signature Area -->
        <div class="signature-area" style="margin-top: 20px;">
            <div class="row">
                <div class="col-md-6">
                    <div class="signature-line" style="border-top: 1px solid #000; margin-top: 50px; padding-top: 5px;">
                        Casher Signature
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="signature-line" style="border-top: 1px solid #000; margin-top: 50px; padding-top: 5px;">
                        Store Keeper Signature
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
</section>
<!-- /.content -->