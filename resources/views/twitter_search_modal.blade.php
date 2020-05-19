{{-- 検索画面モーダル --}}

<div class='remodal rounded ui segment' data-remodal-id='twitter_search_modal' data-remodal-options='hashTracking:false,closeOnOutsideClick: false,closeOnEscape:false' id="twitter_search_modal">
    <div class="ui inverted dimmer" style="pointer-events: none;">
        <div class="ui text loader form-disabled">@lang('title.loading')</div>
    </div>
{!! Form::open(['action' => 'TwitterController@search', 'method' => 'get', 'id' => 'search_form']) !!}
@csrf
    <div class='form-horizontal'>
        <div class='overflow-auto border rounded border-success' style='height:300px;'>
            {{-- キーワード --}}
            <div class="d-block modal_d-block border rounded border-success">
                <h5 class="d-block">
                    {!! Form::label('keyword_txt', Lang::get('title.keyword'), ['class' => 'control-label']) !!}
                </h5>
                <div class="d-block">
                        {!! Form::text('', '', ['class' => 'form-control form-disabled', 'id' => 'keyword_txt']) !!}
                        {!! Form::hidden('keyword', '', ['id' => 'keyword_txt_hidden']) !!}
                </div>
            </div>
            <div class="d-block modal_d-block border rounded border-success row">
                {{-- いつから --}}
                <h5 class="d-block">
                    {!! Form::label('date_select', Lang::get('title.date_time_from'), ['class' => 'control-label']) !!}
                </h5>
                <div class="d-block">
                    <div class="row">
                        {!! Form::date('', '', ['class' => 'form-control col-6 offset-1 input_date_time form-disabled', 'id' => 'date_select']) !!}
                        {!! Form::time('', '00:00', ['class' => 'form-control col-4 input_date_time form-disabled', 'id' => 'time_select', 'step'=>'300']) !!}
                    </div>
                </div>
                {{-- いつまで --}}
                <h5 class="d-block modal_d-block">
                    {!! Form::label('time_unit_minutes_radio', Lang::get('title.date_time_to'), ['class' => 'control-label']) !!}
                </h5>
                <div class="d-block">
                    <div class = 'form-group row'>
                        <div class='col-4 offset-1 text-left'>
                            <div class='d-block form-check form-check-inline'>
                                {!! Form::radio('time_unit', 'minutes', ['class' => 'form-check-input input_date_time', 'id' => 'time_unit_minutes_radio', 'checked' => 'checked']) !!}
                                {!! Form::label('time_unit_minutes_radio', Lang::get('title.minutes'), ['class' => 'form-check-label']) !!}
                            </div>
                            <div class='d-block form-check form-check-inline'>
                                {!! Form::radio('time_unit', 'hours', ['class' => 'form-check-input input_date_time', 'id' => 'time_unit_hours_radio']) !!}
                                {!! Form::label('time_unit_hours_radio', Lang::get('title.hours'), ['class' => 'form-check-label']) !!}
                            </div>
                            <div class='d-block form-check form-check-inline'>
                                {!! Form::radio('time_unit', 'days', ['class' => 'form-check-input input_date_time', 'id' => 'time_unit_days_radio']) !!}
                                {!! Form::label('time_unit_days_radio', Lang::get('title.days'), ['class' => 'form-check-label']) !!}
                            </div>
                        </div>
                        <div class = 'form-group col-6 row form-group col-6 row d-flex align-items-end'>
                            {!! Form::number('', '5', ['class' => 'form-control col-6 input_date_time form-disabled', 'id' => 'time_value_select', 'min' => '5', 'max' => '50', 'step' => '5']) !!}
                            {!! Form::label('time_value_select', Lang::get('title.minutes'), ['class' => 'control-label col-6 text-left', 'id' => 'time_unit_lb']) !!}
                        </div>
                    </div>
                </div>
                <div class="d-block">
                    {!! Form::label('', '', ['class' => 'form-check-label', 'id' => 'from_to_lb']) !!}
                </div>
                {{-- 日付範囲のhidden値 --}}
                {!! Form::hidden('date_time_from', '', ['id' => 'date_from_hidden']) !!}
                {!! Form::hidden('date_time_to', '', ['id' => 'date_to_hidden']) !!}
            </div>

            {{-- メディア --}}
            <div class="d-block modal_d-block border rounded border-success">
                <h5 class="d-block">
                    {!! Form::label('select_media', Lang::get('title.media'), ['class' => 'control-label form-disabled']) !!}
                </h5>
                <div class="d-block">
                    {{Form::select('', [
                        'all_tweet' => Lang::get('title.all_tweet'),
                        'only_media' => Lang::get('title.only_media'),
                        'only_images' => Lang::get('title.only_images'),
                        'only_video' => Lang::get('title.only_video'),
                        'without_media' => Lang::get('title.without_media')],'all_tweet',['class'=>'form-control form-disabled', 'id'=>'select_media'])}}
                </div>
                {!! Form::hidden('select_media', '', ['id' => 'select_media_hidden']) !!}
            </div>

            {{-- 追加検索条件 --}}
            <div class="d-block modal_d-block border rounded border-success">
                <h5 class="d-block">
                    {!! Form::label('select_media', Lang::get('title.serach_others'), ['class' => 'control-label']) !!}
                </h5>
                <div class="d-block">
                    <div class="d-block form-check">
                        {!! Form::checkbox('', 'without', '', ['class'=>' form-disabled', 'id' => 'without_rt_check']) !!}
                        {!! Form::label('without_rt_check', Lang::get('title.without_rt'), ['class' => 'form-check-label']) !!}
                        {!! Form::hidden('without_rt_check', '', ['id' => 'without_rt_check_hidden']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="d-block modal_d-block">
            {!! Form::submit(Lang::get('title.search'), ['class' => 'btn btn-success form-disabled']) !!}
            {!! Form::input('button','clear',Lang::get('title.clear'),['class' => 'btn btn-clear form-disabled', 'id' => 'search_clear_btn']) !!}
            {!! Form::input('button','close',Lang::get('title.close'),['class' => 'btn btn-clear form-disabled', 'id' => 'search_close_btn']) !!}
        </div>
    </div>
    {{ Form::close() }}
</div>


