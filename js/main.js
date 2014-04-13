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
                    setImages(retVal);
                }
            );

        //}, function(){},{maximumAge: 30000, enableHighAccuracy:true});
    }

    function setImages(data){
        var SplitCount = 3;

        var longDiff = Math.abs(data['MaxLong']-data['MinLong'])/SplitCount;
        var latDiff = Math.abs(data['MaxLat']-data['MinLat'])/SplitCount;

        console.log(longDiff);
        console.log(latDiff);

        for(var y=0; y<SplitCount; y++){

            var minLatSegment = parseFloat(data['MinLat'])+y*latDiff;
            var maxLatSegment = parseFloat(data['MinLat'])+(y+1)*latDiff;

            for(var x=0; x<SplitCount; x++){

                console.log(x);

                var minLongSegment = parseFloat(data['MinLong'])+x*longDiff;
                var maxLongSegment = parseFloat(data['MinLong'])+(x+1)*longDiff;

                var masonryGridDiv = $('<div/>').addClass('masonryGrid').attr('id',x+'-'+y).css('height','1000px');

                if(x!=0){
                    masonryGridDiv.css('display','inline');
                }

                for(var i=0; i<data['Images'].length; i++){

                    var image = data['Images'][i];

                    console.log(maxLatSegment - minLatSegment);

                    if(image['Latitude']>=minLatSegment && image['Latitude']<maxLatSegment
                        && image['Longitude']>=minLongSegment && image['Longitude']<maxLongSegment
                        && data['Images'][i]['Used'] != true){

                        data['Images'][i]['Used'] = true;

                        $('<img />')
                            .attr('src', 'data:image/jpeg;base64,'+ data['Images'][i]['Base64Image'])
                            .addClass('WorldImage')
                            .data('id','#'+x+'-'+y)
                            .css({
                                'width': data['Images'][i]['Width'],
                                'height': data['Images'][i]['Height']
                            })
                            .load(function(){
                                $($(this).data('id')).append( $(this) ).masonry({
                                    // other masonry options
                                    itemSelector: '.WorldImage'
                                }).masonry('reloadItems');
                            });

                    }
                }

                $('#WorldNav').append(masonryGridDiv);
            }
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