/**
 * Twitter
 */
import moment from "moment";

export default class twitter {

    /**
     * コンストラクタ
     */
    constructor() {
        this.setEvent();

        // フォームを入力可能に戻す
        $('.segment').dimmer('hide');
        $('.form-disabled').removeAttr("disabled");
        $(":input[name='time_unit']").prop('disabled',false);

        // sessionStorage に保存したデータを取得する
        let serach_conditions_json = sessionStorage.getItem('serach_conditions');
        if (serach_conditions_json) {
            // JOSNから配列に変換
            let serach_conditions = JSON.parse(serach_conditions_json);
            twitter.createForm(serach_conditions);
        }
        else {
            // フォームの初期化
            twitter.createForm();
        }

        // 日付を設定する
        $('.input_date_time').trigger('change');

        // 検索範囲
        let from_date_moment = moment($('#date_from_hidden').val().replace('_', ' '));
        let to_date_moment = moment($('#date_to_hidden').val().replace('_', ' '));

        // 差分を分で取得する
        let duration_minutes = to_date_moment.diff(from_date_moment) / 60000;

        // １時間以下はグラフを表示しない
        if ($('#date_from_hidden').val() != '' && $('#date_to_hidden').val() != '' && duration_minutes >= 60) {
            // グラフを表示する
            $('#chart_div').removeClass("d-none")
        }

        // colorboxの設定
        for (let index = 0; index < $('.tweet_id').length; index++) {
            // ツイートID取得
            let tweet_id = $('.tweet_id')[index].value;
            // colorboxに設定するクラス
            let inline_class = 'inline_' + tweet_id;
            // メディア表示のdivを取得
            let tweet_media_div = $(`#tweet_media_div_${$('.tweet_id')[index].value}`);
            // リンクにクラスを設定
            let tweet_media_a =  tweet_media_div.find('a');
            for (let index = 0; index < tweet_media_a.length; index++) {
                $(tweet_media_a[index]).addClass(inline_class);
            }
            // colorboxを生成
            $("." + inline_class).colorbox({inline:true, rel:inline_class});
        }
    }

