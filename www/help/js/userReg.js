//获取验证码
$(function(){
	var CFG = window.qixiang.config;
    var UTL = new window.qixiang.utils;
	$("#get_code").click(function(){
		var usermobile = $("#mobile").val();
		if (!usermobile) {
			showTip('手机号不能为空');
			return false;
		}
		var patrn = /^1\d{10}$/;
		if (!patrn.exec($.trim(usermobile))) {
			showTip('手机号格式错误');
			return false;
		}else{
			$("#get_code").hide();
			$("#showTime").show();
			var data = {
				mobile:usermobile,
				type_id:1,
				platform_id:5,
				user_type:2,
			};
			UTL.sendPostData(data, CFG.ApiUrl + 'public/sms/send', function (result) {
	            if (result.code == 1) {
	                $("#get_code").hide();
	                $("#showTime").show();
	                showTip('验证码已发送到手机');
	                settime(this);//倒计时
	            } else {
	                //showTip(result.msg);
	                $("#get_code").show();
	                $("#showTime").hide();
					return false;
	            }
	        });
		}
	})
})

var countdown=60; 
function settime(val) {
	if (countdown == 0) {
		$("#get_code").show();
		$("#showTime").hide();
		countdown = 60; 
	}else{ 
		$("#get_code").hide();
		$("#showTime").show(); 
		$("#showTime").html("(" + countdown + ")秒可重发"); 
		countdown--;
		setTimeout(function() { 
			settime(val) 
		},1000) 
	}
} 

//判断用户设备类型（1安卓，2ios）
function getUserAgent() {
	var Agent = 1;
	if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {
		Agent = 2;
	} else if (/(Android)/i.test(navigator.userAgent)) {
		Agent = 1;
	}
	return Agent;
}
	