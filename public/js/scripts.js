(function( $ ) {

    'use strict';

    function buttonDialog( event ) {

        var target, source;

        event.preventDefault();
        event.stopImmediatePropagation();

        target = event.currentTarget;
        source = target.getAttribute( 'href' );

        if ( ! source || '' !== source ) {
            windowPopup( source );
            return;
        }

        return;
    }

    function windowPopup( url ) {

        var wind = window;
        var docu = document;

        var screenLeft = undefined !== wind.screenLeft ? wind.screenLeft : screen.left;
        var screenTop = undefined !== wind.screenTop ? wind.screenTop : screen.top;
        var screenWidth = wind.innerWidth ? wind.innerWidth : docu.documentElement.clientWidth ? docu.documentElement.clientWidth : screen.width;
        var screenHeight = wind.innerHeight ? wind.innerHeight : docu.documentElement.clientHeight ? docu.documentElement.clientHeight : screen.height;

        var width = 600;
        var height = 430;

        var left = ( ( screenWidth / 2 ) - ( width / 2 ) ) + screenLeft;
        var top = ( ( screenHeight / 2 ) - ( height / 2 ) ) + screenTop;

        var newWindow = wind.open( url, '', 'scrollbars=no,width=' + width + ',height=' + height + ',top=' + top + ',left=' + left );

        if ( newWindow ) {
            newWindow.focus();
        }
    }

    $( 'body' ).on( 'click', '[data-social-buttons="content"] a', buttonDialog );
    $( 'body' ).on( 'click', '[data-social-buttons="image"] a', buttonDialog );

})( jQuery, undefined );
