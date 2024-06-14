<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;



class MapController extends Controller
{
    public function index()
    {
        $data = [
            "title" => "AGNI",
        ];

        //Check if user is login
        if (auth()->check() ) {
            return view('index', $data);
        } else{
            return view('index-public', $data);
        }
    }
    public function monitoring()
    {
        $data = [
            "title" => "AGNI",
        ];

        //Check if user is login
        if (auth()->check() ) {
            return view('monitoring', $data);
        } else{
            return view('index-public', $data);
        }
    }
    public function table()
    {
        $data = [
            "title" => "Table",
        ];
        return view('table', $data);
    }


public function uploadShapefile(Request $request)
{
    // Validate the uploaded file
    $request->validate([
        'shapefile' => 'required|mimes:zip'
    ]);

    // Get the uploaded file
    $file = $request->file('shapefile');

    // Generate a unique file name
    $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

    // Move the uploaded file to a storage directory
    $file->storeAs('shapefiles', $fileName);

    // Process the shapefile and save it to the database
    // TODO: Implement the shapefile processing logic here

    // Return a response
    return response()->json(['message' => 'Shapefile uploaded successfully']);
}

public function deleteShapefile($fileName)
{
    // Delete the shapefile from the storage directory
    Storage::delete('shapefiles/' . $fileName);

    // TODO: Delete the shapefile data from the database


    // Return a response
    return response()->json(['message' => 'Shapefile deleted successfully']);
}

public function downloadShapefile($fileName)
{
    // Check if the shapefile exists in the storage directory
    if (!Storage::exists('shapefiles/'. $fileName)) {
        return response()->json(['message' => 'Shapefile not found'], 404);
    }

    // Get the shapefile from the storage directory
    $file = Storage::get('shapefiles/'. $fileName);

    // Return the shapefile as a downloadable file
    return response($file, 200)->header('Content-Type', 'application/zip')
        ->header('Content-Disposition', 'attachment; filename="'. $fileName. '"');
    
}
}