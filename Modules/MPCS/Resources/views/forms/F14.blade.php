

@extends('layouts.app')
@section('title', __('mpcs::lang.14_form'))

@section('content')

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<!-- Main content -->
<section class="content">
    <div class="page-title-area" id="app">
        <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('mpcs::lang.F14_form')])
        <div class="col-md-3" id="location_filter">
            <div class="form-group">
                {!! Form::label('f14b_location_id', __('purchase.business_location') . ':') !!}
                <br />
                <select v-model="filter.business_location_id" class="form-select filter-select" aria-label="Default select example" @change="setBusinessLocation">
                  <option selected>ALL</option>
                  <template v-for="(business_location,id) in business_locations">
                    <option :value="id">@{{ business_location }}</option>
                  </template>
                </select>
                
                
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="type">Date:</label>
                <input v-model="filter.date_range" class="form-control" ref="daterange" name="f14b_date" type="text">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <div class="form-group">
                    <label for="type">F14B Form No:</label>
                    <input v-model="filter.form_no"  class="form-control" readonly="" name="F14b_from_no" type="text" value="1" @input="getData">
                </div>
            </div>
        </div>
        @endcomponent
		
		<div class="row">
			<div class="col-md-12">
				<div class="box-tools">
					<!-- Standard Print button -->
					<button class="btn btn-primary print_report pull-right" @click="printBills">
						<i class="fa fa-print"></i> @lang('messages.print')</button>
				</div>
			</div>
		</div>
		
        @component('components.widget', ['class' => 'box-primary', 'title' => __('mpcs::lang.F14_form')])
            <div id="form14B_content">
                <div class="container">
                    <nav aria-label="Page navigation">
                      <ul class="pagination">
                        <li>
                          <a href="#" aria-label="Previous" @click="prevPage">
                            <span aria-hidden="true">&laquo;</span>
                          </a>
                        </li>
                        <template v-for="(pg,pgind) in pages">
                            <li :class="(page == (pgind))?'active':''"><a href="#" @click="setPage(pgind)">@{{ pgind + 1 }}</a></li>
                        </template>
                        <li>
                          <a href="#" aria-label="Next"  @click="nextPage">
                            <span aria-hidden="true">&raquo;</span>
                          </a>
                        </li>
                      </ul>
                    </nav>
                    
                    <div class="row" id="printarea">
                        
                        <template v-for="(sale,ind) in sales">
                            
                            <div class="col-md-6 col-sm-6  col-xl-4 border">
                                <div class="col-md-12 " style="border: 1px solid #333; padding : 10px !important; margin-bottom: 10px !important">
                                    <div class="row">
                                        <div class="col-md-11 col-sm-9 text-center">
                                        <b>@{{ sale.comapany }}</b><br>
                                        <b>@lang('mpcs::lang.filling_station')</b><br>
                                        <b>@lang('mpcs::lang.tel') :</b> @{{ sale.tel }}
                                        </div>
                                        <div class="col-md-1 col-sm-3 text-right">F@{{ setting.F14_form_sn + ind + total_before_startdate }}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6"><b>Date:</b> @{{ sale.date }}</div>
                                        <div class="col-md-6 col-sm-6"><b>Bill No:</b>@{{ setting.bill_no_form_14 + ind + total_before_startdate }}</div>
                                        <div class="col-md-6 col-sm-6"><b>Customer:</b> @{{ sale.customer }}</div>
                                        <div class="col-md-6 col-sm-6"><b>Order No:</b>@{{ sale.order_no }}</div>
                                        <div class="col-md-6 col-sm-6"><b>Vehicle No:</b> @{{ sale.customer_reference }}</div>
                                        <div class="col-md-6 col-sm-6"><b>Our Reference:</b> @{{ sale.sattlement_no }}</div>
                                    </div>
                                    <table class="table table-bordered table-striped credit_sale_table" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>@lang('mpcs::lang.voucher_no')</th>
                                                <th>@lang('mpcs::lang.balance_qty')</th>
                                                <th>@lang('mpcs::lang.description')</th>
                                                <th>@lang('mpcs::lang.unit_price')</th>
                                                <th>@lang('mpcs::lang.amount')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <tr>
                                                <td>@{{ sale.order_no }}</td>
                                                <td>@{{ parseFloat(sale.balance_qty).toFixed((sale.is_fuel)?fuel_qty_decimals:business.quantity_precision) }}</td>
                                                <td>@{{ sale.description }}</td>
                                                <td>@{{ parseFloat(sale.sell_price_inc_tax).toFixed(business.currency_precision) }}</td>
                                                <td>@{{ parseFloat(sale.final_total).toFixed(sale.currency_precision) }} </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-center">@lang('mpcs::lang.total_amount')</td>
                                                <td>@{{ parseFloat(sale.final_total).toFixed(sale.currency_precision) }} </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </template>
                        
                        
                    </div>
                </div>
            </div>
        @endcomponent
        
        <!-- Transactions Table -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'All Transactions'])
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-striped" id="transactions_table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Invoice No</th>
                            <th>Ref No</th>
                            <th>Transaction Date</th>
                            <th>Final Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>{{ $transaction->invoice_no }}</td>
                            <td>{{ $transaction->ref_no }}</td>
                            <td>{{ $transaction->transaction_date }}</td>
                            <td>{{ number_format($transaction->final_total, 2) }}</td>
                            <td>{{ $transaction->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endcomponent
        
    </section>
    </div>
    
</section>

<script>
    new Vue({
        el: '#app',
        data() {
            return {
                title: 'F14B Former',
                business_locations: {!! json_encode($business_locations) !!},
                setting: {!! json_encode($setting) !!},
                business: {!! json_encode($business) !!},
                filter: {business_location_id:"{{ $default_business_location }}",date_range:'',form_no:{{ $setting['F14_form_sn'] ?? "null" }}},
                fuel_qty_decimals: {{ $fuel_qty_decimals }},
                credit_sales: [],
                sales:[],
                page:1,
                pages:[],
                total_before_startdate:0,
            }
        },
        mounted() {
            $(document).ready(() => {
                

                $(this.$refs.daterange).daterangepicker({
                  showDropdowns: true,
                  autoUpdateInput: false,
                  startDate: moment(), 
                  endDate: moment(), 
                  locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel: 'Clear',
                    applyLabel: 'Apply',
                    fromLabel: 'From',
                    toLabel: 'To'
                  },
                  ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                  }
                }, (start, end, label) => {
                  // Handle the date range selection
                  if (label === 'Today' || label === 'Yesterday') {
                    // For single day selections, use the same date for start and end
                    this.filter.date_range = `${start.format('YYYY-MM-DD')}`;
                  } else {
                    // For custom ranges, format as start_date to end_date
                    this.filter.date_range = `${start.format('YYYY-MM-DD')} - ${end.format('YYYY-MM-DD')}`;
                  }
                  
                  // Update the input field
                  $(this.$refs.daterange).val(this.filter.date_range);
                  this.getData();
                });

                // Handle manual input clearing
                $(this.$refs.daterange).on('cancel.daterangepicker', (ev, picker) => {
                  $(this.$refs.daterange).val('');
                  this.filter.date_range = '';
                  this.getData();
                });
                
                // Set initial date range
                this.filter.date_range = '{{ $startdate }}'; // initial date (e.g., today)
                $(this.$refs.daterange).val(this.filter.date_range);
                this.getData();
            });
        },
        methods: {
			printBills(){
				window.print();
			},
            setBusinessLocation( a,b ){
                console.log(this.filter.business_location_id);
                
                this.getData();
            },
            prevPage(){
              if(this.page > 0){
                  this.setPage(this.page - 1);
              }  
            },
            nextPage(){
              if(this.page < (this.pages.length - 1)){
                  this.setPage(this.page + 1);
              }  
            },
            setPage(page){
                this.page = page;
                this.sales = this.credit_sales[this.pages[this.page]];
            },
            getData(){
                
                console.log('get data by axios get');
                console.log(this.filter.business_location_id);
                console.log(this.filter.date_range);
                console.log(this.filter.form_no);
                
                axios.get('/mpcs/mpcs/get-form-14',{params:this.filter}).then(res => {
                    if(res.status == 200){
                        
                        this.credit_sales = res.data.data;
                        this.total_before_startdate = res.data.total_before_startdate;
                        this.pages = Object.keys(res.data.data);
                        
                        // Update setting with current values from server
                        if (res.data.setting) {
                            this.setting = res.data.setting;
                        }
                        

                        
                        this.setPage(0);
                        
                    }
                }).catch(err => {
                    console.log(err);
                });
                
            }
        }
    });
    
</script>
<style>
    @media print {
      @page {
        size: A4 portrait;
      }

      body * {
        visibility: hidden; /* Hide everything */
      }
      #printarea, #printarea * {
        visibility: visible; /* Only make the content div visible */
      }
      #printarea {
        position: absolute;
        top: 0;
        left: 0;
      }
    }
    .filter-select{
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 4px;
        height: 35px;
        width:100%;
    }
    
    /* Date picker styling */
    .daterangepicker {
        z-index: 9999;
    }
    
    .daterangepicker .ranges li {
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 4px;
        margin: 2px 0;
    }
    
    .daterangepicker .ranges li:hover {
        background-color: #f8f9fa;
    }
    
    .daterangepicker .ranges li.active {
        background-color: #007bff;
        color: white;
    }
    
    /* Transactions table scrolling */
    #transactions_table thead th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
        border-bottom: 2px solid #dee2e6;
    }
    
    #transactions_table tbody tr:hover {
        background-color: #f5f5f5;
    }
</style>
@endsection