{{--トップページ--}}

@extends('layouts.app')

{{--検索モーダル--}}
@include('twitter_search_modal')

@section('content')
    <div class="container"  id="container_back">
        @if (!empty($result))
            <div class="container">
                {!! Form::hidden('hidden', $result['chart_data'], ['id' => 'chart_data_hidden']) !!}
                <div class="d-none" id="chart_div">
                    <button class="btn btn-primary" id="show_chart_btn"  data-toggle="collapse" data-target="#c3_chart_div" aria-expand="false" aria-controls="c3_chart_div">@lang('title.show_chart')</button>
                    <div class="collapse border rounded" id="c3_chart_div">
                        <div class="mx-auto border rounded border-success" id="c3_chart" style="width:1000px;"></div>
                    </div>
                </div>
                <div>
                    @foreach ($result['tweets'] as $index => $tweet)
                        @if(isset($tweet['user']))
                            @include('tweet', ['tweet' => $tweet])
                            {{-- デバッグでツイートのindexを取得する際に表示する --}}
                            {{-- {{ $index }} --}}
                        @endif
                    @endforeach
                </div>
            </div>
        @else
        @endif
    </div>
    {{-- フッター --}}
    <footer class="footer">
      <div class="container">
        <p class="text-muted">@lang('title.copyright')</p>
      </div>
    </footer>
@endsection
