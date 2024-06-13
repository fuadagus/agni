@extends('layouts.template')

@section('style')
    <style>
        html,
        body {
            height: 100%;
            width: 100%;
        }

        #map {
            height: calc(100vh - 56px);
            width: 100%;
            margin: 0;
        }
    </style>
@endsection



@section('content')
    <div id="map" style="width: 100vw; height: 100vh; margin: 0"></div>
@endsection



@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="[path to js]/L.Geoserver.js"></script>
    <script>
        // Map
        var map = L.map('map').setView([-7.7956, 110.3695], 13);

        //Basemap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);



        		  /* GeoJSON Point */
        var point = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "Name: " + feature.properties.name + "<br>" +
                    "Description: " + feature.properties.description + "<br>" +
                    "Photo: <img src='{{ asset('storage/images/') }}/" + feature.properties.image +
                    "' class='img-thumbnail' alt='...'>" + "<br>";

                layer.on({
                    click: function(e) {
                        point.bindPopup(popupContent);
                    },
                    mouseover: function(e) {
                        point.bindTooltip(feature.properties.name);
                    },
                });
            },
        });
        $.getJSON("{{ route('api.points') }}", function(data) {
            point.addData(data);
            map.addLayer(point);
        });
        var firePoint = L.geoJson(null, {
    onEachFeature: function(feature, layer) {
        var popupContent = "Temperatur IF4: " + feature.properties.brightness + "<br>" +
            "Daya radiasi: " + feature.properties.frp + "MW" + "<br>" +
            "Tanggal/Waktu: " + feature.properties.acq_datetime + "<br>" +
            "Kepercayaan: " + feature.properties.confidence;

        layer.bindPopup(popupContent);

        layer.on({
            mouseover: function(e) {
                layer.bindTooltip(feature.properties.latitude);
            }
        });
    }
});

$.getJSON("{{ route('api.fetch-fires-data') }}", function(data) {
    firePoint.addData(data);
    map.addLayer(firePoint);
}).fail(function(jqXHR, textStatus, errorThrown) {
    console.error('Error fetching NASA FIRMS data:', textStatus, errorThrown);
});

// var wfsLayer = L.Geoserver.wfs("https://firms.modaps.eosdis.nasa.gov/mapserver/wfs/SouthEast_Asia/YourMapKey/?SERVICE=WFS&REQUEST=GetFeature&VERSION=2.0.0&TYPENAME=ms:fires_snpp_7days&STARTINDEX=0&COUNT=1000&SRSNAME=urn:ogc:def:crs:EPSG::4326&BBOX=-90,-180,90,180,urn:ogc:def:crs:EPSG::4326&outputformat=geojson/geoserver/wfs", {
//   layers: "topp:tasmania_roads",
// });
// wfsLayer.addTo(map);

        //retrieve and visualize points from wfs layer
        // var wfsLayer = L.Geoserver.wfs("https://firms.modaps.eosdis.nasa.gov/mapserver/wfs/SouthEast_Asia/b908b453e6302e29f9be4f5f5b5533b1/?SERVICE=WFS&REQUEST=GetFeature&VERSION=2.0.0&TYPENAME=ms:fires_snpp_7days&STARTINDEX=0&COUNT=1000&SRSNAME=urn:ogc:def:crs:EPSG::4326&BBOX=-90,-180,90,180,urn:ogc:def:crs:EPSG::4326&outputformat=geojson/geoserver/wfs", {
        //     layers: "fires_snpp_7days",
        // });
        // wfsLayer.addTo(map);

        /* GeoJSON Point */

        /* GeoJSON Polyline */
        var polyline = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "Nama: " + feature.properties.name + "<br>" +
                    "Deskripsi: " + feature.properties.description + "<br>" +
                    "Foto: <img src='{{ asset('storage/images/') }}/" + feature.properties.image +
                    "' class='img-thumbnail' alt='...'>" + "<br>";
                layer.on({
                    click: function(e) {
                        polyline.bindPopup(popupContent);
                    },
                    mouseover: function(e) {
                        polyline.bindTooltip(feature.properties.name);
                    },
                });
            },
        });
        $.getJSON("{{ route('api.polylines') }}", function(data) {
            polyline.addData(data);
            map.addLayer(polyline);
        });
        /* GeoJSON Polygon */
        var polygon = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "Nama: " + feature.properties.name + "<br>" +
                    "Deskripsi: " + feature.properties.description + "<br>" +
                    "Foto: <img src='{{ asset('storage/images/') }}/" + feature.properties.image +
                    "' class='img-thumbnail' alt='...'>" + "<br>";

                layer.on({
                    click: function(e) {
                        polygon.bindPopup(popupContent);
                    },
                    mouseover: function(e) {
                        polygon.bindTooltip(feature.properties.name);
                    },
                });
            },
        });
        $.getJSON("{{ route('api.polygons') }}", function(data) {
            polygon.addData(data);
            map.addLayer(polygon);
        });
        //layer control
        var overlayMaps = {
            "Point": point,
            "Polyline": polyline,
            "Polygon": polygon
        };

        var layerControl = L.control.layers(null, overlayMaps, {collapsed: false}).addTo(map);

    </script>
@endsection

</body>

</html>
