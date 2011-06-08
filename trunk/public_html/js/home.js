/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(function() {
        $( "#progressbar" ).progressbar({
                value: $("#porcentaje").html()
        });

        //scrollpane parts
        var scrollPane = $( ".scroll-pane" ),
                scrollContent = $( ".scroll-content" );

        var topLimit=75;

        if ( scrollContent.height() > scrollPane.height() ) {

            //build slider
            var scrollbar = $( ".scroll-bar" ).slider({
                    orientation: "vertical",
                    value:topLimit,
                    slide: function( event, ui ) {
                            if (ui.value>topLimit) return false;
                            if ( scrollContent.height() > scrollPane.height() ) {
                                    scrollContent.css( "margin-top", Math.round((topLimit-ui.value) / (topLimit-4) * ( scrollPane.height() - scrollContent.height() )) + "px" );
                            } else {
                                    scrollContent.css( "margin-top", 0 );
                            }
                    }
            });

            //append icon to handle
            var handleHelper = scrollbar.find( ".ui-slider-handle" )
            .mousedown(function() {
                    scrollbar.width( handleHelper.width() );
            })
            /*.mouseup(function() {
                    scrollbar.width( "20px" );
            })*/
            .append( "<span class='ui-icon ui-icon-grip-dotted-vertical'></span>" )
            .wrap( "<div class='ui-handle-helper-parent'></div>" ).parent();

            //change overflow to hidden now that slider handles the scrolling
            scrollPane.css( "overflow", "hidden" );

            //size scrollbar and handle proportionally to scroll distance
            function sizeScrollbar() {
                    var remainder = scrollContent.width() - scrollPane.width();
                    var proportion = remainder / scrollContent.width();
                    var handleSize = scrollPane.width() - ( proportion * scrollPane.width() );
                    scrollbar.find( ".ui-slider-handle" ).css({
                            width: "20px"
                    });
                    handleHelper.width( "" ).width( scrollbar.width() - handleSize );
            }

            //reset slider value based on scroll content position
            function resetValue() {
                    var remainder = scrollPane.width() - scrollContent.width();
                    var leftVal = scrollContent.css( "margin-left" ) === "auto" ? 0 :
                            parseInt( scrollContent.css( "margin-left" ) );
                    var percentage = Math.round( leftVal / remainder * 100 );
                    scrollbar.slider( "value", percentage );
            }

            //if the slider is 100% and window gets larger, reveal content
            function reflowContent() {
                            var showing = scrollContent.width() + parseInt( scrollContent.css( "margin-left" ), 10 );
                            var gap = scrollPane.width() - showing;
                            if ( gap > 0 ) {
                                    scrollContent.css( "margin-left", parseInt( scrollContent.css( "margin-left" ), 10 ) + gap );
                            }
            }

            //change handle position on window resize
            $( window ).resize(function() {
                    resetValue();
                    sizeScrollbar();
                    //reflowContent();
            });
            //init scrollbar size
            setTimeout( sizeScrollbar, 10 );//safari wants a timeout
        }
});