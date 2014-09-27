/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
window.Chat =  window.Chat || {};
Chat.app = Chat.app || {};
Chat.app.util = {
	timeStampToDate : function(timestamp){
		var date = new Date(timestamp * 1000);
		var hours = date.getHours();
		var minutes = (date.getMinutes() < 10 ? '0' : '') + date.getMinutes();
		var seconds = date.getSeconds();
		return	{hours : date.getHours(), minutes : (date.getMinutes() < 10 ? '0' : '') + date.getMinutes(), seconds : date.getSeconds()};
	},
	isYoutubeUrl : function(url) {
		if(url.indexOf('https://youtube.com') > -1
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
	//emojis : [
	//	{ "name" : ':+1:' , "url" : OC.imagePath('chat', 'emoji/+1.png') },
	//	{ "name" : ':-1:' , "url" : OC.imagePath('chat', 'emoji/-1.png') },
	//	{ "name" : ':alien:' , "url" : OC.imagePath('chat', 'emoji/alien.png') },
	//	{ "name" : ':angry:' , "url" : OC.imagePath('chat', 'emoji/angry.png') },
	//	{ "name" : ':anguished:' , "url" : OC.imagePath('chat', 'emoji/anguished.png') },
	//	{ "name" : ':astonished:' , "url" : OC.imagePath('chat', 'emoji/astonished.png') },
	//	{ "name" : ':blush:' , "url" : OC.imagePath('chat', 'emoji/blush.png') },
	//	{ "name" : ':bowtie:' , "url" : OC.imagePath('chat', 'emoji/bowtie.png') },
	//	{ "name" : ':clap:' , "url" : OC.imagePath('chat', 'emoji/clap.png') },
	//	{ "name" : ':cold_sweat:' , "url" : OC.imagePath('chat', 'emoji/cold_sweat.png') },
	//	{ "name" : ':confounded:' , "url" : OC.imagePath('chat', 'emoji/confounded.png') },
	//	{ "name" : ':confused:' , "url" : OC.imagePath('chat', 'emoji/confused.png') },
	//	{ "name" : ':cry:' , "url" : OC.imagePath('chat', 'emoji/cry.png') },
	//	{ "name" : ':disappointed:' , "url" : OC.imagePath('chat', 'emoji/disappointed.png') },
	//	{ "name" : ':disappointed_relieved:' , "url" : OC.imagePath('chat', 'emoji/disappointed_relieved.png') },
	//	{ "name" : ':dizzy_face:' , "url" : OC.imagePath('chat', 'emoji/dizzy_face.png') },
	//	{ "name" : ':expressionless:' , "url" : OC.imagePath('chat', 'emoji/expressionless.png') },
	//	{ "name" : ':facepunch:' , "url" : OC.imagePath('chat', 'emoji/facepunch.png') },
	//	{ "name" : ':fearful:' , "url" : OC.imagePath('chat', 'emoji/fearful.png') },
	//	{ "name" : ':fist:' , "url" : OC.imagePath('chat', 'emoji/fist.png') },
	//	{ "name" : ':flushed:' , "url" : OC.imagePath('chat', 'emoji/flushed.png') },
	//	{ "name" : ':frowning:' , "url" : OC.imagePath('chat', 'emoji/frowning.png') },
	//	{ "name" : ':grimacing:' , "url" : OC.imagePath('chat', 'emoji/grimacing.png') },
	//	{ "name" : ':grin:' , "url" : OC.imagePath('chat', 'emoji/grin.png') },
	//	{ "name" : ':grinning:' , "url" : OC.imagePath('chat', 'emoji/grinning.png') },
	//	{ "name" : ':hand:' , "url" : OC.imagePath('chat', 'emoji/hand.png') },
	//	{ "name" : ':heart_eyes:' , "url" : OC.imagePath('chat', 'emoji/heart_eyes.png') },
	//	{ "name" : ':hushed:' , "url" : OC.imagePath('chat', 'emoji/hushed.png') },
	//	{ "name" : ':imp:' , "url" : OC.imagePath('chat', 'emoji/imp.png') },
	//	{ "name" : ':innocent:' , "url" : OC.imagePath('chat', 'emoji/innocent.png') },
	//	{ "name" : ':joy:' , "url" : OC.imagePath('chat', 'emoji/joy.png') },
	//	{ "name" : ':kissing:' , "url" : OC.imagePath('chat', 'emoji/kissing.png') },
	//	{ "name" : ':kissing_closed_eyes:' , "url" : OC.imagePath('chat', 'emoji/kissing_closed_eyes.png') },
	//	{ "name" : ':kissing_heart:' , "url" : OC.imagePath('chat', 'emoji/kissing_heart.png') },
	//	{ "name" : ':kissing_smiling_eyes:' , "url" : OC.imagePath('chat', 'emoji/kissing_smiling_eyes.png') },
	//	{ "name" : ':laughing:' , "url" : OC.imagePath('chat', 'emoji/laughing.png') },
	//	{ "name" : ':mask:' , "url" : OC.imagePath('chat', 'emoji/mask.png') },
	//	{ "name" : ':neckbeard:' , "url" : OC.imagePath('chat', 'emoji/neckbeard.png') },
	//	{ "name" : ':neutral_face:' , "url" : OC.imagePath('chat', 'emoji/neutral_face.png') },
	//	{ "name" : ':no_mouth:' , "url" : OC.imagePath('chat', 'emoji/no_mouth.png') },
	//	{ "name" : ':ok_hand:' , "url" : OC.imagePath('chat', 'emoji/ok_hand.png') },
	//	{ "name" : ':open_hands:' , "url" : OC.imagePath('chat', 'emoji/open_hands.png') },
	//	{ "name" : ':open_mouth:' , "url" : OC.imagePath('chat', 'emoji/open_mouth.png') },
	//	{ "name" : ':pensive:' , "url" : OC.imagePath('chat', 'emoji/pensive.png') },
	//	{ "name" : ':persevere:' , "url" : OC.imagePath('chat', 'emoji/persevere.png') },
	//	{ "name" : ':point_down:' , "url" : OC.imagePath('chat', 'emoji/point_down.png') },
	//	{ "name" : ':point_left:' , "url" : OC.imagePath('chat', 'emoji/point_left.png') },
	//	{ "name" : ':point_right:' , "url" : OC.imagePath('chat', 'emoji/point_right.png') },
	//	{ "name" : ':point_up:' , "url" : OC.imagePath('chat', 'emoji/point_up.png') },
	//	{ "name" : ':point_up_2:' , "url" : OC.imagePath('chat', 'emoji/point_up_2.png') },
	//	{ "name" : ':pray:' , "url" : OC.imagePath('chat', 'emoji/pray.png') },
	//	{ "name" : ':punch:' , "url" : OC.imagePath('chat', 'emoji/punch.png') },
	//	{ "name" : ':rage:' , "url" : OC.imagePath('chat', 'emoji/rage.png') },
	//	{ "name" : ':raised_hand:' , "url" : OC.imagePath('chat', 'emoji/raised_hand.png') },
	//	{ "name" : ':raised_hands:' , "url" : OC.imagePath('chat', 'emoji/raised_hands.png') },
	//	{ "name" : ':relaxed:' , "url" : OC.imagePath('chat', 'emoji/relaxed.png') },
	//	{ "name" : ':relieved:' , "url" : OC.imagePath('chat', 'emoji/relieved.png') },
	//	{ "name" : ':satisfied:' , "url" : OC.imagePath('chat', 'emoji/satisfied.png') },
	//	{ "name" : ':scream:' , "url" : OC.imagePath('chat', 'emoji/scream.png') },
	//	{ "name" : ':sleeping:' , "url" : OC.imagePath('chat', 'emoji/sleeping.png') },
	//	{ "name" : ':sleepy:' , "url" : OC.imagePath('chat', 'emoji/sleepy.png') },
	//	{ "name" : ':smile:' , "url" : OC.imagePath('chat', 'emoji/smile.png') },
	//	{ "name" : ':smiley:' , "url" : OC.imagePath('chat', 'emoji/smiley.png') },
	//	{ "name" : ':smiling_imp:' , "url" : OC.imagePath('chat', 'emoji/smiling_imp.png') },
	//	{ "name" : ':smirk:' , "url" : OC.imagePath('chat', 'emoji/smirk.png') },
	//	{ "name" : ':sob:' , "url" : OC.imagePath('chat', 'emoji/sob.png') },
	//	{ "name" : ':stuck_out_tongue:' , "url" : OC.imagePath('chat', 'emoji/stuck_out_tongue.png') },
	//	{ "name" : ':stuck_out_tongue_closed_eyes:' , "url" : OC.imagePath('chat', 'emoji/stuck_out_tongue_closed_eyes.png') },
	//	{ "name" : ':stuck_out_tongue_winking_eye:' , "url" : OC.imagePath('chat', 'emoji/stuck_out_tongue_winking_eye.png') },
	//	{ "name" : ':sunglasses:' , "url" : OC.imagePath('chat', 'emoji/sunglasses.png') },
	//	{ "name" : ':sweat:' , "url" : OC.imagePath('chat', 'emoji/sweat.png') },
	//	{ "name" : ':sweat_smile:' , "url" : OC.imagePath('chat', 'emoji/sweat_smile.png') },
	//	{ "name" : ':thumbsdown:' , "url" : OC.imagePath('chat', 'emoji/thumbsdown.png') },
	//	{ "name" : ':thumbsup:' , "url" : OC.imagePath('chat', 'emoji/thumbsup.png') },
	//	{ "name" : ':tired_face:' , "url" : OC.imagePath('chat', 'emoji/tired_face.png') },
	//	{ "name" : ':triumph:' , "url" : OC.imagePath('chat', 'emoji/triumph.png') },
	//	{ "name" : ':unamused:' , "url" : OC.imagePath('chat', 'emoji/unamused.png') },
	//	{ "name" : ':v:' , "url" : OC.imagePath('chat', 'emoji/v.png') },
	//	{ "name" : ':wave:' , "url" : OC.imagePath('chat', 'emoji/wave.png') },
	//	{ "name" : ':weary:' , "url" : OC.imagePath('chat', 'emoji/weary.png') },
	//	{ "name" : ':wink:' , "url" : OC.imagePath('chat', 'emoji/wink.png') },
	//	{ "name" : ':worried:' , "url" : OC.imagePath('chat', 'emoji/worried.png') },
	//	{ "name" : ':yum:' , "url" : OC.imagePath('chat', 'emoji/yum.png') },
	//]
};
