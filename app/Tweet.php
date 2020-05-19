<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

/**
 * Tweetモデル
 */
class Tweet extends Model
{
    /**
     * ツイート検索
     *
     * @param Request $request
     * @return array $results
     */
    public static function tweetSearch($request)
    {
        \Log::info(__FILE__.':'.__LINE__.':'.__CLASS__.':'. __FUNCTION__.':entry');

        try {
            // ツイートを検索する
            $tweets = Tweet::getTweet($request);
            // 加工
            $results['tweets'] = [];
            foreach ($tweets['tweets'] as $index => $tweet) {
                // ツイートを整形する
                $result = Tweet::formatTweet($request, $tweet, 'normal');
                // レスポンス
                array_push($results['tweets'],$result);
            }
            // グラフのデータ取得
            $results['chart_data'] = $tweets['chart_data'];

            return $results;
        } catch (\Throwable $th) {
            \Log::error($th);
        } finally {
            \Log::info(__FILE__.':'.__LINE__.':'.__CLASS__.':'. __FUNCTION__.':exit');
        }
    }

    /**
     * ツイッターAPIのツイート取得
     *
     * @param Request $request
     * @param string $id
     * @return array $results
     */
    public static function getTweet($request, $id = null)
    {
        \Log::info(__FILE__.':'.__LINE__.':'.__CLASS__.':'. __FUNCTION__.':entry');

        // タイムアウトの設定を延ばす
        set_time_limit(60);

        try {
            $results['tweets'] = [];
            $results['chart_data'] = [];

            // idが指定されている場合
            if (isset($id)) {
                $results['tweets'] = [\Twitter::get('statuses/show', array('id' => $id))];
            }
            // 通常検索：id指定無しの場合
            else {
                // デバッグ時のみ表示（APIの制限）
                if (config('app.debug')) {
                    dump(\Twitter::get('application/rate_limit_status', array('resources'=>'search')));
                };

                // 検索条件
                // 検索文字列
                $keyword = $request['keyword'];
                // 開始日時
                $query_input_date_time = ' since:' . $request['date_time_from'] . '_JST';
                // 終了期間
                $query_until_date_time = ' until:' . $request['date_time_to'] . '_JST';
                // APIに送る検索文字列
                $query = $keyword . $query_input_date_time . $query_until_date_time;
                // リツイートを除くか
                $without_rt = $request['without_rt_check'];
                if ($without_rt == 'without') {
                    $query = $query . ' -filter:retweets';
                }
                // メディア
                $select_media = $request['select_media'];
                if ($select_media == 'only_media') {
                    $query = $query . ' filter:media';
                }
                else if ($select_media == 'only_images') {
                    $query = $query . ' filter:images';
                }
                else if ($select_media == 'only_video') {
                    $query = $query . ' filter:native_video';
                }
                else if ($select_media == 'without_media') {
                    $query = $query . ' -filter:media';
                }

                // ツイートを取得する
                $results['tweets'] = [];
                $tweet_conut = 0;
                $max_id = null;
                do {
                    // ツイートを取得する
                    if (isset($max_id)) {
                        $tweets = \Twitter::get('search/tweets', array('q' => $query, 'count' => 100,'max_id' => $max_id));
                    } else {
                        $tweets = \Twitter::get('search/tweets', array('q' => $query, 'count' => 100));
                    }

                    if (!empty($tweets->statuses)) {
                        $results['tweets'] = array_merge($results['tweets'],$tweets->statuses);
                        if (null !== end($tweets->statuses)) {
                            $max_id = end($tweets->statuses)->id - 1;
                        }else {
                            break;
                        }
                        $tweet_conut = count($tweets->statuses);
                    }

                } while ($tweet_conut == 100);
            }
            // 取得したツイートの時刻を日本時間に直す
            foreach ($results['tweets'] as $key => $value) {
                if (isset($value->id)) {
                    $t = new \DateTime($value->created_at);
                    $t->setTimeZone(new \DateTimeZone('Asia/Tokyo'));
                    $value->created_at = date('Y-m-d H:i', strtotime((string)$t->format(\DateTime::COOKIE)));
                    array_push($results['chart_data'], $value->created_at);
                }
            }

            $results['chart_data'] = json_encode($results['chart_data']);

            return $results;
        } catch (\Throwable $th) {
            \Log::error($th);
        } finally {
            \Log::info(__FILE__.':'.__LINE__.':'.__CLASS__.':'. __FUNCTION__.':exit');
        }
    }

