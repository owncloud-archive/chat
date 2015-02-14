(function(angular){

'use strict';


var app = angular.module('bernhardposselt.enhancetext', ['ngSanitize'])
.provider('enhanceTextFilter', function () {

    var options = {
            cache: true,
            newLineToBr: true,
            embedLinks: true,
            embeddedLinkTarget: '_blank',
            embedImages: true,
            embeddedImagesHeight: undefined,
            embeddedImagesWidth: undefined,
            embedVideos: true,
            embeddedVideosHeight: undefined,
            embeddedVideosWidth: undefined,
            embedYoutube: true,
            embeddedYoutubeHeight: undefined,
            embeddedYoutubeWidth: undefined,
            smilies: {}
        },
        textCache = {};

    this.setOptions = function (customOptions) {
        angular.extend(options, customOptions);
    };

    /* @ngInject */
    this.$get = ["$sce", "TextEnhancer", function ($sce, TextEnhancer) {
        return function (text) {
            var originalText = text;

            // hit cache first before replacing
            if (options.cache) {
                var cachedResult = textCache[text];
                if (angular.isDefined(cachedResult)) {
                    return cachedResult;
                }
            }

            text = TextEnhancer(text, options);

            // trust result to able to use it in ng-bind-html
            text = $sce.trustAsHtml(text);

            // cache result
            if (options.cache) {
                textCache[originalText] = text;
            }

            return text;
        };
    }];
    this.$get.$inject = ["$sce", "TextEnhancer"];


});
app.factory('TextEnhancer',
["SmileyEnhancer", "VideoEnhancer", "NewLineEnhancer", "ImageEnhancer", "YouTubeEnhancer", "LinkEnhancer", function (SmileyEnhancer, VideoEnhancer, NewLineEnhancer, ImageEnhancer,
          YouTubeEnhancer, LinkEnhancer) {
    return function (text, options) {
        text = escapeHtml(text);
        text = SmileyEnhancer(text, options.smilies);

        if (options.embedImages) {
            text = ImageEnhancer(text, options.embeddedImagesHeight,
                                 options.embeddedVideosWidth,
                                 options.embeddedLinkTarget);
        }

        if (options.embedVideos) {
            text = VideoEnhancer(text, options.embeddedImagesHeight,
                                 options.embeddedVideosWidth);
        }

        if (options.embedYoutube) {
            text = YouTubeEnhancer(text, options.embeddedYoutubeHeight,
                                   options.embeddedYoutubeWidth);
        }

        if (options.newLineToBr) {
            text = NewLineEnhancer(text);
        }

        if (options.embedLinks) {
            text = LinkEnhancer(text, options.embeddedLinkTarget);
        }

        return text;
    };
}]);


app.factory('ImageEnhancer', function () {
    return function (text, height, width, target) {
        if(target === undefined) {
            target = '_blank';
        }
        
        var imgRegex = /((?:https?):\/\/\S*\.(?:gif|jpg|jpeg|tiff|png|svg|webp))/gi;
        var imgDimensions = getDimensionsHtml(height, width);

        var img = '<a href="$1" target="' + target + 
            '">' + '<img ' + imgDimensions + 'alt="image" src="$1"/></a>';
        return text.replace(imgRegex, img);
    };
});
app.factory('LinkEnhancer', function () {
    return function (text, target) {
        if(target === undefined) {
            target = '_blank';
        }

        var regex = /((href|src)=["']|)(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
        
        return text.replace(regex, function() {
            return  arguments[1] ? 
                arguments[0] : 
                '<a target="' + target + '" href="'+ arguments[3] + '">' + arguments[3] + '</a>';
        });
    };
});
app.factory('NewLineEnhancer', function () {
    return function (text) {
        return text.replace(/\n/g, '<br/>').replace(/&#10;/g, '<br/>');
    };
});
app.factory('SmileyEnhancer', function () {
    return function(text, smilies) {

        var smileyKeys = Object.keys(smilies);

        // split input into lines to avoid dealing with tons of
        // additional complexity/combinations arising from new lines
        var lines = text.split('\n');

        var smileyReplacer = function (smiley, replacement, line) {
            // four possibilities: at the beginning, at the end, in the
            // middle or only the smiley
            var startSmiley = "^" + escapeRegExp(smiley) + " ";
            var endSmiley = " " + escapeRegExp(smiley) + "$";
            var middleSmiley = " " + escapeRegExp(smiley) + " ";
            var onlySmiley = "^" + escapeRegExp(smiley) + "$";

            return line.
                replace(new RegExp(startSmiley), replacement + " ").
                replace(new RegExp(endSmiley), " " + replacement).
                replace(new RegExp(middleSmiley), " " + replacement + " ").
                replace(new RegExp(onlySmiley), replacement);
        };

        // loop over smilies and replace them in the text
        for (var i=0; i<smileyKeys.length; i++) {
            var smiley = smileyKeys[i];
            var replacement = '<img alt="' + smiley + '" src="' +
                smilies[smiley] + '"/>';

            // partially apply the replacer function to set the replacement
            // string
            var replacer = smileyReplacer.bind(null, smiley, replacement);
            lines = lines.map(replacer);
        }

        return lines.join('\n');
    };
});
app.factory('VideoEnhancer', function () {
    return function (text, height, width) {
        var regex = /((?:https?):\/\/\S*\.(?:ogv|webm))/gi;
        var dimensions = getDimensionsHtml(height, width);
        var vid = '<video ' + dimensions + 'src="$1" controls preload="none"></video>';
        return text.replace(regex, vid);
    };
});
app.factory('YouTubeEnhancer', function () {
    return function (text, height, width) {
        var regex = /https?:\/\/(?:[0-9A-Z-]+\.)?(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeningroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*[^\w\-\s]))([\w\-]{11})[?=&+%\w-]*/gi;
        var dimensions = getDimensionsHtml(height, width);

        var html = '<iframe ' + dimensions + 
            'src="https://www.youtube.com/embed/$1" ' + 
            'frameborder="0" allowfullscreen></iframe>';
        return text.replace(regex, html);
    };
});
function escapeHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
// taken from https://developer.mozilla.org/en/docs/Web/JavaScript/Guide/Regular_Expressions
function escapeRegExp (str) {
    return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
}
function getDimensionsHtml (height, width) {
    var dimensions = '';
    if (angular.isDefined(height)) {
        dimensions += 'height="' + height + '" ';
    }

    if (angular.isDefined(width)) {
        dimensions += 'width="' + width + '" ';
    }

    return dimensions;
}

})(angular, undefined);