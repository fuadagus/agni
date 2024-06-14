@extends('layouts.template')

@section('style')
<style>
    html,
    body {

        width: 100%;
        overflow: hidden;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
        display: none;
    }

  
</style>
@endsection



@section('content')
<div id="map" style="width: 100vw; height:87vh; margin: 0"></div>
@endsection



@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- <script src="[path to js]/L.Geoserver.js"></script> -->
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<!-- Leaflet Heatmap Plugin -->
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

<!-- Leaflet.markercluster CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
<!-- Leaflet.markercluster JavaScript -->
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>


<script>
    // Map
    var map = L.map('map').setView([-0.789275, 113.921327], 5);
    var markers = L.markerClusterGroup();

    //Basemap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

 // Define different basemaps
 var openStreetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });


    var stadiaAlidadeSmoothDark = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.{ext}', {
        minZoom: 0,
        maxZoom: 20,
        attribution: '&copy; <a href="https://www.stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        ext: 'png'
    });



    var esriWorldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
    });

    // Add the default basemap
    stadiaAlidadeSmoothDark.addTo(map);

    // Define the basemaps object for layer control
    var baseMaps = {
        "Stadia Alidade Smooth Dark": stadiaAlidadeSmoothDark,
        "OpenStreetMap": openStreetMap,

        "Esri World Imagery": esriWorldImagery
    };

    // Add the layer control to the map
    L.control.layers(baseMaps).addTo(map);

    /* GeoJSON Point */
    var point = L.geoJson(null, {
        onEachFeature: function (feature, layer) {
            var popupContent = "Name: " + feature.properties.name + "<br>" +
                "Description: " + feature.properties.description + "<br>" +
                "Photo: <img src='{{ asset('storage/images/') }}/" + feature.properties.image +
                "' class='img-thumbnail' alt='...'>" + "<br>";

            layer.on({
                click: function (e) {
                    point.bindPopup(popupContent);
                },
                mouseover: function (e) {
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
    pointToLayer: function (feature, latlng) {
        // Calculate median temperature in Celsius
        var temperatureMedian = ((feature.properties.brightness_2 + feature.properties.brightness) / 2 - 273.15).toFixed(2);

        // Determine confidence level text
        var confidenceLevel = "";
        if (feature.properties.confidence === 'n') {
            confidenceLevel = "Normal";
        } else if (feature.properties.confidence === 'l') {
            confidenceLevel = "Rendah";
        } else if (feature.properties.confidence === 'h') {
            confidenceLevel = "Tinggi";
        } else {
            confidenceLevel = "Tidak diketahui";
        }

        // Define marker options based on temperatureMedian
        var markerOptions = {
            radius: 8, // Adjust as needed
            fillColor: getColorForTemperature(temperatureMedian), // Define a function to determine fill color based on temperature
            color: "#000",
            weight: 1,
            opacity: 1,
            fillOpacity: 0.8
        };

        // Create the marker and bind popup
        var marker = L.circleMarker(latlng, markerOptions);
        var popupContent = "Temperatur Median: " + temperatureMedian + "°C" + "<br>" +
            "Daya radiasi: " + feature.properties.frp + " MW" + "<br>" +
            "Tanggal/Waktu: " + feature.properties.acq_datetime + "<br>" +
            "Kepercayaan: " + confidenceLevel;
        marker.bindPopup(popupContent);

        return marker;
    }
});



// Fetch GeoJSON data from Laravel route
$.getJSON("{{ route('api.fetch-fires-data') }}", function(data) {
   // Add the GeoJSON data to the marker cluster group
firePoint.addData(data);
markers.addLayer(firePoint);

// Add the marker cluster group to the map
map.addLayer(markers);

var heatMapData = [];

    // Process each feature in the GeoJSON data
    data.features.forEach(function(feature) {
        // Ensure feature has temperature property
        if (feature.properties && feature.properties.brightness_2 && feature.properties.brightness) {
            // Extract latitude and longitude
            var lat = feature.geometry.coordinates[1];
            var lng = feature.geometry.coordinates[0];
            var temperatureMedian = ((feature.properties.brightness_2 + feature.properties.brightness) / 2 - 273.15).toFixed(2);
            // Add temperature data to heatmap array
            heatMapData.push([lat, lng, feature.properties.brightness]);
        }
    });

    // Create heatmap layer
    var heat = L.heatLayer(heatMapData, {
        radius: 20, // Adjust radius as needed
        blur: 15,   // Adjust blur as needed
        maxZoom: 18, // Maximum zoom level for heatmap
        gradient: { 0.4: 'blue', 0.65: 'lime', 1: 'red' } // Color gradient
    }).addTo(map);

    // Add layer control if needed
    var overlayMaps = {
        "Heatmap": heat
    };

    L.control.layers(null, overlayMaps, { collapsed: false }).addTo(map);
}).fail(function (jqXHR, textStatus, errorThrown) {
    console.error('Error fetching NASA FIRMS data:', textStatus, errorThrown);
});

// Function to determine fill color based on temperature
function getColorForTemperature(temperature) {
    // Example color gradient based on temperature value
    if (temperature < 20) {
        return "#3498db"; // Blue
    } else if (temperature >= 20 && temperature < 30) {
        return "#f39c12"; // Orange
    } else {
        return "#e74c3c"; // Red
    }
}



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
        onEachFeature: function (feature, layer) {
            var popupContent = "Nama: " + feature.properties.name + "<br>" +
                "Deskripsi: " + feature.properties.description + "<br>" +
                "Foto: <img src='{{ asset('storage/images/') }}/" + feature.properties.image +
                "' class='img-thumbnail' alt='...'>" + "<br>";
            layer.on({
                click: function (e) {
                    polyline.bindPopup(popupContent);
                },
                mouseover: function (e) {
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
        onEachFeature: function (feature, layer) {
            var popupContent = "Nama: " + feature.properties.name + "<br>" +
                "Deskripsi: " + feature.properties.description + "<br>" +
                "Foto: <img src='{{ asset('storage/images/') }}/" + feature.properties.image +
                "' class='img-thumbnail' alt='...'>" + "<br>";

            layer.on({
                click: function (e) {
                    polygon.bindPopup(popupContent);
                },
                mouseover: function (e) {
                    polygon.bindTooltip(feature.properties.name);
                },
            });
        },
    });
    $.getJSON("{{ route('api.polygons') }}", function(data) {
        polygon.addData(data);
        map.addLayer(polygon);
    });
    /* GeoJSON Polygon */
    var admin = L.geoJson(null, {
        onEachFeature: function (feature, layer) {
            var popupContent = "Kab/Kota: " + feature.properties.kab_kota + "<br>" ;
           

            layer.on({
                click: function (e) {
                    polygon.bindPopup(popupContent);
                },
                mouseover: function (e) {
                    polygon.bindTooltip(feature.properties.kab_kota);
                },
            });
        },
    });
    $.getJSON("{{ route('api.fetch-batas-kabupaten') }}", function(data) {
        admin.addData(data);
        map.addLayer(admin);
    });
    //layer control
    var overlayMaps = {
        "Point": point,
        "Polyline": polyline,
        "Polygon": polygon,
        "Fire": markers,
        "Batas Kabupaten": admin
    };

    var layerControl = L.control.layers(null, overlayMaps, { collapsed: false }).addTo(map);

</script>
@endsection

