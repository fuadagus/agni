@extends('layouts.template')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
<style>
    html,
    body {
        height: 100%;
        width: 100%;
        overflow: hidden;
        margin:0;
        /* Hide scrollbars */
    }

  
</style>
@endsection

@section('content')
<div id="map" style="width: 100vw; height: 87vh; margin: 0"></div>
@endsection
<!-- Modal Create Point -->
<div class="modal fade" id="PointModal" tabindex="-1" aria-labelledby="PointModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="PointModalLabel">Create Point</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('store-point') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Fill point name">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="geom" class="form-label">Geomerty</label>
                        <textarea class="form-control" id="geom_point" name="geom" rows="1" readonly></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image_point" name="image"
                            onchange="document.getElementById('preview-image-point').src = window.URL.createObjectURL(this.files[0])">
                    </div>
                    <div class="mb-3">
                        <img src="" alt="Preview" id="preview-image-point" class="img-thumbnail" width="400">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Create Polyline -->
<div class="modal fade" id="PolylineModal" tabindex="-1" aria-labelledby="PolylineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="PolylineModalLabel">Create Polyline</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('store-polyline') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Fill point name">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="geom" class="form-label">Geomerty</label>
                        <textarea class="form-control" id="geom_polyline" name="geom" rows="1" readonly></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image_polyline" name="image"
                            onchange="document.getElementById('preview-image-polyline').src = window.URL.createObjectURL(this.files[0])">
                    </div>
                    <div class="mb-3">
                        <img src="" alt="Preview" id="preview-image-polyline" class="img-thumbnail" width="400">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Create Polygon -->
<div class="modal fade" id="PolygonModal" tabindex="-1" aria-labelledby="PolygonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="PolygonModalLabel">Create Polygon</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('store-polygon') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Fill point name">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="geom" class="form-label">Geomerty</label>
                        <textarea class="form-control" id="geom_polygon" name="geom" rows="1" readonly></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image_polygon" name="image"
                            onchange="document.getElementById('preview-image-polygon').src = window.URL.createObjectURL(this.files[0])">
                    </div>
                    <div class="mb-3">
                        <img src="" alt="Preview" id="preview-image-polygon" class="img-thumbnail" width="400">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://unpkg.com/terraformer@1.0.7/terraformer.js"></script>
<script src="https://unpkg.com/terraformer-wkt-parser@1.1.2/terraformer-wkt-parser.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- <script src="[path to js]/L.Geoserver.js"></script> -->
<!-- Leaflet CSS -->

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




    /* Digitize Function */
    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    var drawControl = new L.Control.Draw({
        draw: {
            position: 'topleft',
            polyline: true,
            polygon: true,
            rectangle: true,
            circle: false,
            marker: true,
            circlemarker: false
        },
        edit: false
    });

    map.addControl(drawControl);

    map.on('draw:created', function (e) {
        var type = e.layerType,
            layer = e.layer;

        console.log(type);

        var drawnJSONObject = layer.toGeoJSON();
        var objectGeometry = Terraformer.WKT.convert(drawnJSONObject.geometry);

        console.log(drawnJSONObject);
        console.log(objectGeometry);

        if (type === 'polyline') {
            $("#geom_polyline").val(objectGeometry);
            $("#PolylineModal").modal('show');
        } else if (type === 'polygon' || type === 'rectangle') {
            $("#geom_polygon").val(objectGeometry);
            $("#PolygonModal").modal('show');
        } else if (type === 'marker') {
            $("#geom_point").val(objectGeometry);
            $("#PointModal").modal('show');
        } else {
            console.log('undefined');
        }

        drawnItems.addLayer(layer);
    });
    var firePoint = L.geoJson(null, {
        onEachFeature: function (feature, layer) {
            var popupContent = "Temperatur IF4: " + feature.properties.brightness + "<br>" +
                "Daya radiasi: " + feature.properties.frp + "MW" + "<br>" +
                "Tanggal/Waktu: " + feature.properties.acq_datetime + "<br>" +
                "Kepercayaan: " + feature.properties.confidence;

            layer.bindPopup(popupContent);

            layer.on({
                mouseover: function (e) {
                    layer.bindTooltip(feature.properties.latitude);
                }
            });
        }
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
        data.features.forEach(function (feature) {
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
    /* GeoJSON Polyline */
    var polyline = L.geoJson(null, {
        onEachFeature: function (feature, layer) {
            var popupContent = "Name: " + feature.properties.name + "<br>" +
                "Description: " + feature.properties.description + "<br>" +
                "Photo: <img src='{{ asset('storage/images/') }}/" + feature.properties.image +
                "' class='img-thumbnail' alt='...'>" + "<br>" +

                "<div class='d-flex flex-row mt-3'>" +
                "<a href='{{ url('edit-polyline') }}/" + feature.properties.id + "' class='btn btn-sm btn-warning me-2'><i class='fa-solid fa-edit'></i></a>" +
                
                "
                <form action='{{ url('delete-polyline') }}/" + feature.properties.id + "' method='POST'>" +
                '{{ csrf_field() }}' +
                '{{ method_field('DELETE') }}' +

                "<button type='submit' class='btn btn-danger' onclick='return confirm(`Yakin nih dihapus?`)'><i class='fa-solid fa-trash-can'></i></button>" +
                "</form>" +
                "</div>";

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
            var popupContent = "Name: " + feature.properties.name + "<br>" +
                "Description: " + feature.properties.description + "<br>" +
                "Photo: <img src='{{ asset('storage/images/') }}/" + feature.properties.image +
                "' class='img-thumbnail' alt='...'>" + "<br>" +

                "<div class='d-flex flex-row mt-3'>" +
                "<a href='{{ url('edit-polygon') }}/" + feature.properties.id + "' class='btn btn-sm btn-warning me-2'><i class='fa-solid fa-edit'></i></a>" +

                "<form action='{{ url('delete-polygon') }}/" + feature.properties.id + "' method='POST'>" +
                '{{ csrf_field() }}' +
                '{{ method_field('DELETE') }}' +

                "<button type='submit' class='btn btn-danger' onclick='return confirm(`Yakin nih dihapus?`)'><i class='fa-solid fa-trash-can'></i></button>" +
                "</form>" +
                "</div>";

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

    /* Layer Control */
    var overlayMaps = {
        "Validasi": point,
        "Polylines": polyline,
        "Polygons": polygon
    };
    var layerControl = L.control.layers(null, overlayMaps, {
        collapsed: false
    }).addTo(map);
</script>
@endsection
