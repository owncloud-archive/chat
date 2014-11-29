/**
 * Copyright (c) 2013, Bernhard Posselt <dev@bernhard-posselt.com>
 * This file is licensed under the Lesser General Public License version 3 or later.
 * See the COPYING file.
 */
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

            var smileyKeys = Object.keys(options.smilies);
            
            // split input into lines to avoid dealing with tons of 
            // additional complexity/combinations arising from new lines
            var lines = text.split('&#10;');
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
                    options.smilies[smiley] + '"/>';
                
                // partially apply the replacer function to set the replacement
                // string
                var replacer = smileyReplacer.bind(null, smiley, replacement);
                lines = lines.map(replacer);
            }

            text = lines.join('&#10;');

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