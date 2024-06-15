<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FireData; // Ensure this uses your actual model
use Illuminate\Support\Facades\Storage; // Add this line to import the Storage class

class UpdateFireData extends Command
{
    protected $signature = 'firedata:update';
    protected $description = 'Updates fire data from a remote CSV file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
{
    $this->info('Starting to update fire data...');
    $this->logMemoryUsage('Before processing data');

    $csvUrl = 'https://firms.modaps.eosdis.nasa.gov/mapserver/wfs/SouthEast_Asia/b908b453e6302e29f9be4f5f5b5533b1/?SERVICE=WFS&REQUEST=GetFeature&VERSION=2.0.0&TYPENAME=ms:fires_noaa21_24hrs&STARTINDEX=0&COUNT=1000&SRSNAME=urn:ogc:def:crs:EPSG::4326&BBOX=-90,-180,90,180,urn:ogc:def:crs:EPSG::4326&outputformat=csv';
    $storagePath = 'public/storage/fire_data.csv'; // Define the storage path

    // Ensure the directory exists
    Storage::makeDirectory(dirname($storagePath));

    // Download the CSV file and save it to the specified path
    Storage::put($storagePath, fopen($csvUrl, 'r'));

    if (Storage::exists($storagePath)) {
        $fireData = new FireData();
        $fireData->updateFireDataFromCsv(storage_path('app/'.$storagePath)); // Adjust the path for the method

        $this->logMemoryUsage('After processing data');

        // Optionally, delete the file after processing
        Storage::delete($storagePath);
    } else {
        $this->error('Failed to download the CSV file.');
    }
}

    protected function logMemoryUsage($message)
    {
        // Implement memory logging logic here
        $this->info($message);
    }
}