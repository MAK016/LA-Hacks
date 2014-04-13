/**
 * Created by Mike on 4/12/14.
 */

$(function(){
    var WorldNav = $('#WorldNav');

    WorldNav.panzoom({
        minScale: 0.01,
        maxScale: 7
    });

    getImageJSON();

    WorldNav.on('mousewheel.focal', function( e ) {
        e.preventDefault();
        var delta = e.delta || e.originalEvent.wheelDelta;
        var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
        WorldNav.panzoom('zoom', zoomOut, {
            increment: 0.1,
            focal: e
        });
    });

    function getImageJSON(){
        //navigator.geolocation.getCurrentPosition(function(position) { //TODO: Reinstate geolocation

            $.post("/Projects/LAHacks/magic/getImage.php",
            //$.post("/magic/getImage.php",
                //{ locationLong: position.coords.longitude, locationLat: position.coords.latitude },
                { locationLong: -118.4352035, locationLat: 34.0661969 }, //TODO: Remove this debug line
                function(data){
                    var retVal = jQuery.parseJSON(data);
                    setImages(retVal['Images'], retVal['OriginLongitude'], retVal['OriginLatitude'] );
                }
            );

        //}, function(){},{maximumAge: 30000, enableHighAccuracy:true});
    }

    function setImages(images, curLong, curLat){

        var imgPos = [];

        var WorldX = 0, WorldY = 0, WorldWidth = 0, WorldHeight = 0;

        for(var i=0; i<images.length; i++){

            var imgTop = (images[i]['Latitude']-curLat)*350000,
                imgLeft = (images[i]['Longitude']-curLong)*350000,//-curLong+getRandomArbitrary(-.01,.01))
                imgWidth = images[i]['Width'],
                imgHeight = images[i]['Height'];

            console.log('WorldX: ' + WorldX);
            console.log('WorldY: ' + WorldY);
            console.log('WorldWidth: ' + WorldWidth);
            console.log('WorldHeight: ' + WorldHeight);

            //We don't need collision checks for the first item
            if(i!=0){
                for(var j=0; j<imgPos.length; j++){
                    if(rectIntersect(imgLeft,imgWidth,imgPos[j]['x'],imgPos[j]['w'],imgTop,imgHeight,imgPos[j]['y'],imgPos[j]['h'])){

                        var MovementAngle = Math.atan2((WorldY+WorldHeight/2) - (imgTop+imgHeight/2) ,(WorldX+WorldWidth/2)-(imgLeft+imgWidth/2));

                        $('#WorldNav').append( "<div style='color:white;z-index:500;position:absolute;top:"+imgTop+"px;left:"+imgLeft+"px;'>"+MovementAngle+"</div>" );

                        while(rectIntersect(imgLeft,imgWidth,imgPos[j]['x'],imgPos[j]['w'],imgTop,imgHeight,imgPos[j]['y'],imgPos[j]['h'])){
                            imgTop += Math.sin(MovementAngle)*100;
                            imgLeft += Math.cos(MovementAngle)*100;
                        }
                        j=0+0;
                    }
                }
            }

            //Set coordinates for the world, to determine geometric center of the world.
            if(imgLeft<WorldX){
                WorldX = imgLeft;
            }
            if(imgTop<WorldY){
                WorldY = imgTop;
            }
            if(imgTop+imgHeight>WorldY+WorldHeight){
                WorldHeight = (imgTop+imgHeight) - WorldY;
            }
            if(imgLeft+imgWidth > WorldX+WorldWidth){
                WorldWidth = (imgLeft+imgWidth) - WorldX;
            }

            var curImgPos = [];
            curImgPos['x'] = imgLeft;
            curImgPos['y'] = imgTop;
            curImgPos['w'] = imgWidth;
            curImgPos['h'] = imgHeight;

            imgPos.push(curImgPos);

            $('<img />')
                .attr('src', 'data:image/jpeg;base64,'+images[i]['Base64Image'])
                .addClass('WorldImage')
                .css({
                    'top': imgTop,
                    'left': imgLeft,
                    'width': imgWidth,
                    'height': imgHeight
                })
                .load(function(){
                    $('#WorldNav').append( $(this) );
                });

        }
    }

    function getRandomArbitrary(min, max) {
        return Math.random() * (max - min) + min;
    }

    function rectIntersect(X1,W1,X2,W2,Y1,H1,Y2,H2){
        if (X1+W1<X2 || X2+W2<X1 || Y1+H1<Y2 || Y2+H2<Y1)
            return false;
        return true;
    }
});