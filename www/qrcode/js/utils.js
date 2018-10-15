(function () {
    window.qixiang = {};
    window.getDeviceInfoCallBack = function (result) {
        //alert(result);
        var obj = JSON.parse(result);
        window.deviceInfo = obj;
        if (obj.terminal == "android" || obj.terminal == "android") {

        }
    }
    $.fn.numeral = function (bl) {//限制金额输入、兼容浏览器、屏蔽粘贴拖拽等
        $(this).keypress(function (e) {
            var keyCode = e.keyCode ? e.keyCode : e.which;
            if (bl) {//浮点数
                if ((this.value.length == 0 || this.value.indexOf(".") != -1) && keyCode == 46) return false;
                return keyCode >= 48 && keyCode <= 57 || keyCode == 46 || keyCode == 8;
            } else {//整数
                return keyCode >= 48 && keyCode <= 57 || keyCode == 8;
            }
        });
        $(this).bind("copy cut paste", function (e) { // 通过空格连续添加复制、剪切、粘贴事件
            if (window.clipboardData)//clipboardData.setData('text', clipboardData.getData('text').replace(/\D/g, ''));
                return !clipboardData.getData('text').match(/\D/);
            else
                event.preventDefault();
        });
        $(this).bind("dragenter", function () {
            return false;
        });
        $(this).css("ime-mode", "disabled");
        $(this).bind("focus", function () {
            if (this.value.lastIndexOf(".") == (this.value.length - 1)) {
                this.value = this.value.substr(0, this.value.length - 1);
            } else if (isNaN(this.value)) {
                this.value = "";
            }
        });
        this.bind("keyup", function () {
            if (/(^0+)/.test(this.value)) {
                this.value = this.value.replace(/^0*/, '');
            }
        });
        this.bind("blur", function () {
            if (this.value.lastIndexOf(".") == (this.value.length - 1)) {
                this.value = this.value.substr(0, this.value.length - 1);
            } else if (isNaN(this.value)) {
                this.value = "";
            }
        });
    }
    window.qixiang.config = {
        SiteUrl: "http://" + window.location.host,
        ApiUrl: "http://" + window.location.host + "/api/",
        pagesize: 5,
        pagesize2: 10,
        WapSiteUrl: "http://" + window.location.host + "/wap",
        HomePage: "http://" + window.location.host + "/wap",
    };
    window.qixiang.utils = function () {
        var self = this;
        var debug = 1

        this.webBackHandler = function (url, boo) {
            if (url && boo) {
                var ss = "?";
                if (url.indexOf(ss) > 0) {
                    ss = "&"
                }

                if (self.getTokenFromUrl()) {
                    url = url + ss + 'token=' + self.getTokenFromUrl();
                }
                location.href = url;
                return;
            }
            if (!window.deviceInfo) {
                if (url) {
                    window.location.href = url;
                } else {
                    window.history.go(-1);
                }

            } else {
                window.location.href = 'js://closePage/123/';
            }
        }
        this.DropLoad = function (opts) {
            var deep = 100;
            var page = 1;
            var totalpage = 1;
            var before = true;
            var lists = opts.list;
            var init = false;
            this.setBefore = function (b) {
                before = b;
                if (dropload) {
                    dropload.isData = true;
                }

                $(opts.container).scrollTop(0);
            };
            this.setPage = function (p, tp) {
                page = p;
                totalpage = tp;
                if (page >= totalpage) {
                    dropload.noData();
                }
            };
            var dropload = $(opts.container).dropload({

                loadUpFn: function (me) {
                    before = true;
                    me.isData = true;
                    //dropload.lock();
                    //setTimeout(function(){
                    //    dropload.unlock();
                    opts.dropHandler(before)
                    //},500);
                },
                loadDownFn: function (me) {
                    if (!init) {
                        me.noData();
                        return;
                    }
                    before = false;
                    if (page >= totalpage) {
                        //me.lock();
                        //// 无数据
                        dropload.noData();
                        return;
                    }
                    dropload.lock();

                    setTimeout(function () {
                        dropload.unlock();
                        opts.dropHandler(before)
                    }, 500);

                },
                distance: deep,
                uplock: opts.uplock,
            });
            init = true;
            this.lock = function () {
                dropload.lock();
            }

            this.unlock = function () {
                dropload.unlock();

            }
            this.resetListContent = function (str) {
                if (before) {
                    page = 1;
                    //mySwiper.removeAllSlides();
                    $(lists).html('');
                    $(lists).html(str);

                } else {
                    $(lists).append(str);
                }
                dropload.resetload();

            };

            this.errorResetload = function () {
                dropload.resetload();
            };

            return this;
        };

        this.sendtestPostData = function (obj, url, fun, errfun) {
            var time = new Date().getTime();
            var sign = get_sign_str(obj, time, isNeedToken(url))
            obj.sign = sign;
            obj.timestamp = time;
            if (!errfun) {
                errfun = self.errorHnadler;
            }
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'text',
                data: obj,
                success: function (result) {
                    try {
                        var res = JSON.parse(result);
                        if (res.code == 1) {

                        } else {
                            self.tipsAlert(res.msg);
                        }
                    } catch (e) {
                        $('body').html(result);
                    }

                },
                error: errfun,
            });
        }
        this.sendPostData = function (obj, url, fun, errfun, loading) {
            //是否需要加载中；
            if (loading) {
                //if (!loadingInit) {
                //    initLoading();
                //}
                //$('#loading_inmation').show();
            }

            var time = new Date().getTime();
            var sign = get_sign_str(obj, time, isNeedToken(url))
            obj.sign = sign;
            obj.timestamp = time;
            if (!errfun) {
                errfun = self.errorHnadler;
            }
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                data: obj,
                success: function (res) {
                    $('#loading_inmation').hide();
                    if (res.code == -3 && res.msg.indexOf('签名错误') >= 0) {
                        //showUnkownError();
                        //return;
                    }
                    if ((res.code == -1 && res.msg == '请先登录') || res.code == -1000) {
                        var shop_id = self.getUrlParam('shop_id');
                        self.replaceState('/wap/page/index.html')
                        location.href = '/wap/login/login.html';
                        return;
                    }
                    if (res.code != 1) {
                        self.tipsAlert(res.msg);
                    }
                    if(res.code==-10086||res.code==-10010||res.code==-1000||res.code==-1001){
                        self.tipsAlert(res.msg);
                        setTimeout(function () {
                            location.href = '/wap/login/login.html';
                        }, 600);

                        return;
                    }

                    fun(res);


                },
                error: function (res) {
                    $('#loading_inmation').hide();
                    errfun(res);
                },
            });
            delete obj.sign;
            delete obj.timestamp;
            delete obj.token;
        };
        var loading_content = '<div style="position: absolute;width: 100%;height: 100%;z-index: 10000;background: rgba(0,0,0,0.1);" id="loading_inmation">'
            + '<img src="/wap/images/loading.gif" style="width: 80%;left:10%;margin-top:50%;position: relative;"/>'
            + '</div>';
        var loadingInit = false;

        function initLoading() {
            if (loadingInit) {
                return;
            }
            loadingInit = true;
            $('body').append(loading_content);
        }

        function get_sign_str(json_obj, time, need_token) {
            var str = "";
            var keys = [];
            if (need_token) {
                var tok = self.get_user_token();
                if(tok){
                    json_obj.token =tok;
                }

            }

            for (var key in json_obj) {
                keys.push(key);
                json_obj[key] = self.xssCheck(json_obj[key]);
            }
            keys.sort();
            $.each(keys, function (index, value) {
                var nn = json_obj[value]
                str += value + "=" + nn + "&";
            });

            str += "appkey=mahjongqx$#&timestamp=" + time;

            str = encodeURIComponent(str);
            str = hex_md5(str);
            //console.log(str);
            //alert(str);
            return str;
        }

        this.get_user_token = function () {

            if (self.getTokenFromUrl()) {
                return self.getTokenFromUrl();
            }
            var info = self.get_user_Info();
            if (info) {
                return info.token;
            }
            return '';
        }

        this.getTokenFromUrl = function(){
            return self.getUrlParam('token');

        }

        this.getUrlParam = function (name, url) {
            //构造一个含有目标参数的正则表达式对象
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
            //匹配目标参数
            if (!url) {
                url = window.location.search;
            }
            url = decodeURI(url);
            var r = url.substr(1).match(reg);
            //返回参数值
            if (r != null) return unescape(r[2]);
            return null;
        }

        this.getIPAddr = function () {
            return IPAdd;
        }

        var IPAdd = "";
        this.getIP = function () {

            $.ajax({
                url: "http://ip.chinaz.com/getip.aspx",
                type: 'get',
                dataType: 'jsonp',
                success: function (data) {
                    IPAdd = data.ip;
                },
            });
        }

        this.save_string_tolocal = function (key, value) {
            if (window.localStorage) {
                localStorage.setItem(key, value);
            }
        }

        this.get_string_fromlocal = function (key) {
            if (window.localStorage) {
                return localStorage.getItem(key);
            }


        }

        this.get_user_Info = function () {
            return self.get_json_fromlocal('userInfo');

        }

        this.save_json_tolocal = function (key, value) {
            if (window.localStorage) {
                var str = '';
                try {
                    str = self.xssCheckJson(JSON.stringify(value));
                    JSON.parse(str);
                } catch (e) {
                    str = JSON.stringify(value);
                }
                localStorage.setItem(key, str);
                return 1;
            }
            return 0;//保存数据失败
        }

        this.get_json_fromlocal = function (key) {
            if (window.localStorage) {
                var json = localStorage.getItem(key);
                if (json) {
                    var obj;
                    try {
                        obj = JSON.parse(json)
                    } catch (e) {

                    }
                    return obj;
                }

            }
            return "";//未找到数据
        }

        this.testMobile = function (str) {
            return /^1[3|4|5|7|8]\d{9}$/.test(str)
        }
        this.md5 = function (str) {
            return hex_md5(str);
        }
        ///2016.6.2
        this.get_total_page = function (count, pagesize) {
            var total = parseInt((parseInt(count) + parseInt(pagesize) - 1) / parseInt(pagesize));
            return total;
        }
        this.errorHnadler = function (XMLHttpRequest, textStatus, errorThrown) {
            //alert(XMLHttpRequest)
            if (debug) {
                $('body').html(XMLHttpRequest.responseText)
            } else {
                if (XMLHttpRequest.status == 200) {
                    self.tipsAlert('好像哪里不对。。。')
                } else {

                }
            }

        }

        function ajaxFileUpload(url, img, file,succfun) {
            var time = new Date().getTime();
            var obj = {};
            var sign = get_sign_str(obj, time, isNeedToken(url))
            obj.sign = sign;
            obj.timestamp = time;
            $.ajaxFileUpload
            (
                {
                    url: url, //用于文件上传的服务器端请求地址
                    secureuri: true, //是否需要安全协议，一般设置为false
                    fileElementId: file, //文件上传域的ID
                    dataType: 'json', //返回值类型 一般设置为json
                    data: obj,
                    type: 'post',
                    success: function (data, status)  //服务器成功响应处理函数
                    {
                        $('#' + img).attr("src", data.data.url);
                        if (typeof (data.error) != 'undefined') {
                            if (data.error != '') {
                                self.tipsAlert(data.error);
                            } else {
                                self.tipsAlert(data.msg);
                            }
                        }
                        if(succfun){
                            succfun(data);
                        }

                    },
                    error: function (data, status, e)//服务器响应失败处理函数
                    {
                        self.tipsAlert(e);
                    }
                }
            )
            return false;
        }

        this.previewImage = function(file, imageid, fileid,url,size,succfun,efffun) {
            //var MAXWIDTH  = 100;
            //var MAXHEIGHT = 100;
            //var div = document.getElementById('preview');
            if(!size){
                size = 1024*1024
            }
            if (file.files && file.files[0]) {
                //var img = document.getElementById(imageid);
                //
                if(file.files[0].size > size){
                    self.tipsAlert('图片尺寸超过规定限制!')
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (evt) {
                    ajaxFileUpload(url, imageid, fileid,succfun)
                    //$(imageid).attr('src',evt.target.result)
                };
                reader.readAsDataURL(file.files[0]);
            }
            else {
                //var sFilter='filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="';
                //file.select();
                //var src = document.selection.createRange().text;
                //var img = document.getElementById('imghead');
                //img.filters.item('DXImageTransform.Microsoft.AlphaImageLoader').src = src;
                //var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
                //status =('rect:'+rect.top+','+rect.left+','+rect.width+','+rect.height);
                //div.innerHTML = "<div id=divhead style='width:"+rect.width+"px;height:"+rect.height+"px;margin-top:"+rect.top+"px;margin-left:"+rect.left+"px;"+sFilter+src+"\"'></div>";
            }
        }

        this.isWeiXin = function () {
            var ua = window.navigator.userAgent.toLowerCase();
            if (ua.match(/MicroMessenger/i) == 'micromessenger') {
                return true;
            } else {
                return false;
            }
        }
        //替换历史当前页
        this.replaceState = function (url){
            window.history.replaceState({},'',url)
        }

        this.tipsAlert = function (str) {
            var div = document.createElement('div');
            div.innerHTML = '<div class="deploy_ctype_tip"><p>' + str + '</p></div>';
            var tipNode = div.firstChild;
            $("body").after(tipNode);

            $(tipNode).css('margin-top',-$(tipNode).height()/2);
            setTimeout(function () {
                $(tipNode).remove();
            }, 3000);
        }
        this.xssCheck = function (str, reg) {
            if (typeof str != 'string') {
                return str;
            }
            str = str.replace(/\s+/g, "");
            return str;
        }

        this.xssCheckJson = function (str, reg) {
            if (typeof str != 'string') {
                return str;
            }
            return str ? str.replace(reg || /[<>](?:(amp|lt|quot|gt|#39|nbsp|#\d+);)?/g, function (a, b) {
                if (b) {
                    return a;
                } else {
                    return {
                        '<': '&lt;',
                        '>': '&gt;',
                    }[a]
                }
            }) : '';
        }

        function isNeedToken(url) {
            if (url.indexOf('api/home') >= 0) {
                return false;
            } else {
                return true;
            }
        }

        this.removeAnimateClass = function (element, dura, classname, hide) {
            setTimeout(function () {
                $(element).removeClass(classname);
                if (hide) {
                    $(element).hide();
                }

            }, dura);
        }

        this.getCookie = function(name)
        {
            var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
            if(arr=document.cookie.match(reg))
                return unescape(arr[2]);
            else
                return null;
        }

        this.setCookie = function(name,value,day){
            var _date = new Date();
            _date.setDate(_date.getDate()+day);
            var str =  name+"="+decodeURI(value)+";expires="+_date.toGMTString()+';path=/';
            document.cookie =str;
        }




    };


    /*
     *
     * yyyy-MM-dd hh:mm:ss
     * */
    Date.prototype.Format = function (fmt) { //author: meizz
        var o = {
            "M+": this.getMonth() + 1, //月份
            "d+": this.getDate(), //日
            "h+": this.getHours(), //小时
            "m+": this.getMinutes(), //分
            "s+": this.getSeconds(), //秒
            "q+": Math.floor((this.getMonth() + 3) / 3), //季度
            "S": this.getMilliseconds() //毫秒
        };
        if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        for (var k in o)
            if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        return fmt;
    }

    /*
     * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
     * Digest Algorithm, as defined in RFC 1321.
     * Version 2.1 Copyright (C) Paul Johnston 1999 - 2002.
     * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
     * Distributed under the BSD License
     * See http://pajhome.org.uk/crypt/md5 for more info.
     */

    /*
     * Configurable variables. You may need to tweak these to be compatible with
     * the server-side, but the defaults work in most cases.
     */
    var hexcase = 0;
    /* hex output format. 0 - lowercase; 1 - uppercase        */
    var b64pad = "";
    /* base-64 pad character. "=" for strict RFC compliance   */
    var chrsz = 8;
    /* bits per input character. 8 - ASCII; 16 - Unicode      */

    /*
     * These are the functions you'll usually want to call
     * They take string arguments and return either hex or base-64 encoded strings
     */
    function hex_md5(s) {
        return binl2hex(core_md5(str2binl(s), s.length * chrsz));
    }

    function b64_md5(s) {
        return binl2b64(core_md5(str2binl(s), s.length * chrsz));
    }

    function str_md5(s) {
        return binl2str(core_md5(str2binl(s), s.length * chrsz));
    }

    function hex_hmac_md5(key, data) {
        return binl2hex(core_hmac_md5(key, data));
    }

    function b64_hmac_md5(key, data) {
        return binl2b64(core_hmac_md5(key, data));
    }

    function str_hmac_md5(key, data) {
        return binl2str(core_hmac_md5(key, data));
    }

    /*
     * Perform a simple self-test to see if the VM is working
     */
    function md5_vm_test() {
        return hex_md5("abc") == "900150983cd24fb0d6963f7d28e17f72";
    }

    /*
     * Calculate the MD5 of an array of little-endian words, and a bit length
     */
    function core_md5(x, len) {
        /* append padding */
        x[len >> 5] |= 0x80 << ((len) % 32);
        x[(((len + 64) >>> 9) << 4) + 14] = len;

        var a = 1732584193;
        var b = -271733879;
        var c = -1732584194;
        var d = 271733878;

        for (var i = 0; i < x.length; i += 16) {
            var olda = a;
            var oldb = b;
            var oldc = c;
            var oldd = d;

            a = md5_ff(a, b, c, d, x[i + 0], 7, -680876936);
            d = md5_ff(d, a, b, c, x[i + 1], 12, -389564586);
            c = md5_ff(c, d, a, b, x[i + 2], 17, 606105819);
            b = md5_ff(b, c, d, a, x[i + 3], 22, -1044525330);
            a = md5_ff(a, b, c, d, x[i + 4], 7, -176418897);
            d = md5_ff(d, a, b, c, x[i + 5], 12, 1200080426);
            c = md5_ff(c, d, a, b, x[i + 6], 17, -1473231341);
            b = md5_ff(b, c, d, a, x[i + 7], 22, -45705983);
            a = md5_ff(a, b, c, d, x[i + 8], 7, 1770035416);
            d = md5_ff(d, a, b, c, x[i + 9], 12, -1958414417);
            c = md5_ff(c, d, a, b, x[i + 10], 17, -42063);
            b = md5_ff(b, c, d, a, x[i + 11], 22, -1990404162);
            a = md5_ff(a, b, c, d, x[i + 12], 7, 1804603682);
            d = md5_ff(d, a, b, c, x[i + 13], 12, -40341101);
            c = md5_ff(c, d, a, b, x[i + 14], 17, -1502002290);
            b = md5_ff(b, c, d, a, x[i + 15], 22, 1236535329);

            a = md5_gg(a, b, c, d, x[i + 1], 5, -165796510);
            d = md5_gg(d, a, b, c, x[i + 6], 9, -1069501632);
            c = md5_gg(c, d, a, b, x[i + 11], 14, 643717713);
            b = md5_gg(b, c, d, a, x[i + 0], 20, -373897302);
            a = md5_gg(a, b, c, d, x[i + 5], 5, -701558691);
            d = md5_gg(d, a, b, c, x[i + 10], 9, 38016083);
            c = md5_gg(c, d, a, b, x[i + 15], 14, -660478335);
            b = md5_gg(b, c, d, a, x[i + 4], 20, -405537848);
            a = md5_gg(a, b, c, d, x[i + 9], 5, 568446438);
            d = md5_gg(d, a, b, c, x[i + 14], 9, -1019803690);
            c = md5_gg(c, d, a, b, x[i + 3], 14, -187363961);
            b = md5_gg(b, c, d, a, x[i + 8], 20, 1163531501);
            a = md5_gg(a, b, c, d, x[i + 13], 5, -1444681467);
            d = md5_gg(d, a, b, c, x[i + 2], 9, -51403784);
            c = md5_gg(c, d, a, b, x[i + 7], 14, 1735328473);
            b = md5_gg(b, c, d, a, x[i + 12], 20, -1926607734);

            a = md5_hh(a, b, c, d, x[i + 5], 4, -378558);
            d = md5_hh(d, a, b, c, x[i + 8], 11, -2022574463);
            c = md5_hh(c, d, a, b, x[i + 11], 16, 1839030562);
            b = md5_hh(b, c, d, a, x[i + 14], 23, -35309556);
            a = md5_hh(a, b, c, d, x[i + 1], 4, -1530992060);
            d = md5_hh(d, a, b, c, x[i + 4], 11, 1272893353);
            c = md5_hh(c, d, a, b, x[i + 7], 16, -155497632);
            b = md5_hh(b, c, d, a, x[i + 10], 23, -1094730640);
            a = md5_hh(a, b, c, d, x[i + 13], 4, 681279174);
            d = md5_hh(d, a, b, c, x[i + 0], 11, -358537222);
            c = md5_hh(c, d, a, b, x[i + 3], 16, -722521979);
            b = md5_hh(b, c, d, a, x[i + 6], 23, 76029189);
            a = md5_hh(a, b, c, d, x[i + 9], 4, -640364487);
            d = md5_hh(d, a, b, c, x[i + 12], 11, -421815835);
            c = md5_hh(c, d, a, b, x[i + 15], 16, 530742520);
            b = md5_hh(b, c, d, a, x[i + 2], 23, -995338651);

            a = md5_ii(a, b, c, d, x[i + 0], 6, -198630844);
            d = md5_ii(d, a, b, c, x[i + 7], 10, 1126891415);
            c = md5_ii(c, d, a, b, x[i + 14], 15, -1416354905);
            b = md5_ii(b, c, d, a, x[i + 5], 21, -57434055);
            a = md5_ii(a, b, c, d, x[i + 12], 6, 1700485571);
            d = md5_ii(d, a, b, c, x[i + 3], 10, -1894986606);
            c = md5_ii(c, d, a, b, x[i + 10], 15, -1051523);
            b = md5_ii(b, c, d, a, x[i + 1], 21, -2054922799);
            a = md5_ii(a, b, c, d, x[i + 8], 6, 1873313359);
            d = md5_ii(d, a, b, c, x[i + 15], 10, -30611744);
            c = md5_ii(c, d, a, b, x[i + 6], 15, -1560198380);
            b = md5_ii(b, c, d, a, x[i + 13], 21, 1309151649);
            a = md5_ii(a, b, c, d, x[i + 4], 6, -145523070);
            d = md5_ii(d, a, b, c, x[i + 11], 10, -1120210379);
            c = md5_ii(c, d, a, b, x[i + 2], 15, 718787259);
            b = md5_ii(b, c, d, a, x[i + 9], 21, -343485551);

            a = safe_add(a, olda);
            b = safe_add(b, oldb);
            c = safe_add(c, oldc);
            d = safe_add(d, oldd);
        }
        return Array(a, b, c, d);

    }

    /*
     * These functions implement the four basic operations the algorithm uses.
     */
    function md5_cmn(q, a, b, x, s, t) {
        return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s), b);
    }

    function md5_ff(a, b, c, d, x, s, t) {
        return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
    }

    function md5_gg(a, b, c, d, x, s, t) {
        return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
    }

    function md5_hh(a, b, c, d, x, s, t) {
        return md5_cmn(b ^ c ^ d, a, b, x, s, t);
    }

    function md5_ii(a, b, c, d, x, s, t) {
        return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
    }

    /*
     * Calculate the HMAC-MD5, of a key and some data
     */
    function core_hmac_md5(key, data) {
        var bkey = str2binl(key);
        if (bkey.length > 16) bkey = core_md5(bkey, key.length * chrsz);

        var ipad = Array(16), opad = Array(16);
        for (var i = 0; i < 16; i++) {
            ipad[i] = bkey[i] ^ 0x36363636;
            opad[i] = bkey[i] ^ 0x5C5C5C5C;
        }

        var hash = core_md5(ipad.concat(str2binl(data)), 512 + data.length * chrsz);
        return core_md5(opad.concat(hash), 512 + 128);
    }

    /*
     * Add integers, wrapping at 2^32. This uses 16-bit operations internally
     * to work around bugs in some JS interpreters.
     */
    function safe_add(x, y) {
        var lsw = (x & 0xFFFF) + (y & 0xFFFF);
        var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
        return (msw << 16) | (lsw & 0xFFFF);
    }

    /*
     * Bitwise rotate a 32-bit number to the left.
     */
    function bit_rol(num, cnt) {
        return (num << cnt) | (num >>> (32 - cnt));
    }

    /*
     * Convert a string to an array of little-endian words
     * If chrsz is ASCII, characters >255 have their hi-byte silently ignored.
     */
    function str2binl(str) {
        var bin = Array();
        var mask = (1 << chrsz) - 1;
        for (var i = 0; i < str.length * chrsz; i += chrsz)
            bin[i >> 5] |= (str.charCodeAt(i / chrsz) & mask) << (i % 32);
        return bin;
    }

    /*
     * Convert an array of little-endian words to a string
     */
    function binl2str(bin) {
        var str = "";
        var mask = (1 << chrsz) - 1;
        for (var i = 0; i < bin.length * 32; i += chrsz)
            str += String.fromCharCode((bin[i >> 5] >>> (i % 32)) & mask);
        return str;
    }

    /*
     * Convert an array of little-endian words to a hex string.
     */
    function binl2hex(binarray) {
        var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
        var str = "";
        for (var i = 0; i < binarray.length * 4; i++) {
            str += hex_tab.charAt((binarray[i >> 2] >> ((i % 4) * 8 + 4)) & 0xF) +
                hex_tab.charAt((binarray[i >> 2] >> ((i % 4) * 8  )) & 0xF);
        }
        return str;
    }

    /*
     * Convert an array of little-endian words to a base-64 string
     */
    function binl2b64(binarray) {
        var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
        var str = "";
        for (var i = 0; i < binarray.length * 4; i += 3) {
            var triplet = (((binarray[i >> 2] >> 8 * ( i % 4)) & 0xFF) << 16)
                | (((binarray[i + 1 >> 2] >> 8 * ((i + 1) % 4)) & 0xFF) << 8 )
                | ((binarray[i + 2 >> 2] >> 8 * ((i + 2) % 4)) & 0xFF);
            for (var j = 0; j < 4; j++) {
                if (i * 8 + j * 6 > binarray.length * 32) str += b64pad;
                else str += tab.charAt((triplet >> 6 * (3 - j)) & 0x3F);
            }
        }
        return str;
    }

})();
