$(function(){

    $('#mainImageUploadButton').click(function(){
        getGeoLocation();
    });

    function getGeoLocation(){
        navigator.geolocation.getCurrentPosition(function(position) {
            $('#locationLong').val(position.coords.longitude);
            $('#locationLat').val(position.coords.latitude);
            $('#locationAcc').val(position.coords.accuracy);

            //alert(position.coords.latitude+ position.coords.longitude);

        });
    }

});