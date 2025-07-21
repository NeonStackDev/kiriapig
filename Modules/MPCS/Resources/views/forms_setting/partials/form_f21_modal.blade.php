<div class="modal-dialog" role="document" style="width: 45%;">
    <div class="modal-content">
<style>
    .9c_table tbody> tr>td{
        padding: 15px;
    }
</style>
        {!! Form::open(['url' => action('\Modules\MPCS\Http\Controllers\FormsSettingController@postFormF21Setting'),
        'method' => 'post', 'id' => 'f21_settings_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'mpcs::lang.f21_settings' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('F21_form_tdate', __( 'lang_v1.transaction_date' ) . ':*') !!}
                        {!! Form::text('F21_form_tdate', !empty($setting) ? $setting->F21_form_tdate :null, ['class' => 'form-control', 'id' => 'F21_form_tdate',
                        'disabled', 'required', 'placeholder' => __( 'lang_v1.transaction_date' ) ]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('F21_form_sn', __( 'mpcs::lang.form_starting_number' ) . ':*') !!}
                        {!! Form::text('F21_form_sn', !empty($setting) ? $setting->F21_form_sn :null, ['class' => 'form-control', 'id' => 'F159ABC_setting_sn',
                        'disabled', 'placeholder' => __( 'mpcs::lang.form_starting_number' ) ]); !!}
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:20px;">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('F21_no_of_product_per_page', __( 'mpcs::lang.F21_no_of_product_per_page' ) . ':*') !!}
                        {!! Form::text('F21_no_of_product_per_page', $setting->F21_no_of_product_per_page, ['class' => 'form-control', 'id' => 'F21_no_of_product_per_page',
                        'required', 'placeholder' => __( 'mpcs::lang.enter' ) ]); !!}
                    </div>
                </div>
            </div>

        </div>

        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#F16A_setting_tdate').datepicker().datepicker("setDate", new Date());
</script>