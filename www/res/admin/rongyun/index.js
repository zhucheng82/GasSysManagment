var service = angular.module("service", ["RongWebIMWidget"]);


service.controller("main", ["$scope", "WebIMWidget", function($scope,
  WebIMWidget) {

  $scope.show = function() {
      WebIMWidget.show();
      console.log("已打开");
  }

  $scope.hidden = function() {
      WebIMWidget.hidden();
  }

  $scope.server = WebIMWidget;
  $scope.targetType=1;

  $scope.setconversation=function(){
      
      WebIMWidget.setConversation(Number($scope.targetType), $scope.targetId, "聊天中:"+$scope.targetId);
  }
  
  var token = '7ZVeMA+kVG4sD4GRXos6tAR6JTQ9KJ2dCwOEaMSBEhkKKbZ92R8/1qxRVaQeHi1ci6gG7aAWb2LleWdW9RKr4A==';//自己的融云token

  angular.element(document).ready(function() {

    WebIMWidget.init({
        appkey: "k51hidwq1yt4b",//正式融云appkey
        //token: "/VG0+UZ9qs2Hey3Yww1EJWWDa4wRYIO8B25VVOTDPjBdM9bYUmD0hBKn2tkik5O8QLU9XM54Djg=",
        token:token,
        style:{
              width:1000,
              height:500,
              positionFixed:true,
              right:0,
              top:50,
        },
        displayConversationList:true,
        conversationListPosition:WebIMWidget.EnumConversationListPosition.right,
          
        onSuccess:function(){
          //alert(token);
          //alert('初始化完成');
        },

        onError:function(error){
            console.log("error:"+error);
        }
    });

    WebIMWidget.show();

    WebIMWidget.setUserInfoProvider(function(targetId,obj){

        obj.onSuccess({name:"用户"+targetId});
    });

    /*WebIMWidget.onCloseBefore=function(obj){
        console.log("关闭前");
        setTimeout(function(){
          obj.close();
        },1000)
    }*/

    WebIMWidget.onClose=function(){
        console.log("已关闭");
    }

    //接收到消息时
    WebIMWidget.onReceivedMessage = function(message) {
        //console.log("收到信息");
    }

    //WebIMWidget.show();
    //设置会话
    //WebIMWidget.setConversation("1", "1", "123");


  });

}]);

