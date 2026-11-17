<div class="row">
    <div class="col-md-12">
        <div class="form-group ">
            <div id="map_canvas" style="height:550px"></div>
        </div>
    </div>
</div>


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATnKfSj_uqVEJ0KMbQB4ECq3Mh-5SHmio&v=3&sensor=false&callback=initialize"></script>

<script>
    var geocoder;
    var map;
    var marker;

    var customer = <?php echo json_encode($customer); ?>;

    function initialize(){
        var initialLat = 7.207248;
        var initialLong = 79.843413;

        if (customer.lat_coordinates != null) {
            initialLat = customer.lat_coordinates;
            initialLong = customer.lng_coordinates;
        }


        var latlng = new google.maps.LatLng(initialLat, initialLong);
        var options = {
            zoom: 16,
            center: latlng,
            mapType: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById("map_canvas"), options);

        geocoder = new google.maps.Geocoder();

        marker = new google.maps.Marker({
            map:map,
            draggable:false,
            position: latlng
        });

        google.maps.event.addListener(marker, 'dragend', function (evt) {
                $("#lat_coordinats").val(evt.latLng.lat().toFixed(6));
                $("#lng_coordinats").val(evt.latLng.lng().toFixed(6));
                map.panTo(evt.latLng);
            });
    }

    </script>
