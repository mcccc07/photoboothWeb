<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index()
    {
        return view('camera');
    }

    public function store(Request $request)
    {
        // Get base64 photo from JS
        $data = $request->input('photo');

        // Decode to raw image bytes
        $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));

        // Generate unique filename
        $filename = 'booth_' . time() . '.jpg';

        // Save to S3 and get S3 URL
        Storage::disk('s3')->put('photos/' . $filename, $image);
        $url = Storage::disk('s3')->url('photos/' . $filename);

        // 5. Return URL to browser
        return response()->json([
            'url' => $url
        ]);
    }

    public function download($file)
    {
        // Check if file exists on S3
        if (Storage::disk('s3')->exists('photos/' . $file)) {
            // Download directly from S3
            $contents = Storage::disk('s3')->get('photos/' . $file);

            return response($contents, 200, [
                'Content-Type'        => 'image/jpeg',
                'Content-Disposition' => 'attachment; filename="' . $file . '"',
            ]);
        }

        return response()->json(['error' => 'File not found.'], 404);
    }

    public function result()
    {
        return view('result');
    }
}
