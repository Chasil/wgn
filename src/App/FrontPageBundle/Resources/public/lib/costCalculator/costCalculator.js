Number.prototype.format = function()
{
        var a = new String(this)
        a = a.split(/\,|\./)
        a[0] = a[0].split('')
        var st = a[0].length%3
        var ret = ""
        var i=0
        for(var j=0; j < a[0].length; i++)
                ret+=(i == st || (i-st)%4 == 0 ? " " : a[0][j++])
                if(a[1] && a[1].length==1) a[1]+="0"
        return ret.replace(/(^\s*)|(\s*$)/g, "") + (a[1] ? "," + a[1] : ",00")
}

var sp_w = 2
var wpis = false //wpis do ksiegi wieczystej


function setval(src)
{
        sp_w = src.value
        calculate()
}

function calculate()
{
        var srednia_krajowa = 2234.80
        var m = parseFloat(document.forms['opcalc'].cn.value)
        var tpod = parseFloat(0.02*m).toFixed(2) //podatek od czynnosci cywilno-prawnych
        var tvat //vat od operacji
        var ttn = 0 //taksa notarialna
        var tops //oplata sadowa
        var tprow //prowizja
        var tprowv //VAT od prowizji
        var topd //oplaty dodatkowe
        var tsuma


        if(m > 0 && m < 3000)
                ttn = 100
        if(m >= 3000 && m < 10000)
                ttn = 100 + 0.03*(m - 3000)
        if(m >=10000 && m < 30000)
                ttn = 310 + 0.02*(m - 10000)
        if(m >= 30000 && m < 60000)
                ttn = 710 + 0.01*(m - 30000)
        if(m >= 60000 && m <1000000)
                ttn = 1010 + 0.004*(m - 60000)
        if(m >= 1000000 && m <2000000)
                ttn = 4770 + 0.002*(m - 1000000)
        if(m >= 2000000)
                ttn = 6770 + 0.0025*(m - 2000000)


           ttn = ttn/sp_w
     if(ttn>10000) ttn=10000
        //oplata sadowa

        tops=0

        if(sp_w==2 && !wpis)
        {
                tops = 0
                }
        else
        {
                tops = 200
  }
        //debug.innerHTML = wpis

        tvat = parseFloat(0.23 * ttn).toFixed(2)
        tprow = (parseFloat(document.forms['opcalc'].prow.value)/100) * m
        tprowv = parseFloat(0.23 * (tprow)).toFixed(2)
        topd = parseFloat(parseFloat(tpod) + parseFloat(ttn) + parseFloat(tops) + parseFloat(tprow) + parseFloat(tvat) + parseFloat(tprowv))
        tsuma = parseFloat(m + topd)


        tops = parseFloat(tops).toFixed(2)
        ttn = parseFloat(ttn).toFixed(2)
        tprow = parseFloat(tprow).toFixed(2)
        tprowv = parseFloat(tprowv).toFixed(2)
        topd = parseFloat(topd).toFixed(2)
        tsuma = parseFloat(tsuma).toFixed(2)


        document.forms['opcalc'].pod.value = parseFloat(tpod).format()
        document.forms['opcalc'].ops.value =  parseFloat(tops).format()
        document.forms['opcalc'].tn.value = parseFloat(ttn).format()
        document.forms['opcalc'].vat.value = parseFloat(tvat).format()
        document.forms['opcalc'].cprow.value = parseFloat(tprow).format()
        document.forms['opcalc'].cprowv.value = parseFloat(tprowv).format()
        document.forms['opcalc'].opd.value = parseFloat(topd).format()
        document.forms['opcalc'].suma.value = parseFloat(tsuma).format()

}

function check()
{
        var m = parseInt(document.forms['opcalc'].cn.value)
        if(isNaN(m))
                document.forms['opcalc'].cn.value = 0
        else
                document.forms['opcalc'].cn.value = m
        calculate()
}

function check3()
{
    document.forms['opcalc'].prow.value = document.forms['opcalc'].prow.value.replace(',','.')
    if(document.forms['opcalc'].prow.value.substr(-1,1)=='.') return false;
    if(document.forms['opcalc'].prow.value!='0')
        var p = parseFloat(document.forms['opcalc'].prow.value)
    if(isNaN(p))
        document.forms['opcalc'].prow.value = 0
    else
        document.forms['opcalc'].prow.value = p
    calculate()
}

function check2()
{
        setTimeout(function(){check3()},5000);
}
function chops()
{
        if(wpis)
                wpis = false;
        else
                wpis = true;
        calculate();
}


