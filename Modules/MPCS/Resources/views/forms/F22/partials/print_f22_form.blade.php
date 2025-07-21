<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>F22-{{$details['F22_from_no']}} {{request()->session()->get('business.name')}} {{$details['f22_location_name']}}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
            page-break-inside: auto;
        }
        
        table tbody td {
            border: 1px solid black; 
        }
        
        table thead th {
            border: 1px solid black; 
        }
        
        th {
            font-size: 13px;
        }
        
        td {
            font-size: 13px;
        }
        
        .page {
            page-break-after: always;
        }
        
        .page:last-child {
            page-break-after: avoid;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .footer {
            margin-top: 10px;
        }
        
        .text-bold {
            font-weight: bold;
        }
        
        .text-red {
            color: red;
        }
        
        .bg-gray {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    @php
        $index = 1;
        $chunk_number = !empty($settings->F22_no_of_product_per_page) ? $settings->F22_no_of_product_per_page : 25;
        $chuncks = array_chunk($data ,$chunk_number );
        $pre_page_total_purchase = 0;
        $pre_page_total_sale = 0;
        $grand_page_total_purchase = 0;
        $grand_page_total_sale = 0;
    @endphp
    
    @foreach ($chuncks as $key => $detail)
    <div class="page">
        <div class="header">
            <div>
                <h5 style="font-weight: bold; margin-bottom: 5px;">{{request()->session()->get('business.name')}}</h5>
                <h5 style="font-weight: bold; margin-top: 0;"><span class="f22_location_name">{{$details['f22_location_name']}}</span></h5>
            </div>
            <div>
                <h5 style="font-weight: bold; color: red; margin-top: 5px;">
                    @lang('mpcs::lang.f22_form_no') : {{$details['F22_from_no']}}</h5>
            </div>
            <div>
                <label for="date_range_filter" class="control-label">Date: {{ $date }}</label>                
            </div>
        </div>
        
        @php
            $this_page_total_purchase = 0.00;
            $this_page_total_sale = 0.00;
        @endphp
        
        <table class="table table-bordered table-striped" id="form_22_table">
            <thead>
                <tr>
                    <th>@lang('mpcs::lang.index_no')</th>
                    <th>@lang('mpcs::lang.code')</th>
                    <th>@lang('mpcs::lang.book_no')</th>
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
                @foreach ($detail as $item)
                <tr>
                    <td>{{$index}}</td>
                    <td>{{!empty($item['sku']) ? $item['sku'] : ''}}</td>
                    <td>{{!empty($item['book_no']) ? $item['book_no'] : ''}}</td>
                    <td>{{!empty($item['product']) ? $item['product'] : ''}}</td>
                    <td>{{isset($item['current_stock']) ? number_format((float)$item['current_stock'], 2) : ''}}</td>
                    <td>{{!empty($item['stock_count']) ? @number_format((float)$item['stock_count'],2) : ''}}</td>
                    <td>{{!empty($item['unit_purchase_price']) ? @number_format((float)$item['unit_purchase_price'],2) : ''}}</td>
                    <td>{{!empty($item['total_purhcase_value']) ? @number_format((float)$item['total_purhcase_value'],2) : ''}}</td>
                    <td>{{!empty($item['unit_sale_price']) ? @number_format((float)$item['unit_sale_price'],2) : ''}}</td>
                    <td>{{!empty($item['total_sale_value']) ? @number_format((float)$item['total_sale_value'],2) : ''}}</td>
                    <td>{{!empty($item['difference_qty']) ? @number_format((float)$item['difference_qty'],2) : ''}}</td>
                </tr>

                @php
                    $index++;
                    $this_page_total_purchase += !empty($item['total_purhcase_value']) ? $item['total_purhcase_value'] : 0;
                    $this_page_total_sale += !empty($item['total_sale_value']) ? $item['total_sale_value'] : 0;
                    $grand_page_total_purchase += !empty($item['total_purhcase_value']) ? $item['total_purhcase_value'] : 0;
                    $grand_page_total_sale += !empty($item['total_sale_value']) ? $item['total_sale_value'] : 0;
                    if($key == 0){
                        $pre_page_total_purchase = 0;
                        $pre_page_total_sale = 0;
                    }
                @endphp
                @endforeach
            </tbody>
            <tfoot class="bg-gray">
                <tr>
                    <td class="text-red text-bold" colspan="7">@lang('mpcs::lang.total_this_page')</td>
                    <td class="text-red text-bold" id="footer_total_purchase_price">
                        {{@number_format($this_page_total_purchase)}}</td>
                    <td>&nbsp;</td>
                    <td class="text-red text-bold" colspan="2" id="footer_total_sale_price">
                        {{@number_format($this_page_total_sale)}}</td>
                </tr>
                <tr>
                    <td class="text-red text-bold" colspan="7">@lang('mpcs::lang.total_previous_page')
                    </td>
                    <td class="text-red text-bold" id="pre_total_purchase_price">{{ @number_format($pre_page_total_purchase)}}
                    </td>
                    <td>&nbsp;</td>
                    <td class="text-red text-bold" colspan="2" id="pre_total_sale_price">
                        {{ @number_format($pre_page_total_sale) }}</td>
                </tr>
                <tr>
                    <td class="text-red text-bold" colspan="7">@lang('mpcs::lang.grand_total')</td>
                    <td class="text-red text-bold" id="grand_total_purchase_price">
                        {{@number_format($grand_page_total_purchase)}}</td>
                    <td>&nbsp;</td>
                    <td class="text-red text-bold" colspan="2" id="grand_total_sale_price">
                        {{@number_format($grand_page_total_sale)}}</td>
                </tr>
                <tr>
                    <td colspan="11"> @lang('mpcs::lang.confirm_f22')</td>
                </tr>
                <tr>
                    <td colspan="7" class="text-left" style="border: 0px !important">
                        @lang('mpcs::lang.checked_by'): ____________
                    </td>
                    <td colspan="4" style="border: 0px !important">
                        @lang('mpcs::lang.received_by'): ____________
                    </td>
                </tr>
                <tr>
                    <td colspan="7" class="text-left" style="border: 0px !important">
                        @lang('mpcs::lang.signature_of_manager'): ____________
                    </td>
                    <td colspan="4" style="border: 0px !important">
                        @lang('mpcs::lang.handed_over_by'): ____________
                    </td>
                </tr>
                <tr>
                    <td colspan="7" class="text-left" style="border: 0px !important">
                        @lang('mpcs::lang.user'): {{auth()->user()->username }}
                    </td>
                    <td colspan="4" style="border: 0px !important">
                        &nbsp;
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @php
        $pre_page_total_purchase = $grand_page_total_purchase;
        $pre_page_total_sale = $grand_page_total_sale;
    @endphp
    @endforeach
</body>
</html>