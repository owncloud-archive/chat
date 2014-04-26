Chat.app.util = {
    timeStampToDate : function(timestamp){
        var date = new Date(timestamp*1000);
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var seconds = date.getSeconds();
        return	{hours : date.getHours(), minutes : date.getMinutes(), seconds : date.getSeconds()};
    },
    countObjects : function(objects){
    	var count = 0;
  		
    	for (var k in object) {
            if (object.hasOwnProperty(k)) {
                ++count;
            }
        }
        return count;
    },
    updateContacts : function(){
        $.post('/index.php' + OC.linkTo("chat", "contacts")).done(function(contacts){
            console.log('request done');
            Chat.scope.$apply(function(){
               Chat.scope.contacts = contacts; 
               console.log(Chat.scope.contacts);
            });
        });
    },
    isYoutubeUrl : function(url) {
    	if(	url.indexOf('https://youtube.com') > -1 
    		|| url.indexOf('https://youtube.com') > -1 
			|| url.indexOf('https://www.youtube.com') > -1 
			|| url.indexOf('http://www.youtube.com') > -1 
			|| url.indexOf('http://youtu.be') > -1
    		){
    		return true;
    	} else {
    		return false;
    	}
    },
    isImageUrl : function(url){
    	var imgRegex = /((?:https?):\/\/\S*\.(?:gif|jpg|jpeg|tiff|png|svg|webp))/gi;
    	return imgRegex.test(url);
    	
    }
};