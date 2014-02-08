Chat.app.util = {
    timeStampToDate : function(timestamp){
        var date = new Date(timestamp*1000);
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var seconds = date.getSeconds();
        return	{hours : date.getHours(), minutes : date.getMinutes(), seconds : date.getSeconds()};
    },
};