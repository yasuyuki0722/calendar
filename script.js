// //この書き方よくわからん
// var combo1 = function(){
//     var year = document.info.start_y;
//     var month = document.info.start_m;
//     var day = document.info.start_d;

//     var s_y = year.options[year.selectedIndex].value;
//     var s_m = month.options[month.selectedIndex].value;
//     var s_d = day.options[day.selectedIndex].value;


//     var date = new Date(s_y, s_m, 0);
//     var max = date.getDate();

//     //初期化
//     day.length = 0;

//     for (var i = 1; i <= max; i++) {
//         day.options[i] = new Option(i, i);
//     }

//     //0番目のよけいなoption削除
//     day.removeChild(day.options[0]);
//     //もし、最初に選んでいた日が、新しく生成した日付を超えていたら
//     if (s_d > day.length) {
//         day.options[day.length - 1].selected = true;
//     } else {
//         day.options[s_d - 1].selected = true;
//     }
// }

// var combo2 = function(){
//     var year = document.info.end_y;
//     var month = document.info.end_m;
//     var day = document.info.end_d;

//     var s_y = year.options[year.selectedIndex].value;
//     var s_m = month.options[month.selectedIndex].value;
//     var s_d = day.options[day.selectedIndex].value;

//     var date = new Date(s_y, s_m, 0);
//     var max = date.getDate();

//     //初期化
//     day.length = 0;

//     for (var i = 1; i <= max; i++) {
//         day.options[i] = new Option(i, i);
//     }


//     //0番目のよけいなoption削除
//     day.removeChild(day.options[0]);
//     //もし、最初に選んでいた日が、新しく生成した日付を超えていたら
//     if (s_d > day.length) {
//         day.options[day.length - 1].selected = true;
//     } else {
//         day.options[s_d - 1].selected = true;
//     }
// }

$(function(){
    $('.combo_year_month').change(function(){
        //開始と終了どちらのコンボボックスか
        var dd_id = $(this).parent('dd').attr('id');
        //selectedされている値を取得
        var year  = $('#'+dd_id+' .sch_year option:selected').val();
        var month = $('#'+dd_id+' .sch_month option:selected').val();
        var day   = $('#'+dd_id+' .sch_day option:selected').val()
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
            console.log($('#'+dd_id+' .sch_day option[value='+day_max+']'));
        } else {
            $('#'+dd_id+' .sch_day option[value='+day+']').attr('selected',true);
        }

    })
})

// $(function(){
//     $('.year_month').change(function(){
//         var day_count;
//         var dd_class = $(this).parent('dd').attr('class');
//         var option_len = $(dd_class+' .day').html('');
//         for(var i=0;day_count>=i;i++)
//         {
//             $(dd_class+' .day').append('<option value="'+$i+'">'+$i+'</option>');
//         }
// });

