function nengo(year){
    return year<1988? year: (year < 2019 ? '平成'+(year-1988) : '令和'+(year-2018))
  }

function make_timeline(id,items, groups, start_time, end_time, step){
    moment.locale("ja");
    const options = {
        start: start_time,  // timeline軸が表す期間の範囲の開始日
        end: end_time,    // （同）範囲の終了日
        width: '100%', //timelineの表示
        horizontalScroll: false,
        zoomable: false,    // timeline chartのzoomを無効にする 
        moveable: false,    // timeline アイテムの移動を無効にする
        orientation: 'top',   // timeline軸(見出し行）を上側に表示する
        showCurrentTime: false,
        stack: true,
        timeAxis: {scale: 'hour', step: step},// step in hours for time-axis
        format: {
        minorLabels: {
            hour: 'H',
        },        
        majorLabels: function (date, scale, step) { 
            var year = date.format('YYYY');
            return nengo(year) + date.format('年M月D日(dd)');
        }
        },
    };
    const container = document.getElementById(id);
    var timeline = new vis.Timeline(container, items, groups, options);
    var inners = document.getElementsByClassName("vis-inner");
    Array.from(inners).forEach(element => {
        var elem=element.firstChild;   
        if (elem.tagName =='A'){
            elem.classList.add('btn','btn-outline-info');
        }else{
            //element.classList.add('text-info');
        }    
    });
    

}