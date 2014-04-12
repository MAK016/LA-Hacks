/**
 * Created by Mike on 4/12/14.
 */

$(function(){
    var WorldNav = $('#WorldNav');

    WorldNav.panzoom({
    });

    WorldNav.on('mousewheel.focal', function( e ) {
        e.preventDefault();
        var delta = e.delta || e.originalEvent.wheelDelta;
        var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
        WorldNav.panzoom('zoom', zoomOut, {
            increment: 0.1,
            focal: e
        });
    });
});