$(function(){
    $('form').submit(function(){
        var error_count = 0;

        var sch_title = $('input[name="sch_title"]').val();
        if (sch_title =='') {
            $('#error_msg_title').text('＊タイトルは入力必須');
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
        console.log(start_date.getTime());
        console.log(end_date.getTime());
        if (start_date.getTime() > end_date.getTime()) {
            $('#error_msg_date').text('＊開始日時より終了日時が先にきています');
            error_count++;
        } else {
            $('#error_msg_date').text('');
        }
        if (error_count > 0) {
            return false;
        } else {
        var start = start_y +'-'+ start_m +'-'+ start_d;
        var end = end_y +'-'+ end_m +'-'+ end_d;

        $(function(){
            $.ajax({
                type: 'post',
                url: 'cal_sql.php',
                data: {
                    //'schedule_id' : 109,
                    'schedule_title': sch_title,
                    'schedule_plan' : sch_plan,
                    'schedule_start': start,
                    'schedule_end'  : end,
                    'command' : 'insert'
                },
                success: function(data){
                    alert(data);
                }
            })
        })
            return true;
        }
    })
})





$(function(){
    $('#reset').click(function(){
        $('#schedule_edit').fadeOut();
    })
})


//日付クリックされたら
//日付を取得する
//その年月日のコンボボックスを正しく取得

$(function(){
    $('.day').click(function(){
        //tableから何年何月何日か取得
        var get_date = $(this).attr('id');
        var sch_date = get_date.split('-');
        var start_year = sch_date[0],
            start_month = sch_date[1],
            start_day = sch_date[2],
            end_year = sch_date[0],
            end_month = sch_date[1],
            end_day = sch_date[2];

        conboBoxMake(start_year, start_month, start_day, 'start_date');
        conboBoxMake(end_year, end_month, end_day, 'end_date');

        $('#schedule_edit').fadeIn();
    })
})



function conboBoxMake(year, month, day, dd_id){
            //関数化
            //取得年月から正しい日数を求める
            var s_date = new Date(year, month, 0);
            var day_max = s_date.getDate();

            var year_option = $('#'+dd_id+' .sch_year');
            var month_option = $('#'+dd_id+' .sch_month');
            var day_option = $('#'+dd_id+' .sch_day');

            //初期化
            $(year_option).html('');

            //年option作成
            for (var i = -1; i <= 1; i++){
                var j = i + parseInt(year);
                $(year_option).append('<option value="'+j+'">'+j+'</option>');
            }

            //当年にselected
            $('#'+dd_id+' .sch_year option[value='+year+']').attr('selected',true);

            //月は生成しなくていい
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

            //日時指定
            //
            return;
}



//予定がクリックされたら
//idをajaxに渡して予定データを受け取る
//データを変数に入れる
//コンボボックスに適用
//textのvalueにも適用

$(function(){
    $('.calendar_schedule').click(function(){
        //id="sch_id=**"の形のidを取得
        var sch_id = $(this).attr('id');
        var schedule_id = sch_id.split('=');
        console.log(schedule_id[1]);

        $(function(){
            $.ajax({
                type: 'post',
                url: 'cal_sql.php',
                data: {
                    'schedule_id' : schedule_id[1],
                    'command' : 'select'
                },
                success: function(data){
                    var schedule_array = JSON.parse(data); 
                    console.log(schedule_array['schedule_plan']);
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

                    conboBoxMake(start_y, start_m, start_d, 'start_date');
                    conboBoxMake(end_y, end_m, end_d, 'end_date');

                    $('#schedule_title').val(schedule_array['schedule_title']);
                    $('#schedule_plan').val(schedule_array['schedule_plan']);
                }
            })
        })


        $('#schedule_edit').fadeIn();
    })
})




    // function check(){
    //     var must = Array('sch_title', 'sch_plan');
    //     var miss = Array('タイトル', '内容');
    //     var leng = must.length;
    //     var er_count = 0;
    //     for (var i = 0; i < leng; i++) {
    //         var obj = document.info.elements[must[i]];
    //         if (obj.type=='text' || obj.type=='textarea' ) {
    //             if (obj.value == '') {
    //                  alert(miss[i] + 'は必須です');
    //                 // document.info.elements[must[i]].focus;
    //                 // var e = document.createElement('name');
    //                 //     t = document.createTextNode('ないよ！')
    //                 // document.body.appendChild(e).appendChild(t);
    //                 er_count++;
    //             }

    //         }
    //     }
    //     if (er_count > 0) {
    //         return false;
    //     };
    //     return true;
    // }

    // function link(){
    //     window.open("cal_edit.php", null,'width=400, height=400, menubar=no, toolbar=no, scrollbars=yes');
    //     return;
    // }

    //     <script type="text/javascript">
    // function link(){
    // var y = <?php echo $year; ?>,
    //     m = <?php echo $month; ?>,
    //     d = <?php echo $day; ?>;
    //     url = 'cal_edit.php?sch_y='+y+'&sch_m='+m+'&sch_d='+d;
    //     window.open(url, null,'width=400, height=400, menubar=no, toolbar=no, scrollbars=yes');
    // return;
    // }
    // </script>