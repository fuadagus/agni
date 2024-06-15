<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use DateTime;

class FireData extends Model
{
    protected $table = 'firedata'; // Assuming your table name is 'fires'

    protected $fillable = [
        'brightness',
        'brightness_2',
        'geom',
        'scan',
        'track',
        'acq_datetime',
        'confidence',
        'frp'
    ];

  

    public function updateFireDataFromCsv($csvPath)
    {
        $batchSize = 100; // Number of records to insert at a time

        LazyCollection::make(function () use ($csvPath) {
            $handle = fopen($csvPath, 'r');
            while (($line = fgetcsv($handle)) !== FALSE) {
                yield $line;
            }
            fclose($handle);
        })->skip(1) // Skip the header row
            ->chunk($batchSize)
            ->each(function ($chunk) {
                DB::table('firedata')->upsert(
                    $chunk->map(function ($fireData, $index) {
                        

                        try {
                            // Assuming $dateString is the input date that needs to be corrected
                            $dateString = $fireData[8]; // This is just an example; replace it with the actual source of your date string
        
                            // Correcting the format
                            // This is a placeholder step - you'll need to replace it with actual logic to correct the format
                            $correctedDateString = preg_replace('/\b(\d)(\d{2})\b/', '$1:$2:00', $dateString);

                            // Creating a DateTime object
                            $dateTime = new DateTime($correctedDateString);

                            // Formatting the DateTime object to a string that's compatible with your database
                            $formattedDate = $dateTime->format('Y-m-d H:i:s');
                            return [
                                // 'generate id with incremental funtion from map index
                                'id' => $index,



                                'brightness' => $fireData[3],
                                'brightness_2' => $fireData[10],
                                'geom' => DB::raw("ST_GeomFromText('{$fireData[0]}')"),
                                'scan' => $fireData[4],
                                'track' => $fireData[5],
                                'created_at' => now(),
                                'updated_at' => now(),
                                'acq_datetime' => $fireData[8],
                                'confidence' => $fireData[9],
                                'frp' => $fireData[11]
                            ];

                            // Now you can use $formattedDate for database operations
                        } catch (Exception $e) {
                            // Handle exception if the date format is still incorrect
                            echo "Error formatting date: " . $e->getMessage();
                        }
                    })->toArray(),

                    ['id'], // Unique keys for upsert
                    ['geom', 'acq_datetime','created_at','updated_at', 'brightness', 'brightness_2', 'scan', 'track', 'confidence', 'frp'] // Columns to update
                );
            });
    }
}
