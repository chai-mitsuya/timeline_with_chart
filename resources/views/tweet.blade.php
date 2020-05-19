{{-- ツイート表示用ビュー --}}
<div class="card mb-2">
    <div class="card-body">
        {!! Form::hidden('tweet_id', $tweet['tweet']['id'], ['class'=> 'tweet_id']) !!}
        @if ($tweet['type'] == 'retweet_original') <div class="media tweet_retweet_back">
        @elseif ($tweet['type'] == 'reply_original_tweet') <div class="media tweet_reply_back">
        @else  <div class="media tweet_normal_back">  @endif
            <div>
                {{-- アイコン --}}
                <div class="d-block">
                    <img src= {{ $tweet['user']['profile_image'] }} class="rounded-circle mr-4 border border-dark">
                </div>
                <div class="d-block text-secondary">
                    @if ($tweet['type'] == 'retweet' || $tweet['type'] == 'quoted_retweet') @lang('title.tweet_type_retweet')
                    @elseif ($tweet['type'] == 'reply') @lang('title.tweet_type_reply')
                    @else @lang('title.tweet_type_normal')  @endif
                </div>
            </div>
            <div class="media-body">
                <div class="d-block">
                    <div class="row">
                        <div class="col-6">
                            <h5 class="d-inline mr-1"><strong>{{ $tweet['user']['name'] }}</strong></h5>
                            <h5 class="d-inline text-secondary mr-10"><strong>{{ $tweet['user']['id'] }}</strong></h5>
                        </div>
                        <div class="col-6 text-right">
                            <a href={{ $tweet['tweet']['original_url'] }} target='_blank' class='btn-twitter-link'>@lang('title.twitter')</a>
                        </div>
                    </div>

                </div>
                <div class="d-block">
                    <h6 class="text-secondary">{{ $tweet['tweet']['post_time'] }}</h6>
                </div>
                @if ($tweet['type'] != 'retweet')
                        @if ($tweet['type'] == 'retweet_original') <div class="container tweet_retweet_front shadow-sm">
                        @elseif ($tweet['type'] == 'reply_original_tweet') <div class="container tweet_reply_front shadow-sm">
                        @else  <div class="container tweet_normal_front shadow-sm">  @endif
                            <div class="row">
                                @if (!empty($tweet['tweet']['media']))
                                    <div class="col-md-8">{{ $tweet['tweet']['text'] }}</div>
                                    <div class="col-md-4 row  bg-dark text-white" id="{{ 'tweet_media_div_' . $tweet['tweet']['id'] }}">
                                        @foreach ($tweet['tweet']['media'] as $media)
                                            <div class="d-inline col-3 tweet_media_div">
                                                {{-- 画像 --}}
                                                @if ($media['type'] == "photo")
                                                    <a class='tweet_media' href="{{ '#'.$media['index'] }}" title=@lang('title.image')>
                                                        <img src="{{ $media['url'] }}" class="tweet_media" style="width:100%;" alt="">
                                                    </a>
                                                    <div style='display:none'>
                                                        <div id="{{ $media['index'] }}">
                                                            <img src="{{ $media['url'] }}" alt="" />
                                                        </div>
                                                    </div>
                                                @endif
                                                {{-- 動画 --}}
                                                @if ($media['type'] == "video" || $media['type'] == "animated_gif")
                                                    <a class='tweet_media' href="{{ '#'.$media['index'] }}" title=@lang('title.video')>
                                                        @lang('title.video')
                                                        <img src="{{ $media['thumbnail'] }}" class="tweet_media" style="width:100%;" alt="">
                                                    </a>
                                                    <!--インライン-->
                                                    <div style='display:none'>
                                                        <div id="{{ $media['index'] }}">
                                                            <video controls preload>
                                                                <source src="{{ $media['url'] }}" type="video/mp4">
                                                            </video>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="col-md-12">{{ $tweet['tweet']['text'] }}</div>
                                @endif
                            </div>
                        </div>
                @endif
                @if ($tweet['type'] == 'retweet' || $tweet['type'] == 'quoted_retweet')
                    <!-- リツイートの表示 -->
                        <div class="media mt-3">
                            <div class="media-body">
                                @include('tweet', ['tweet' => $tweet['retweet']])
                            </div>
                        </div>
                @endif
                @if ($tweet['type'] == 'reply')
                    <!-- リプライの表示 -->
                        <div class="media mt-3">
                            <div class="media-body">
                                @if (!empty($tweet['reply']['reply_original']))
                                    @include('tweet', ['tweet' => $tweet['reply']['reply_original']])
                                @else  @include('no_tweet')  @endif
                            </div>
                        </div>
                @endif
            </div>
        </div>
    </div>
</div>
