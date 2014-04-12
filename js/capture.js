$(function(){
    navigator.geolocation.getCurrentPosition(function(position) {
        alert(position.coords.latitude+ position.coords.longitude);
    });
});