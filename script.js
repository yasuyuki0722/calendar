/**
*
*
*/
resister_flg =false;

function sessionSet(){
    $.ajax({
        async:false,
        url: 'session.php'
    }).done(function(data){
        $('input[name="nk_token"]').val(data);
    }).fail(function(){
        alert('接続に失敗しました（Ajax/session）');
    })
}

$(function(){
    $('.combo_year_month').change(function(){
        //開始と終了どちらのコンボボックスか
        var dd_id = $(this).parent('dd').attr('id');
        //selectedされている値を取得
        var year  = $('#'+dd_id+' .sch_year option:selected').val();
        var month = $('#'+dd_id+' .sch_month option:selected').val();
        var day   = $('#'+dd_id+' .sch_day option:selected').val();
        //日のoptionを取得
        var day_option = $('#'+dd_id+' .sch_day');

        //取得年月から正しい日数を求める
        var date = new Date(year, month, 0);
        var day_max = date.getDate();

        //初期化
        $(day_option).html('');

        //日数分option作成
        for (var i = 1; i <= day_max; i++){
            $('#'+dd_id+' .sch_day').append('<option value="'+i+'">'+i+'</option>');
        }

        //もし、最初に選んでいた日が、新しく生成した日付を超えていたら
        if (day > day_max) {
            $('#'+dd_id+' .sch_day option[value='+day_max+']').attr('selected',true);
        } else {
            $('#'+dd_id+' .sch_day option[value='+day+']').attr('selected',true);
        }
    })
})

$(function(){
     $('#submit').click(function(){
        var error_count = 0;
        var sch_title = $('input[name="sch_title"]').val();
        if (sch_title == '') {
            $('#error_msg_title').text('＊タイトルは入力必須');
            error_count++;
        } else if (sch_title.length > 45) {
            $('#error_msg_title').text('＊タイトルは４５字以内');
            error_count++;
        } else {
            $('#error_msg_title').text('');
        }

        var sch_plan = $('textarea[name="sch_plan"]').val();
        if (sch_plan == '') {
            $('#error_msg_plan').text('＊内容は入力必須');
            error_count++;
        } else {
            $('#error_msg_plan').text('');
        }

        //JQueryにしたい
        var start_y = $('select[name="start_y"]').val();
        var start_m = $('select[name="start_m"]').val();
        var start_d = $('select[name="start_d"]').val();
        var start_h = $('select[name="start_h"]').val();
        var start_i = $('select[name="start_i"]').val();
        var end_y = $('select[name="end_y"]').val();
        var end_m = $('select[name="end_m"]').val();
        var end_d = $('select[name="end_d"]').val();
        var end_h = $('select[name="end_h"]').val();
        var end_i = $('select[name="end_i"]').val();

        var start_date = new Date(start_y, start_m, start_d, start_h, start_i);
        var end_date   = new Date(end_y, end_m, end_d, end_h, end_i);

        var start_ymd = start_y +'-'+ start_m +'-'+ start_d+'-'+start_h +'-'+ start_i;
        var end_ymd = end_y +'-'+ end_m +'-'+ end_d+'-'+ end_h +'-'+ end_i;

        if (start_date.getTime() > end_date.getTime()) {
            $('#error_msg_date').text('＊開始日時より終了日時が先にきています');
            error_count++;
        } else {
            $('#error_msg_date').text('');
        }

        //schedule_idを取得
        var schedule_id = $('#schedule_id').text();
        //'保存'連打対応：一回目ならflgをtrueに
        if (typeof flg == "undefined") {
            flg = true;
            var resister_flg =false;
        }
        //エラーチェック
        if (error_count > 0) {
            return false;
        } else {
            if (flg) {
                var token = $('input[name="nk_token"]').val();
                $(function(){
                    console.log(token);
                    $.ajax({
                        type: 'post',
                        url: 'cal_sql.php',
                        data: {
                            'schedule_id'   : schedule_id,
                            'schedule_title': sch_title,
                            'schedule_plan' : sch_plan,
                            'schedule_start': start_ymd,
                            'schedule_end'  : end_ymd,
                            'command'       : 'resister',
                            'nk_token':token
                        }
                    }).done(function(data){
                        console.log(data);
                        var schedule_array = JSON.parse(data); 
                        if (typeof schedule_array['error_msg'] != undefined) {
                            resister_flg = true;
                        }
                    }).always(function(data){
                        if (resister_flg) {
                        //'保存'連打対応：flgをfalseに
                        flg = false;
                        $(location).attr('href', '');
                        }
                    }).fail(function(data){
                        alert('接続に失敗しました');
                    })
                })
            }
        }
    })
})


$(function(){
    $('#delete').click(function(){
        var sch_id = $('#schedule_id').text();
        var token = $('input[name="nk_token"]').val();
        $(function(){
            $.ajax({
                type: 'post',
                url: 'cal_sql.php',
                data: {
                    'schedule_id' : sch_id,
                    'nk_token' : token,
                    'command' : 'delete'
                }
            }).done(function(data){
                var schedule_array = JSON.parse(data); 
                if (typeof schedule_array['error_msg'] != undefined) {
                    resister_flg = true
                }
            }).always(function(data){
                if (resister_flg) {
                    $(location).attr('href', '');
                } else {
                    alert('接続に失敗しました');
                }
            }).fail(function(data){
                alert('接続に失敗しました');
            })
        })
    })
})

$(function(){
    $('#shadow').click(function(){
        $('#schedule_edit').fadeOut();
        $('#shadow').fadeOut();
    })
})

