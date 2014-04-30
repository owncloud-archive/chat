(function(angular){

'use strict';


var app = angular.module('bernhardposselt.enhancetext', ['ngSanitize'])
.provider('enhanceTextFilter', function () {

    // taken from https://developer.mozilla.org/en/docs/Web/JavaScript/Guide/Regular_Expressions
    function escapeRegExp(str) {
        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    }

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

    var getDimensionsHtml = function(height, width) {
        var dimensions = '';
        if (angular.isDefined(height)) {
            dimensions += 'height="' + height + '" ';
        }

        if (angular.isDefined(width)) {
            dimensions += 'width="' + width + '" ';
        }

        return dimensions;
    };

    this.$get = ['$sanitize', '$sce', function ($sanitize, $sce) {
        return function (text) {

            var originalText = text;

            // hit cache first before replacing
            if (options.cache) {
                var cachedResult = textCache[text];
                if (angular.isDefined(cachedResult)) {
                    return cachedResult;
                }
            }

            // sanitize text
            text = $sanitize(text);

            // loop over smilies and replace them in the text
            var smileyKeys = Object.keys(options.smilies);
            for (var i=0; i<smileyKeys.length; i++) {
                var smiley = smileyKeys[i];
                var smileyKeyPath = options.smilies[smiley];
                var replacement = '<img alt="' + smiley + '" src="' + 
                    smileyKeyPath + '"/>';
                
                var middleSmiley = " " + escapeRegExp(smiley) + " ";
                text = text.replace(new RegExp(middleSmiley), " " + replacement + " ");

                var onlySmiley = "^" + escapeRegExp(smiley) + "$";
                text = text.replace(new RegExp(onlySmiley), replacement);

                var endSmiley = " " + escapeRegExp(smiley) + "$";
                text = text.replace(new RegExp(endSmiley), " " + replacement);

                var startSmiley = "^" + escapeRegExp(smiley) + " ";
                text = text.replace(new RegExp(startSmiley), replacement + " ");

                var lineBreakSmiley = " " + escapeRegExp(smiley) + "&#10;";
                text = text.replace(new RegExp(lineBreakSmiley), " " + replacement + "&#10;");

                var newLineBetweenSmiley = "&#10;" + escapeRegExp(smiley) + "&#10;";
                text = text.replace(new RegExp(newLineBetweenSmiley), "&#10;" + replacement + "&#10;");

                var newLineEndSmiley = "&#10;" + escapeRegExp(smiley) + "$";
                text = text.replace(new RegExp(newLineEndSmiley), "&#10;" + replacement);

                var newLineStartSmiley = "^" + escapeRegExp(smiley) + "&#10;";
                text = text.replace(new RegExp(newLineStartSmiley), replacement + "&#10;");

                var onlySmileyNewLine = "&#10;" + escapeRegExp(smiley) + " ";
                text = text.replace(new RegExp(onlySmileyNewLine), "&#10;" + replacement + " ");

                var endSmileyNewLine = "&#10;" + escapeRegExp(smiley) + "$";
                text = text.replace(new RegExp(endSmileyNewLine), "&#10;" + replacement);

                var lineBreakSmileyNewLine = "&#10;" + escapeRegExp(smiley) + "&#10;";
                text = text.replace(new RegExp(lineBreakSmileyNewLine), "&#10;" + replacement + "&#10;");
            }

            // embed images
            if (options.embedImages) {
                var imgRegex = /((?:https?):\/\/\S*\.(?:gif|jpg|jpeg|tiff|png|svg|webp))/gi;
                var imgDimensions = getDimensionsHtml(options.embeddedImagesHeight,
                    options.embeddedImagesWidth);

                var img = '<a href="$1" target="' + options.embeddedLinkTarget + 
                    '">' + '<img ' + imgDimensions + 'alt="image" src="$1"/></a>';
                text = text.replace(imgRegex, img);
            }

            // embed videos
            if (options.embedVideos) {
                var vidRegex = /((?:https?):\/\/\S*\.(?:ogv|webm))/gi;
                var vidDimensions = getDimensionsHtml(options.embeddedVideosHeight,
                    options.embeddedVideosWidth);

                var vid = '<video ' + vidDimensions + 'src="$1" controls preload="none"></video>';
                text = text.replace(vidRegex, vid);
            }

            // embed youtube
            if (options.embedYoutube) {
                var ytRegex = /https?:\/\/(?:[0-9A-Z-]+\.)?(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeningroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*[^\w\-\s]))([\w\-]{11})[?=&+%\w-]*/gi;
                var ytDimensions = getDimensionsHtml(options.embeddedYoutubeHeight,
                    options.embeddedYoutubeWidth);

                var yt = '<iframe ' + ytDimensions + 
                    'src="https://www.youtube.com/embed/$1" ' + 
                    'frameborder="0" allowfullscreen></iframe>';
                text = text.replace(ytRegex, yt);
            }

            // replace newlines with breaks
            if (options.newLineToBr) {
                text = text.replace('/\n/g', '<br/>').replace(/&#10;/g, '<br/>');
            }

            // replace links
            if (options.embedLinks) {
                var linkRegex = /((href|src)=["']|)(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
                text = text.replace(linkRegex, function() {
                  return  arguments[1] ? 
                          arguments[0] : 
                          '<a target="' + options.embeddedLinkTarget + 
                          '" href="'+ arguments[3] + '">' + 
                          arguments[3] + '</a>';
                });
            }

            // trust result to able to use it in ng-bind-html
            text = $sce.trustAsHtml(text);

            // cache result
            if (options.cache) {
                textCache[originalText] = text;
            }

            return text;
        };
    }];


});

})(angular, undefined);