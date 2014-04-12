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

    getImageJSON();

    function getImageJSON(){
        navigator.geolocation.getCurrentPosition(function(position) {

            $.post("/Projects/LAHacks/magic/getImage.php",
                { locationLong: position.coords.longitude, locationLat: position.coords.latitude },
                function(data){
                    console.log('bloop');
                    alert(data);
                }
            );

        }, function(){},{maximumAge: 30000, enableHighAccuracy:true});

    }
});