$(function(){
    $('#reset').click(function(){
        $('#schedule_edit').fadeOut();
        $('#shadow').fadeOut();

        return false;
    })
})


//日付クリックされたら
//日付を取得する
//その年月日のコンボボックスを正しく取得

$(function(){
    $('.day').click(function(){
        formReset();
        //sessionにワンタイムトークンセット
        sessionSet();
        $('#delete').css('display','none');
        //tableから何年何月何日か取得
        var get_date = $(this).data('dateinfo');
        var sch_date = get_date.split('-');
        var start_year = sch_date[0],
            start_month = sch_date[1],
            start_day = sch_date[2],
            end_year = sch_date[0],
            end_month = sch_date[1],
            end_day = sch_date[2];

        comboBoxMake(start_year, start_month, start_day, 0, 0,'start_date');
        comboBoxMake(end_year, end_month, end_day, 0, 0, 'end_date');

        $('#schedule_edit').fadeIn();
        $('#shadow').fadeIn();
    })
})

function formReset(){
    $('#schedule_id').text('');
    $('input[name="sch_title"]').val('');
    $('textarea[name="sch_plan"]').val('');
    $('#error_msg_date').text('');
    $('#error_msg_title').text('');
    $('#error_msg_plan').text('');

    //削除ボタン表示
    $('#delete').css('display','');
}

function comboBoxMake(year, month, day, hour, minute, dd_id){
    //関数化
    //取得年月から正しい日数を求める
    var s_date  = new Date(year, month, 0);
    var day_max = s_date.getDate();

    var year_option  = $('#'+dd_id+' .sch_year');
    var month_option = $('#'+dd_id+' .sch_month');
    var day_option   = $('#'+dd_id+' .sch_day');
    var hour_option     = $('#'+dd_id+' .sch_hour');
    var minute_option   = $('#'+dd_id+' .sch_minute');

    //初期化
    $(year_option).html('');

    //年option作成
    for (var i = -1; i <= 1; i++){
        var j = i + parseInt(year);
        $(year_option).append('<option value="'+j+'">'+j+'</option>');
    }

    //当年にselected
    $('#'+dd_id+' .sch_year option[value='+year+']').attr('selected',true);

    //月の生成
    //初期化
    $(month_option).html('');

    //年option作成
    for (var i = 1; i <= 12; i++){
        $(month_option).append('<option value="'+i+'">'+i+'</option>');
    }
    //当月にselected
    $('#'+dd_id+' .sch_month option[value='+month+']').attr('selected',true);

    //初期化
    $(day_option).html('');

    //日数分option作成
    for (var i = 1; i <= day_max; i++){
        $(day_option).append('<option value="'+i+'">'+i+'</option>');
    }

    //当日にselected
    $('#'+dd_id+' .sch_day option[value='+day+']').attr('selected',true);

    //初期化
    $(hour_option).html('');

    //時刻分option作成
    for (var i = 0; i <= 23; i++){
        $(hour_option).append('<option value="'+i+'">'+i+'</option>');
    }

    //当時刻にselected
    $('#'+dd_id+' .sch_hour option[value='+hour+']').attr('selected',true);

    //初期化
    $(minute_option).html('');

    //時刻分option作成
    for (var i = 0; i <= 30; i+=30){
        $(minute_option).append('<option value="'+i+'">'+i+'</option>');
    }

    //当時刻にselected
    $('#'+dd_id+' .sch_minute option[value='+minute+']').attr('selected',true);

    return;
}



//予定がクリックされたら
//idをajaxに渡して予定データを受け取る
//データを変数に入れる
//コンボボックスに適用
//textのvalueにも適用

$(function(){
    $('.calendar_schedule').click(function(){
        formReset();
        sessionSet();
        //data-scheduleidを取得
        var schedule_id = $(this).data('scheduleid');
        //div schedule_id内にschedule_idを書き込む
        $('#schedule_id').text(schedule_id);

        var token = $('input[name="nk_token"]').val();

        $(function(){
            $.ajax({
                type: 'post',
                url: 'cal_sql.php',
                data: {
                    'schedule_id' : schedule_id,
                    'command' : 'select',
                    'nk_token' : token
                }
            }).done(function(data){
                var schedule_array = JSON.parse(data); 
                if (typeof schedule_array['error_msg'] == "undefined") {
                    var start_date = new Date(schedule_array['schedule_start']);

                    var start_y = start_date.getFullYear(),
                        start_m = start_date.getMonth() + 1,
                        start_d = start_date.getDate(),
                        start_h = start_date.getHours(),
                        start_i = start_date.getMinutes();

                    var end_date = new Date(schedule_array['schedule_end']);
                    var end_y = end_date.getFullYear(),
                        end_m = end_date.getMonth() + 1,
                        end_d = end_date.getDate(),
                        end_h = end_date.getHours(),
                        end_i = end_date.getMinutes();

                    //コンボボックス作成
                    comboBoxMake(start_y, start_m, start_d, start_h, start_i, 'start_date');
                    comboBoxMake(end_y, end_m, end_d, end_h, end_i, 'end_date');
                    //inputにタイトル、内容を書き込む
                    $('#schedule_title').val(schedule_array['schedule_title']);
                    $('#schedule_plan').val(schedule_array['schedule_plan']);
                    } else {
                        alert(schedule_array['error_msg']);
                }
            }).fail(function(){
                alert('接続に失敗しました');
            })
        })
        $('#schedule_edit').fadeIn();
        $('#shadow').fadeIn();
    })
})


