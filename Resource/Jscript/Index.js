/**
 * Created by Administrator on 2016/12/14.
 */
function outTime(){
    var years, months, days, hours, minutes, seconds, weeks_zh_cn, weeks_en, weekdays, outTime;
    var distance, d_days=0, d_hours=0, d_minutes=0, d_seconds=0, countDown;
    var lastDate = new Date(2018,3,10,9,1,1);
    var date = new Date();
    years = date.getFullYear();
    months = date.getMonth()+1; /*月的返回值是从0开始计算的，所以需要+1*/
    days = date.getDate();
    hours = date.getHours();
    minutes = date.getMinutes();
    seconds = date.getSeconds();
    weekdays = date.getDay();
    if(months < 10){
        months = "0"+months.toString();
    }
    if(days < 10){
        days = "0"+days.toString();
    }
    if(hours < 10){
        hours = "0"+hours.toString();
    }
    if(minutes < 10){
        minutes = "0"+minutes.toString();
    }
    if(seconds < 10){
        seconds = "0"+seconds.toString();
    }
    // weeks_zh_cn = {0:"星期日", 1:"星期一", 2:"星期二", 3:"星期三", 4:"星期四", 5: "星期五", 6:"星期六"};
    weeks_en = {0: "Sunday", 1: "Monday", 2: "Tuesday", 3: "Wednesday", 4: "Thursday", 5: "Friday", 6: "Saturday"};
    outTime = "Now："+months+'/ '+days+'/ '+years+'  '+hours+'H '+minutes+'M '+seconds+'S '+weeks_en[weekdays];
    distance = parseInt((lastDate.getTime()-date.getTime())/1000);
    if(distance > 0){
        d_days = Math.floor(distance/(24*60*60));
        d_hours = Math.floor((distance-d_days*60*60*24)/(60*60));
        d_minutes = Math.floor((distance-d_days*60*60*24-d_hours*60*60)/60);
        d_seconds = Math.floor(distance-d_days*60*60*24-d_hours*60*60-d_minutes*60);
    }

    if(d_days < 10){
        d_days = "0"+d_days.toString();
    }
    if(d_hours < 10){
        d_hours = "0"+d_hours.toString();
    }
    if(d_minutes < 10){
        d_minutes = "0"+d_minutes.toString();
    }
    if(d_seconds < 10){
        d_seconds = "0"+d_seconds.toString();
    }
    countDown = "DeadLine: "+d_days+"/"+d_hours+"/"+d_minutes+"/"+d_seconds;
    document.getElementById("nowTime").innerHTML = outTime;
    document.getElementById('countTime').innerHTML = countDown;
}
setInterval("outTime()", 100);



