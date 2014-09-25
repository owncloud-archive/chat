# Angular Enhance Text filter

[![Build Status](https://travis-ci.org/Raydiation/angular-enhance-text.png?branch=master)](https://travis-ci.org/Raydiation/angular-enhance-text)
[![Coverage Status](https://coveralls.io/repos/Raydiation/angular-enhance-text/badge.png?branch=master)](https://coveralls.io/r/Raydiation/angular-enhance-text?branch=master)
[![Bower version](https://badge.fury.io/bo/angular-enhance-text.png)](http://badge.fury.io/bo/angular-enhance-text)

Enhances text by replacing commonly used links and characters, e.g. directly embedding youtube videos, replacing smilies etc. This is useful for creating a web chat application. 

All replacements are cached so there's no downside in using it as filter.

Current features include: 

* Embed smilies
* Embed links
* Embed images (gif|jpg|jpeg|tiff|png|svg|webp)
* Embed videos (ogv|webm)
* Embed youtube videos
* Sanitize output

## License
GNU Lesser General Public License 3+ (LGPLv3+)

## How to use
Install **angular-enhance-text** via bower and link it plus **angular** and **angular-sanitize** in your website:
    
    bower install angular-enhance-text

Finally inject it into your app like:
    
```javascript
angular.module('MyApp', ['bernhardposselt.enhancetext']);
```

The filter is available in your templates by using:

```html
<div ng-bind-html="SomeText | enhanceText"></div>
```

All content to the filter is explicitely sanitized and marked as safe.
.

For a complete example see the examples folder.

## Configuration

To configure the provider, inject the provider in your config method:
    
```javascript
angular.module('MyApp', ['bernhardposselt.enhancetext']).
config(['enhanceTextFilterProvider', function (enhanceTextFilterProvider) {
    enhanceTextFilterProvider.setOptions({
        // your options in here
    });
}]);
```

The following options are available:

```javascript
enhanceTextFilterProvider.setOptions({
    cache: true,  // stores replaced text so angular update does not slow down
    newLineToBr: true,  // replaces \n with <br/>
    embedLinks: true,  // replaces links with Html links
    embeddedLinkTarget: '_blank',  // sets the target of all replaced links
    embedImages: true,  // replaces links to images with Html images
    embeddedImagesHeight: undefined,  // if given will be used to set height of embedded images
    embeddedImagesWidth: undefined,  // if given will be used to set width of embedded images
    embedVideos: true,  // replaces links to videos with Html videos
    embeddedVideosHeight: undefined,  // if given will be used to set height of embedded videos
    embeddedVideosWidth: undefined,  // if given will be used to set width of embedded videos
    embedYoutube: true,  // replaces links to youtube videos with iframed youtube videos
    embeddedYoutubeHeight: undefined,  // height of youtube video
    embeddedYoutubeWidth: undefined,  // width of youtube video
    smilies: {  // key = smilie, value = path to smilie
        ':)': '/img/smiley.png',
        ';)': '/img/smiley2.png'
    }
});
```