    /**
     * イベントの設定
     */
    setEvent() {
        // 検索ボタン
        $("#search_form").submit(function () {

            // フォームを入力不可とする
            $('.segment').dimmer('show');
            $('.form-disabled').attr("disabled", "");
            $(":input[name='time_unit']").prop('disabled',true);

            // 検索範囲 開始時間
            // 入力値の取得
            let input_date_from = $('#date_select').val();
            let input_time_from = $('#time_select').val();
            let input_date_time_from = input_date_from + ' ' + input_time_from;
            let moment_input_date_time_from = moment(input_date_time_from);

            // 検索範囲 終了時間
            // 入力値の取得
            let time_unit = $("input[name='time_unit']:checked").val();
            let time_value = Number($("#time_value_select").val());
            // 終了時間
            let to_time = moment.duration(time_value, time_unit);

            // サーバに送る値を設定
            $('#keyword_txt_hidden').val($('#keyword_txt').val());
            $('#select_media_hidden').val($('#select_media').val());
            $('#without_rt_check_hidden').val($('#without_rt_check').val());
            $('#date_from_hidden').val(moment_input_date_time_from.format("YYYY-MM-DD_HH:mm:SS"));
            $('#date_to_hidden').val(moment_input_date_time_from.add(to_time).format("YYYY-MM-DD_HH:mm:SS"));

            // セッションに検索条件を保存する
            let serach_conditions = {
                keyward: $('#keyword_txt').val(),
                date_select: input_date_from,
                time_select: input_time_from,
                time_unit: time_unit,
                time_value: time_value,
                select_media: $('#select_media').val(),
                without_rt: $('#without_rt_check').val(),
                date_from_hidden:$('#date_from_hidden').val(),
                date_to_hidden:$('#date_to_hidden').val(),
            };
            // serach_conditions.push({ keyward: $('#keyword_txt').val() });
            sessionStorage.setItem('serach_conditions', JSON.stringify(serach_conditions));

        });

        // クリアボタン
        $('#search_clear_btn').on('click',function () {
            twitter.createForm();
        });

        // 閉じるボタン
        $('#search_close_btn').on('click',function () {
            $('[data-remodal-id=twitter_search_modal]').remodal().close()
        });

        // グラフを表示ボタン
        $('#show_chart_btn').on('click', function () {
            // グラフの表示
            if ($('#chart_data_hidden').val().length > 0) {
                // グラフの情報を取得する
                let chart_data_array = JSON.parse($('#chart_data_hidden').val());

                // 検索範囲
                let from_date_moment = moment($('#date_from_hidden').val().replace('_', ' '));
                from_date_moment.format("YYYY-MM-DD HH:mm");
                let to_date_moment = moment($('#date_to_hidden').val().replace('_', ' '));
                to_date_moment.format("YYYY-MM-DD HH:mm");

                // 差分を分で取得する
                let duration_minutes = to_date_moment.diff(from_date_moment) / 60000;

                // グラフの間隔
                let time_value = 1;
                let time_unit = 'hours';

                if (duration_minutes <= 180) {
                    // １時間〜３時間 ５分間隔
                    time_value = 5;
                    time_unit = 'minutes';
                }
                else if (duration_minutes <= 300) {
                    // ４時間〜５時間 １０分間隔
                    time_value = 10;
                    time_unit = 'minutes';
                }
                else if (duration_minutes <= 660) {
                    // ６時間〜１１時間 １５分間隔
                    time_value = 15;
                    time_unit = 'minutes';
                }
                else if (duration_minutes <= 1440) {
                    // １２時間〜１日 ３０分間隔
                    time_value = 30;
                    time_unit = 'minutes';
                }
                else if (duration_minutes <= 5760) {
                    // ２日〜４日 ２時間間隔
                    time_value = 2;
                    time_unit = 'hours';
                }
                else if (duration_minutes <= 10080) {
                    // ５日〜７日 ４時間間隔
                    time_value = 4;
                    time_unit = 'hours';
                }

                let to_time = moment.duration(time_value, time_unit);

                // x軸となる配列(最初の日付は再フォーマットしないと表示がおかしくなる)
                let x_axis = ['times', moment(from_date_moment.format("YYYY-MM-DD HH:mm"))];

                // ループの判定用変数
                let clone_date_moment = null;
                while (clone_date_moment < to_date_moment) {
                    // 時間を定義
                    let tmp_date_moment = moment(from_date_moment.add(to_time).format("YYYY-MM-DD HH:mm").toString());
                    // 配列に格納
                    x_axis.push(tmp_date_moment);
                    // ループの判定用
                    clone_date_moment = tmp_date_moment.clone();
                    // 検索範囲以上の時間になったらループを抜ける
                    if (clone_date_moment > to_date_moment) {
                        x_axis.push(to_date_moment);
                        break;
                    }
                }

                // データとなる配列
                let text = ($('#keyword_txt').val() != '') ? $('#keyword_txt').val() : 'unknown';
                let data_array = [text];

                // x軸でループ
                x_axis.forEach((value, index) => {
                    if (value != 'times') {
                        let check_date_from = moment(x_axis[index]);
                        let check_date_to = moment(x_axis[index + 1]);

                        // カウント
                        let conut = 0;

                        for (let index = 0; index < chart_data_array.length; index++) {
                            let chart_target = moment(chart_data_array[index]);
                            if (check_date_from < chart_target && chart_target < check_date_to) {
                                conut = conut + 1;
                            }
                        }

                        data_array.push(conut);
                    }
                });

                // グラフの生成
                var chart = c3.generate({
                    bindto: '#c3_chart',
                    data: {
                      x: 'times',
                      xFormat: '%m-%d %H:%M',
                      columns: [
                        x_axis,
                        data_array
                      ]
                    },
                    axis: {
                        x: {
                            type: 'timeseries',
                            tick: {
                                format: '%m-%d %H:%M'
                            }
                        }
                    }
                });
            }
        });

        // ラジオボタンの変更イベント
        $("input[name='time_unit']").change(function () {

            // 時間の単位を取得
            let time_unit = $("input[name='time_unit']:checked").val();
            let min_value = 1;

            if (time_unit == "minutes") {
                min_value = 5;
                $("#time_value_select").attr("min", min_value);
                $("#time_value_select").attr("max", "50");
                $("#time_value_select").attr("step", "5");
                $("#time_unit_lb").text("分");
            }
            else if (time_unit == "hours") {
                $("#time_value_select").attr("min", min_value);
                $("#time_value_select").attr("max", "23");
                $("#time_value_select").attr("step", "1");
                $("#time_unit_lb").text("時間");
            }
            else {
                $("#time_value_select").attr("min", min_value);
                $("#time_value_select").attr("max", "7");
                $("#time_value_select").attr("step", "1");
                $("#time_unit_lb").text("日");
            }

            $("#time_value_select").val(min_value);

            $('.input_date_time').trigger('change');

        });

        // 時間の変更イベント
        $('.input_date_time').change(function () {
            // fromの取得
            let input_date_time_from = $('#date_select').val() + ' ' + $('#time_select').val();
            let moment_input_date_time_from = moment(input_date_time_from);
            let from_str = moment_input_date_time_from.format("YYYY年MM月DD日のHH時mm分");

            // toの取得
            let time_unit = $("input[name='time_unit']:checked").val();
            let time_unit_str = '分';
            if (time_unit == 'hours') {
                time_unit_str = '時';
            }
            else if (time_unit == 'days') {
                time_unit_str = '日';
            }
            let time_value = Number($("#time_value_select").val());
            let to_str = `から${time_value}${time_unit_str}間`;

            $('#from_to_lb').text(from_str + to_str);

        });
    }
    /**
     * 検索条件の設定
     * @param  {array} serach_conditions=null
     */
    static createForm(serach_conditions = null) {
        if (serach_conditions != null) {
            // セッションの情報から検索条件を設定する
            $('#keyword_txt').val(serach_conditions['keyward']);
            $('#date_select').val(serach_conditions['date_select']);
            $('#time_select').val(serach_conditions['time_select']);
            $('input[value=' + serach_conditions['time_unit'] + ']').prop('checked', true);
            $("input[name='time_unit']").trigger('change');
            $('#time_value_select').val(serach_conditions['time_value']);
            $('#select_media').val(serach_conditions['select_media']);
            $('input[value=' + serach_conditions['without_rt'] + ']').prop('checked', true);
            $('#date_from_hidden').val(serach_conditions['date_from_hidden']);
            $('#date_to_hidden').val(serach_conditions['date_to_hidden']);
        }
        else {
            // 日付の下限定数（１週間）
            const seven_days = moment.duration(6, "days");

            // キーワード
            $('#keyword_txt').val('');
            $('#keyword_txt').focus();

            // 「いつから」
            // 現在時刻を取得
            let moment_date = moment().clone();
            // 日付の上限・下限を設定
            $("#date_select").attr("max",moment_date.format("YYYY-MM-DD"));
            $("#date_select").attr("min", moment_date.subtract(seven_days).format("YYYY-MM-DD"));
            // 日付の下限を初期値とする
            $("#date_select").val(moment_date.format("YYYY-MM-DD"));
            // 時間
            $("#time_select").val('00:00');

            // 「いつまで」
            $('input[value="minutes"]').prop('checked', true);
            $("#time_value_select").attr("min", "5");
            $("#time_value_select").attr("max", "50");
            $("#time_value_select").attr("step", "5");
            $("#time_value_select").val("5");
            $("#time_unit_lb").text("分");

            // メディア
            $('#select_media').val('all_tweet');

            // その他の検索条件
            $('＃without_rt_check').prop('checked', false);
        }

    }

}
