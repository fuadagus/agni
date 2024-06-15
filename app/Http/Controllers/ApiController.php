<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Brick\Geo\Point;
use Brick\Geo\IO\GeoJSONReader;
use Brick\Geo\IO\GeoJSONWriter;

class ApiController extends Controller
{
    public function fetchFiresData()
    {
        $reader = new GeoJSONReader();
        $wfsUrl = "https://firms.modaps.eosdis.nasa.gov/mapserver/wfs/SouthEast_Asia/b908b453e6302e29f9be4f5f5b5533b1/?SERVICE=WFS&REQUEST=GetFeature&VERSION=2.0.0&TYPENAME=ms:fires_noaa21_24hrs&STARTINDEX=0&COUNT=1000&SRSNAME=urn:ogc:def:crs:EPSG::4326&BBOX=-90,-180,90,180,urn:ogc:def:crs:EPSG::4326&outputformat=geojson";

        try {
            $response = Http::withoutVerifying()->get($wfsUrl);

            if ($response->successful()) {
                return $response->json();
            } else {
                return response()->json(['error' => 'Failed to fetch data from NASA FIRMS'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Exception occurred: ' . $e->getMessage()], 500);
        }
    }

    //api for getting batas_kabupaten http://localhost:8443/geoserver/pgwebl_responsi/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=pgwebl_responsi%3Abatas_kabupaten&maxFeatures=50&outputFormat=application%2Fjson
    public function fetchBatasKabupaten(){
        $wfsUrl = "http://localhost:8443/geoserver/pgwebl_responsi/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=pgwebl_responsi%3Abatas_kabupaten&outputFormat=application%2Fjson";

        try {
            $response = Http::withoutVerifying()->get($wfsUrl);

            if ($response->successful()) {
                return $response->json();
            } else {
                return response()->json(['error' => 'Failed to fetch data from NASA FIRMS'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Exception occurred: ' . $e->getMessage()], 500);
        }
    }

}

