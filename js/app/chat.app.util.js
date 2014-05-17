Chat.app.util = {
    timeStampToDate : function(timestamp){
        var date = new Date(timestamp*1000);
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var seconds = date.getSeconds();
        return	{hours : date.getHours(), minutes : date.getMinutes(), seconds : date.getSeconds()};
    },
    updateContacts : function(){
        $.post('/index.php' + OC.linkTo("chat", "contacts")).done(function(contacts){
            Chat.scope.$apply(function(){
               Chat.scope.contacts = contacts; 
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
    	
    },
    array_diff : function(a1, a2){
      var a=[], diff=[];
      for(var i=0;i<a1.length;i++)
        a[a1[i]]=true;
      for(var i=0;i<a2.length;i++)
        if(a[a2[i]]) delete a[a2[i]];
        else a[a2[i]]=true;
      for(var k in a)
        diff.push(k);
      return diff;
    },
    emojis : [
{ "name" : ":+1:", "url" : "/apps/chat/img/emoji/+1.png" } ,
{ "name" : ":-1:", "url" : "/apps/chat/img/emoji/-1.png" } ,
{ "name" : ":alien:", "url" : "/apps/chat/img/emoji/alien.png" } ,
{ "name" : ":angry:", "url" : "/apps/chat/img/emoji/angry.png" } ,
{ "name" : ":anguished:", "url" : "/apps/chat/img/emoji/anguished.png" } ,
{ "name" : ":astonished:", "url" : "/apps/chat/img/emoji/astonished.png" } ,
{ "name" : ":blush:", "url" : "/apps/chat/img/emoji/blush.png" } ,
{ "name" : ":bowtie:", "url" : "/apps/chat/img/emoji/bowtie.png" } ,
{ "name" : ":clap:", "url" : "/apps/chat/img/emoji/clap.png" } ,
{ "name" : ":cold_sweat:", "url" : "/apps/chat/img/emoji/cold_sweat.png" } ,
{ "name" : ":confounded:", "url" : "/apps/chat/img/emoji/confounded.png" } ,
{ "name" : ":confused:", "url" : "/apps/chat/img/emoji/confused.png" } ,
{ "name" : ":cry:", "url" : "/apps/chat/img/emoji/cry.png" } ,
{ "name" : ":disappointed:", "url" : "/apps/chat/img/emoji/disappointed.png" } ,
{ "name" : ":disappointed_relieved:", "url" : "/apps/chat/img/emoji/disappointed_relieved.png" } ,
{ "name" : ":dizzy_face:", "url" : "/apps/chat/img/emoji/dizzy_face.png" } ,
{ "name" : ":expressionless:", "url" : "/apps/chat/img/emoji/expressionless.png" } ,
{ "name" : ":facepunch:", "url" : "/apps/chat/img/emoji/facepunch.png" } ,
{ "name" : ":fearful:", "url" : "/apps/chat/img/emoji/fearful.png" } ,
{ "name" : ":fist:", "url" : "/apps/chat/img/emoji/fist.png" } ,
{ "name" : ":flushed:", "url" : "/apps/chat/img/emoji/flushed.png" } ,
{ "name" : ":frowning:", "url" : "/apps/chat/img/emoji/frowning.png" } ,
{ "name" : ":grimacing:", "url" : "/apps/chat/img/emoji/grimacing.png" } ,
{ "name" : ":grin:", "url" : "/apps/chat/img/emoji/grin.png" } ,
{ "name" : ":grinning:", "url" : "/apps/chat/img/emoji/grinning.png" } ,
{ "name" : ":hand:", "url" : "/apps/chat/img/emoji/hand.png" } ,
{ "name" : ":heart_eyes:", "url" : "/apps/chat/img/emoji/heart_eyes.png" } ,
{ "name" : ":hushed:", "url" : "/apps/chat/img/emoji/hushed.png" } ,
{ "name" : ":imp:", "url" : "/apps/chat/img/emoji/imp.png" } ,
{ "name" : ":innocent:", "url" : "/apps/chat/img/emoji/innocent.png" } ,
{ "name" : ":joy:", "url" : "/apps/chat/img/emoji/joy.png" } ,
{ "name" : ":kissing:", "url" : "/apps/chat/img/emoji/kissing.png" } ,
{ "name" : ":kissing_closed_eyes:", "url" : "/apps/chat/img/emoji/kissing_closed_eyes.png" } ,
{ "name" : ":kissing_heart:", "url" : "/apps/chat/img/emoji/kissing_heart.png" } ,
{ "name" : ":kissing_smiling_eyes:", "url" : "/apps/chat/img/emoji/kissing_smiling_eyes.png" } ,
{ "name" : ":laughing:", "url" : "/apps/chat/img/emoji/laughing.png" } ,
{ "name" : ":mask:", "url" : "/apps/chat/img/emoji/mask.png" } ,
{ "name" : ":neckbeard:", "url" : "/apps/chat/img/emoji/neckbeard.png" } ,
{ "name" : ":neutral_face:", "url" : "/apps/chat/img/emoji/neutral_face.png" } ,
{ "name" : ":no_mouth:", "url" : "/apps/chat/img/emoji/no_mouth.png" } ,
{ "name" : ":ok_hand:", "url" : "/apps/chat/img/emoji/ok_hand.png" } ,
{ "name" : ":open_hands:", "url" : "/apps/chat/img/emoji/open_hands.png" } ,
{ "name" : ":open_mouth:", "url" : "/apps/chat/img/emoji/open_mouth.png" } ,
{ "name" : ":pensive:", "url" : "/apps/chat/img/emoji/pensive.png" } ,
{ "name" : ":persevere:", "url" : "/apps/chat/img/emoji/persevere.png" } ,
{ "name" : ":point_down:", "url" : "/apps/chat/img/emoji/point_down.png" } ,
{ "name" : ":point_left:", "url" : "/apps/chat/img/emoji/point_left.png" } ,
{ "name" : ":point_right:", "url" : "/apps/chat/img/emoji/point_right.png" } ,
{ "name" : ":point_up:", "url" : "/apps/chat/img/emoji/point_up.png" } ,
{ "name" : ":point_up_2:", "url" : "/apps/chat/img/emoji/point_up_2.png" } ,
{ "name" : ":pray:", "url" : "/apps/chat/img/emoji/pray.png" } ,
{ "name" : ":punch:", "url" : "/apps/chat/img/emoji/punch.png" } ,
{ "name" : ":rage:", "url" : "/apps/chat/img/emoji/rage.png" } ,
{ "name" : ":raised_hand:", "url" : "/apps/chat/img/emoji/raised_hand.png" } ,
{ "name" : ":raised_hands:", "url" : "/apps/chat/img/emoji/raised_hands.png" } ,
{ "name" : ":relaxed:", "url" : "/apps/chat/img/emoji/relaxed.png" } ,
{ "name" : ":relieved:", "url" : "/apps/chat/img/emoji/relieved.png" } ,
{ "name" : ":satisfied:", "url" : "/apps/chat/img/emoji/satisfied.png" } ,
{ "name" : ":scream:", "url" : "/apps/chat/img/emoji/scream.png" } ,
{ "name" : ":sleeping:", "url" : "/apps/chat/img/emoji/sleeping.png" } ,
{ "name" : ":sleepy:", "url" : "/apps/chat/img/emoji/sleepy.png" } ,
{ "name" : ":smile:", "url" : "/apps/chat/img/emoji/smile.png" } ,
{ "name" : ":smiley:", "url" : "/apps/chat/img/emoji/smiley.png" } ,
{ "name" : ":smiling_imp:", "url" : "/apps/chat/img/emoji/smiling_imp.png" } ,
{ "name" : ":smirk:", "url" : "/apps/chat/img/emoji/smirk.png" } ,
{ "name" : ":sob:", "url" : "/apps/chat/img/emoji/sob.png" } ,
{ "name" : ":stuck_out_tongue:", "url" : "/apps/chat/img/emoji/stuck_out_tongue.png" } ,
{ "name" : ":stuck_out_tongue_closed_eyes:", "url" : "/apps/chat/img/emoji/stuck_out_tongue_closed_eyes.png" } ,
{ "name" : ":stuck_out_tongue_winking_eye:", "url" : "/apps/chat/img/emoji/stuck_out_tongue_winking_eye.png" } ,
{ "name" : ":sunglasses:", "url" : "/apps/chat/img/emoji/sunglasses.png" } ,
{ "name" : ":sweat:", "url" : "/apps/chat/img/emoji/sweat.png" } ,
{ "name" : ":sweat_smile:", "url" : "/apps/chat/img/emoji/sweat_smile.png" } ,
{ "name" : ":thumbsdown:", "url" : "/apps/chat/img/emoji/thumbsdown.png" } ,
{ "name" : ":thumbsup:", "url" : "/apps/chat/img/emoji/thumbsup.png" } ,
{ "name" : ":tired_face:", "url" : "/apps/chat/img/emoji/tired_face.png" } ,
{ "name" : ":triumph:", "url" : "/apps/chat/img/emoji/triumph.png" } ,
{ "name" : ":unamused:", "url" : "/apps/chat/img/emoji/unamused.png" } ,
{ "name" : ":v:", "url" : "/apps/chat/img/emoji/v.png" } ,
{ "name" : ":wave:", "url" : "/apps/chat/img/emoji/wave.png" } ,
{ "name" : ":weary:", "url" : "/apps/chat/img/emoji/weary.png" } ,
{ "name" : ":wink:", "url" : "/apps/chat/img/emoji/wink.png" } ,
{ "name" : ":worried:", "url" : "/apps/chat/img/emoji/worried.png" } ,
{ "name" : ":yum:", "url" : "/apps/chat/img/emoji/yum.png" } ,
]

};
