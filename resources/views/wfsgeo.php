<?php
	$wfsUrl = file_get_contents("https://firms.modaps.eosdis.nasa.gov/mapserver/wfs/SouthEast_Asia/b908b453e6302e29f9be4f5f5b5533b1/?SERVICE=WFS&REQUEST=GetFeature&VERSION=2.0.0&TYPENAME=ms:fires_noaa21_24hrs&STARTINDEX=0&COUNT=1000&SRSNAME=urn:ogc:def:crs:EPSG::4326&BBOX=-90,-180,90,180,urn:ogc:def:crs:EPSG::4326&outputformat=geojson");

	header('Content-type: application/json');
	echo($wfsUrl);
	# Jika terdapat &maxFeatures=50 pada url wfs geojson, dihapus supaya jumlah feature tidak dibatasi
?>