    /**
     * ツイートの整形
     *
     * @param Request $request
     * @param Object $tweet
     * @param string $tweet_type
     * @return array $results
     */
    public static function formatTweet($request,$tweet,$tweet_type)
    {
        try {
            \Log::info(__FILE__.':'.__LINE__.':'.__CLASS__.':'. __FUNCTION__.':entry');

            if (isset($tweet->user)) {
                // ツイートタイプ
                $result['type'] = $tweet_type;

                // ユーザ情報
                $result['user']['name'] = $tweet->user->name;
                $result['user']['id'] = $tweet->user->screen_name;
                $result['user']['profile_image'] = $tweet->user->profile_image_url_https;

                // ツイート情報
                $result['tweet']['id'] = $tweet->id_str;
                $result['tweet']['post_time'] = date('Y-m-d H:i', strtotime($tweet->created_at));
                $result['tweet']['text'] = $tweet->text;
                $result['tweet']['original_url'] = 'https://twitter.com/' . $result['user']['id'] . '/status/' . $tweet->id_str;
                $result['tweet']['media'] = [];
                if (isset($tweet->extended_entities)) {
                    $result['tweet']['media'] = Tweet::getTweetMedia($tweet->extended_entities->media,$tweet->id_str);
                }

                if ($tweet_type == 'normal') {
                    // リツイート情報
                    $result['retweet'] = [];
                    if (isset($tweet->retweeted_status)) {
                        $result['type'] = 'retweet';
                        $result['retweet']['type'] = 'retweet_original';
                        $result['retweet']['user']['name'] = $tweet->retweeted_status->user->name;
                        $result['retweet']['user']['id'] = $tweet->retweeted_status->user->screen_name;
                        $result['retweet']['user']['profile_image'] = $tweet->retweeted_status->user->profile_image_url_https;
                        $result['retweet']['tweet']['id'] = $tweet->retweeted_status->id_str;
                        $result['retweet']['tweet']['post_time'] = date('Y-m-d H:i', strtotime($tweet->retweeted_status->created_at));
                        $result['retweet']['tweet']['text'] = $tweet->retweeted_status->text;
                        $result['retweet']['tweet']['media'] = [];
                        $result['retweet']['tweet']['original_url'] = 'https://twitter.com/' . $result['retweet']['user']['id'] . '/status/' . $tweet->retweeted_status->id_str;
                        if (isset($tweet->retweeted_status->extended_entities)) {
                            $result['retweet']['tweet']['media'] = Tweet::getTweetMedia($tweet->retweeted_status->extended_entities->media, $tweet->retweeted_status->id_str);
                        }
                    }
                    elseif (isset($tweet->quoted_status)) {
                        $result['type'] = 'quoted_retweet';
                        $result['retweet']['type'] = 'retweet_original';
                        $result['retweet']['user']['name'] = $tweet->quoted_status->user->name;
                        $result['retweet']['user']['id'] = $tweet->quoted_status->user->screen_name;
                        $result['retweet']['user']['profile_image'] = $tweet->quoted_status->user->profile_image_url_https;
                        $result['retweet']['tweet']['id'] = $tweet->quoted_status->id_str;
                        $result['retweet']['tweet']['post_time'] = date('Y-m-d H:i', strtotime($tweet->quoted_status->created_at));
                        $result['retweet']['tweet']['text'] = $tweet->quoted_status->text;
                        $result['retweet']['tweet']['media'] = [];
                        $result['retweet']['tweet']['original_url'] = 'https://twitter.com/' . $result['retweet']['user']['id'] . '/status/' . $tweet->quoted_status->id_str;
                        if (isset($tweet->quoted_status->extended_entities)) {
                            $result['retweet']['tweet']['media'] = Tweet::getTweetMedia($tweet->quoted_status->extended_entities->media, $tweet->quoted_status->id_str);
                       }
                    }

                    // リプライ
                    $result['reply'] = [];
                    if (isset($tweet->in_reply_to_status_id_str)) {
                        $result['type'] = 'reply';
                        $result['reply']['type'] = 'reply_original';
                        $reply_original_tweets = Tweet::getTweet($request,$tweet->in_reply_to_status_id);
                        $reply_original_tweet = Tweet::formatTweet($request, $reply_original_tweets['tweets'][0], 'reply_original_tweet');
                        $result['reply']['reply_original'] = (isset($reply_original_tweet)) ? $reply_original_tweet : [];
                    }
                }

                return $result;
            }
        } catch (\Throwable $th) {
            \Log::error($th);
        } finally {
            \Log::info(__FILE__.':'.__LINE__.':'.__CLASS__.':'. __FUNCTION__.':exit');
        }
    }

    /**
     * ツイートのメディアを取得
     *
     * @param array $medias
     * @param string $tweet_id
     * @return array $responce_medias
     */
    public static function getTweetMedia($medias,$tweet_id)
    {
        try {
            \Log::info(__FILE__.':'.__LINE__.':'.__CLASS__.':'. __FUNCTION__.':entry');

            $responce_medias = [];

            foreach ($medias as $index => $media) {
                $media_type = $media->type;

                // ツイートごとのインデックス
                $responce_media['index'] = $tweet_id . '_' . $index;

                // 画像
                if ($media_type == 'photo') {
                    $responce_media['type'] = $media_type;
                    $responce_media['url'] = $media->media_url_https;
                }
                // 動画
                else if ($media_type == 'video' || $media_type == 'animated_gif') {
                    $responce_media['type'] = $media_type;
                    $responce_media['url'] = $media->video_info->variants[0]->url;
                    $responce_media['thumbnail'] = $media->media_url_https;
                }
                array_push($responce_medias, $responce_media);
            }
            return $responce_medias;
        } catch (\Throwable $th) {
            \Log::error($th);
        } finally {
            \Log::info(__FILE__.':'.__LINE__.':'.__CLASS__.':'. __FUNCTION__.':exit');
        }
    }
}
