@extends('layouts.app')
@section('title', __('mpcs::lang.F22StockTaking_form'))

@section('content')
    <!-- Main content -->
<style>
    .pump-section table {
        display: block;
        overflow-x: auto;
    }

    .pumps-container {
        display: flex;
        gap: 10px;
        /* Horizontal spacing between pump sections */
        align-items: stretch;
        /* Ensures child elements take full height */
    }

    .no-wrap {
        white-space: nowrap;
    }

    .column-50 {
        width: 25%;
    }

    .pump-section {
        flex: 1;
        /* Each pump section takes equal width */
        border-right: 3px solid skyblue;
        /* Vertical divider */
        padding-right: 10px;
        /* Space between content and divider */
        display: flex;
        flex-direction: column;
        height: 100%;
        /* Ensure full height */
    }

    .form-input {
        width: 50%;
        padding: 1px;
        box-sizing: border-box;
    }
    
    
    .padding-20 {
        padding: 20px;
    }
</style>
    
    <div class="card padding-20">
        <div class="card-header">
            <a href="{{ url('/mpcs/F22_stock_taking') }}#list_f22_stock_taking_tab" class="back-btn">
              <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="text-center">
                        <h5 style="font-weight: bold;">
                            {{ request()->session()->get('business.name') }}<br>
                            <span class="f22_location_name">{{ $header->location_name }}</span>
                        </h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center pull-left">
                        <h5 style="font-weight: bold;" class="text-red">
                            @lang('mpcs::lang.f22_form_no') : {{ $header->form_no }}
                        </h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center pull-left">
                        <h5 style="font-weight: bold;" class="text-red">
                            @lang('mpcs::lang.date_and_time') : {{ $header->created_at->format('Y-m-d H:i:s') }}
                        </h5>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="form_22_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang('mpcs::lang.index_no')</th>
                                    <th>@lang('mpcs::lang.code')</th>
                                    <th>@lang('mpcs::lang.product')</th>
                                    <th>@lang('mpcs::lang.current_stock')</th>
                                    <th>@lang('mpcs::lang.stock_count')</th>
                                    <th>@lang('mpcs::lang.unit_purchase_price')</th>
                                    <th>@lang('mpcs::lang.total_purchase_price')</th>
                                    <th>@lang('mpcs::lang.unit_sale_price')</th>
                                    <th>@lang('mpcs::lang.total_sale_price')</th>
                                    <th>@lang('mpcs::lang.qty_difference')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $grand_page_total_purchase = 0;
                                    $grand_page_total_sale = 0;
                                @endphp
        
                                @foreach ($details as $index => $item)
                                    @php
                                        $stock_count = $item->stock_count ?? 0;
                                        $unit_purchase_price = $item->unit_purchase_price ?? 0;
                                        $unit_sale_price = $item->unit_sale_price ?? 0;
        
                                        $purchase_price_total = $unit_purchase_price * $stock_count;
                                        $sales_price_total = $unit_sale_price * $stock_count;
        
                                        $grand_page_total_purchase += $purchase_price_total;
                                        $grand_page_total_sale += $sales_price_total;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->product_code ?? '' }}</td>
                                        <td>{{ $item->product ?? '' }}</td>
                                        <td>{{ !empty($item->current_stock) ? number_format($item->current_stock) : '' }}</td>
                                        <td>{{ !empty($item->stock_count) ? number_format($item->stock_count) : '' }}</td>
                                        <td>{{ number_format($unit_purchase_price, 2) }}</td>
                                        <td>{{ number_format($purchase_price_total, 2) }}</td>
                                        <td>{{ number_format($unit_sale_price, 2) }}</td>
                                        <td>{{ number_format($sales_price_total, 2) }}</td>
                                        <td>{{ !empty($item->difference_qty) ? number_format($item->difference_qty) : '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray">
                                    <td class="text-red text-bold" colspan="6">@lang('mpcs::lang.total_this_page')</td>
                                    <td class="text-red text-bold" id="footer_total_purchase_price"></td>
                                    <td>&nbsp;</td>
                                    <td class="text-red text-bold" colspan="2" id="footer_total_sale_price"></td>
                                </tr>
                                <tr class="bg-gray">
                                    <td class="text-red text-bold" colspan="6">@lang('mpcs::lang.total_previous_page')
                                    </td>
                                    <td class="text-red text-bold" id="pre_total_purchase_price"></td>
                                    <td>&nbsp;</td>
                                    <td class="text-red text-bold" colspan="2" id="pre_total_sale_price"></td>
                                </tr>
                                <tr class="bg-gray">
                                    <td class="text-red text-bold" colspan="6">@lang('mpcs::lang.grand_total')</td>
                                    <td class="text-red text-bold" id="grand_total_purchase_price"></td>
                                    <td>&nbsp;</td>
                                    <td class="text-red text-bold" colspan="2" id="grand_total_sale_price"></td>
                                </tr>
                                <tr>
                                    <td colspan="10"> @lang('mpcs::lang.confirm_f22')</td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                            @lang('mpcs::lang.checked_by'): ____________</h5>
                                    </td>
                                    <td colspan="4">
                                        <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                            @lang('mpcs::lang.received_by'): ____________</h5> <br>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                            @lang('mpcs::lang.signature_of_manager'): ____________</h5>
                                    </td>
                                    <td colspan="4">
                                        <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                            @lang('mpcs::lang.handed_over_by'): ____________</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="10">
                                        <h5 style="font-weight: bold; margin-top: 10px; ">@lang('mpcs::lang.user'):
                                            {{ auth()->user()->username }}</h5>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
@endsection

@section('javascript')
<script>
    let form_22_table;
    var ppage_totals = [];
    var spage_totals = [];
    var pre_gppage_totals = [];
    var pre_gspage_totals = [];
    $(document).ready(function () {
        form_22_table = $('#form_22_table').DataTable({
            pagingType: 'simple', // ðŸ‘ˆ Only "Previous" and "Next"           
            lengthChange: false,
            pageLength: {{!empty($settings->F22_no_of_product_per_page) ? $settings->F22_no_of_product_per_page : 25}},
            columnDefs: [ 
                {
                    "targets": 0,
                    "orderable": false,
                },
            ],
            fnDrawCallback: function(oSettings) {
                var total_purchase_price = sum_table_col($('#form_22_table'), 'total_purchase_price');
                $('#footer_total_purchase_price').text(total_purchase_price);
                var total_sale_price = sum_table_col($('#form_22_table'), 'total_sale_price');
                $('#footer_total_sale_price').text(total_sale_price);
                // __currency_convert_recursively($('#form_22_table'));
            },
            drawCallback: function(oSettings) {
                __currency_convert_recursively($('#form_22_table'));
            }
        });
        
        
    })
</script>
@endsection
