<style>
    .btn-orange {
    color: #fff;
    background-color: #FFA500; /* Standard orange color */
    border-color: #e59400;
}

.btn-orange:hover {
    background-color: #e59400;
    border-color: #cc8400;
    color: #fff;
}
    </style>
<!-- Main content -->
<section class="content" style="padding:10px">
    <div class="row">
        <div class="col-md-3 text-red">
            <b>@lang('mpcs::lang.date_and_time'): <span class="9c_from_date">{{$date}}</span> </b>
        </div>
        <div class="col-md-3 text-red">
            <b>@lang('mpcs::lang.ref_previous_form_number'): <span class="9c_from_date">{{$form_number}}</span> </b>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <h5 style="font-weight: bold;">@lang('mpcs::lang.user_added'): {{$name}} <br>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="box-tools pull-left" style="margin: 14px 20px 14px 0;">  
    
   <button type="button" id="text_details_button" class="btn btn-primary" style="color: red;">
    <i class="fa fa-file-text"></i> Text Section
</button>
</div>
    <div class="box-tools pull-right" style="margin: 14px 20px 14px 0;">
        <button type="button" id="add_form_9_a_settings_button" class="btn btn-primary btn-modal" data-href="{{ action('\Modules\MPCS\Http\Controllers\Form9ASettingsController@create') }}" data-container=".form_9_a_settings_modal">
            <i class="fa fa-plus"></i> @lang('mpcs::lang.add_form_9_a_settings')
        </button>
    </div>

</div>
<div class="row">

</div>
<!-- Hidden input field to store ref_pre_form_number -->
<input type="hidden" id="ref_pre_form_number" value="{{ $form_number ?? '' }}">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="col-md-12">
                <div class="box-body" style="margin-top: 20px;">
                                             
                            <div id="msg"></div>
                            <table id="form_9a_settings_table" class="table table-striped table-bordered" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('mpcs::lang.action')</th>
                                        <th>@lang('mpcs::lang.form_starting_number')</th>
                                        <th>@lang('mpcs::lang.total_sale_up_to_previous_day')</th>
                                        <th>@lang('mpcs::lang.previous_day_cash_sale')</th>
                                        <th>@lang('mpcs::lang.previous_day_card_sale')</th>
                                        <th>@lang('mpcs::lang.previous_day_credit_sale')</th>
                                        <th>@lang('mpcs::lang.previous_day_cash')</th>
                                        <th>@lang('mpcs::lang.previous_day_cheques_cards')</th>
                                        <th>@lang('mpcs::lang.previous_day_total')</th>
                                        <th>@lang('mpcs::lang.previous_day_balance_in_hand')</th>
                                        <th>@lang('mpcs::lang.previous_day_grand_total')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        
                </div>

            </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="text_details_table">
            <thead>
                <tr>
                    <th width="10%">Action</th>
                    <th>Text Detail</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded here via AJAX -->
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade text_details_modal" tabindex="-1" role="dialog" aria-labelledby="textDetailsModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Text Details</h4>
            </div>
            <div class="modal-body">
                <form id="text_details_form">
                    @csrf
                    <div class="form-group">
                        <label for="text_content">Text Content:</label>
                        <textarea class="form-control" id="text_content" name="text_content" rows="5" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save_text_details">Save</button>
            </div>
        </div>
    </div>
</div>
</section>
<!-- /.content -->