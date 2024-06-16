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
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-tilelayer-geojson/1.0.2/TileLayer.GeoJSON.min.js" integrity="sha512-R3sWhPfrUa7FVpXuNnv8+6xyG+/Lmv6UZb9x81qOHddiO4JDhQmzxvIhflBnUaA1jnyVnZIP/8NsSLyE+tKh7w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
    // Map initialization
    var map = L.map('map').setView([-0.789275, 113.921327], 5);
    var markers = L.markerClusterGroup();

    // Basemaps
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

    stadiaAlidadeSmoothDark.addTo(map);

    var baseMaps = {
        "Stadia Alidade Smooth Dark": stadiaAlidadeSmoothDark,
        "OpenStreetMap": openStreetMap,
        "Esri World Imagery": esriWorldImagery
    };

    L.control.layers(baseMaps).addTo(map);

    // Function to determine fill color based on temperature
    function getColorForTemperature(temperature) {
        if (temperature < 20) {
            return "#3498db"; // Blue
        } else if (temperature >= 20 && temperature < 30) {
            return "#f39c12"; // Orange
        } else {
            return "#e74c3c"; // Red
        }
    }

    // Layers
    var pointLayer = L.geoJson(null, {
        onEachFeature: function (feature, layer) {
            var popupContent = "Name: " + feature.properties.name + "<br>" +
                "Description: " + feature.properties.description + "<br>" +
                "Photo: <img src='{{ asset('storage/images/') }}/" + feature.properties.image + "' class='img-thumbnail' alt='...'>" + "<br>";
            layer.on({
                click: function (e) {
                    pointLayer.bindPopup(popupContent);
                },
                mouseover: function (e) {
                    pointLayer.bindTooltip(feature.properties.name);
                },
            });
        },
    });
    $.getJSON("{{ route('api.points') }}", function(data) {
        pointLayer.addData(data);
    });

    var firePointLayer = L.geoJson(null, {
        pointToLayer: function (feature, latlng) {
            var temperatureMedian = ((feature.properties.brightness_2 + feature.properties.brightness) / 2 - 273.15).toFixed(2);
            var confidenceLevel = feature.properties.confidence === 'n' ? "Normal" :
                feature.properties.confidence === 'l' ? "Rendah" :
                feature.properties.confidence === 'h' ? "Tinggi" : "Tidak diketahui";
            var markerOptions = {
                radius: 8,
                fillColor: getColorForTemperature(temperatureMedian),
                color: "#000",
                weight: 1,
                opacity: 1,
                fillOpacity: 0.8
            };
            var marker = L.circleMarker(latlng, markerOptions);
            var popupContent = "Temperatur Median: " + temperatureMedian + "°C" + "<br>" +
                "Daya radiasi: " + feature.properties.frp + " MW" + "<br>" +
                "Tanggal/Waktu: " + feature.properties.acq_datetime + "<br>" +
                "Kepercayaan: " + confidenceLevel;
            marker.bindPopup(popupContent);
            return marker;
        }
    });

    $.getJSON("{{ route('api.fetch-fires-data') }}", function(data) {
        firePointLayer.addData(data);
        markers.addLayer(firePointLayer);
    });
    map.addLayer(markers);
    var heatMapData = [];
    $.getJSON("{{ route('api.fetch-fires-data') }}", function(data) {
        data.features.forEach(function(feature) {
            if (feature.properties && feature.properties.brightness_2 && feature.properties.brightness) {
                var lat = feature.geometry.coordinates[1];
                var lng = feature.geometry.coordinates[0];
                var temperatureMedian = ((feature.properties.brightness_2 + feature.properties.brightness) / 2 - 273.15).toFixed(2);
                heatMapData.push([lat, lng, feature.properties.brightness]);
            }
        });
        var heat = L.heatLayer(heatMapData, {
            radius: 20,
            blur: 15,
            maxZoom: 18,
            gradient: { 0.4: 'blue', 0.65: 'lime', 1: 'red' }
        }).addTo(map);
        L.control.layers(null, { "Heatmap": heat }, { collapsed: false }).addTo(map);
    });

    var polylineLayer = L.geoJson(null, {
        onEachFeature: function (feature, layer) {
            var popupContent = "Nama: " + feature.properties.name + "<br>" +
                "Deskripsi: " + feature.properties.description + "<br>" +
                "Foto: <img src='{{ asset('storage/images/') }}/" + feature.properties.image + "' class='img-thumbnail' alt='...'>" + "<br>";
            layer.on({
                click: function (e) {
                    polylineLayer.bindPopup(popupContent);
                },
                mouseover: function (e) {
                    polylineLayer.bindTooltip(feature.properties.name);
                },
            });
        },
    });
    $.getJSON("{{ route('api.polylines') }}", function(data) {
        polylineLayer.addData(data);
    });

    var polygonLayer = L.geoJson(null, {
        onEachFeature: function (feature, layer) {
            var popupContent = "Nama: " + feature.properties.name + "<br>" +
                "Deskripsi: " + feature.properties.description + "<br>" +
                "Foto: <img src='{{ asset('storage/images/') }}/" + feature.properties.image + "' class='img-thumbnail' alt='...'>" + "<br>";
            layer.on({
                click: function (e) {
                    polygonLayer.bindPopup(popupContent);
                },
                mouseover: function (e) {
                    polygonLayer.bindTooltip(feature.properties.name);
                },
            });
        },
    });
    $.getJSON("{{ route('api.polygons') }}", function(data) {
        polygonLayer.addData(data);
    });

    // WMS layer
    var wmsLayer = L.tileLayer.wms("http://localhost:8443/geoserver/pgwebl_responsi/wms", {
        layers: 'pgwebl_responsi:batas_provinsi',
        format: 'image/png',
        transparent: true,
        attribution: "BIG",
        opacity: 0.5 
    }).addTo(map);

    // GetFeatureInfo URL
    function getFeatureInfoUrl(e) {
        var point = map.latLngToContainerPoint(e.latlng, map.getZoom());
        var size = map.getSize();
        var bounds = map.getBounds();
        var X = point.x;
        var Y = point.y;
        var WIDTH = size.x;
        var HEIGHT = size.y;
        var BBOX = bounds._southWest.lng + "," + bounds._southWest.lat + "," + bounds._northEast.lng + "," + bounds._northEast.lat;
        var url = wmsLayer._url + 
            "?SERVICE=WMS&VERSION=1.1.1&REQUEST=GetFeatureInfo" +
            "&LAYERS=pgwebl_responsi:batas_provinsi" +
            "&QUERY_LAYERS=pgwebl_responsi:batas_provinsi" +
            "&STYLES=&BBOX=" + BBOX + '&FEATURE_COUNT=5&HEIGHT=' + HEIGHT + '&WIDTH=' + WIDTH + '&FORMAT=image%2Fpng&INFO_FORMAT=application/json&SRS=EPSG%3A4326&' +
            "&X=" + Math.floor(point.x) + "&Y=" + Math.floor(point.y);
            // X + '&Y=' + Y;
        
            // echo the url of the wms layer
           console.log(url);
            

            return "{{ url('proxy.php') }}?url=" + encodeURIComponent(url);
    }

    // Click event to show popup
    map.on('click', function(e) {
        var url = getFeatureInfoUrl(e);

        $.ajax({
            url: url,
            success: function(data) {
               
                var feature = data.features[0];
                if (feature) {
                    var properties = feature.properties;
                    var popupContent = properties.provinsi;
                        // "<b>Temperature:</b> " + properties.brightness + " K<br>" +
                        // "<b>FRP:</b> " + properties.frp + " MW<br>" +
                        // "<b>Confidence:</b> " + properties.confidence;
                    L.popup()
                        .setLatLng(e.latlng)
                        .setContent(popupContent)
                        .openOn(map);
                } else {
                    L.popup()
                        .setLatLng(e.latlng)
                        .setContent("No information available")
                        .openOn(map);
                }
            }
            ,
            error: function() {
                L.popup()
                    .setLatLng(e.latlng)
                    .setContent("Error retrieving information")
                    .openOn(map);
            }
        });
    });
    var adminLayer = L.geoJson(null, {
        onEachFeature: function (feature, layer) {
            var popupContent = "Provinsi: " + feature.properties.provinsi + "<br>";
            layer.on({
                click: function (e) {
                    adminLayer.bindPopup(popupContent);
                },
                mouseover: function (e) {
                    adminLayer.bindTooltip(feature.properties.provinsi);
                },
            });
        },
    });
    $.getJSON("{{ route('api.fetch-batas-provinsi') }}", function(data) {
        adminLayer.addData(data);
    });

    // function updateLayersBasedOnZoom() {
    //     var currentZoom = map.getZoom();

    //     if (currentZoom >= 5) {
    //         if (!map.hasLayer(pointLayer)) map.addLayer(pointLayer);
    //         // if (!map.hasLayer(firePointLayer)) map.addLayer(firePointLayer);
    //         if (!map.hasLayer(polylineLayer)) map.addLayer(polylineLayer);
    //         if (!map.hasLayer(polygonLayer)) map.addLayer(polygonLayer);
    //         if (!map.hasLayer(adminLayer)) map.addLayer(adminLayer);
    //     } else {
    //         if (map.hasLayer(pointLayer)) map.removeLayer(pointLayer);
    //         // if (map.hasLayer(firePointLayer)) map.removeLayer(firePointLayer);
    //         if (map.hasLayer(polylineLayer)) map.removeLayer(polylineLayer);
    //         if (map.hasLayer(polygonLayer)) map.removeLayer(polygonLayer);
    //         if (map.hasLayer(adminLayer)) map.removeLayer(adminLayer);
    //     }
    // }

    // map.on('zoomend', updateLayersBasedOnZoom);
    // updateLayersBasedOnZoom(); // Initial call to set layers based on the initial zoom level

    var overlayMaps = {
        "Point": pointLayer,
        "Polyline": polylineLayer,
        "Polygon": polygonLayer,
        "Fire": markers,
        "Batas Provinsi": adminLayer,
        "Batas Provinsi WMS": wmsLayer,
    };

    L.control.layers(null, overlayMaps, { collapsed: false }).addTo(map);
</script>
@endsection
