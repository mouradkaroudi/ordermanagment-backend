<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //return ['file' => $request->file('file')];
        
        $this->validate($request, [
            'file' => 'mimes:png,jpg,jpeg,webp,excel,xlsx|max:2048'
        ]);

        $file = $request->file('file');
        //return ['file' => $file];
        $name = $file->getClientOriginalName();
        $path = $file->storePublicly('public');

        $path = str_replace('public/', 'storage/', $path);

        if(!$path) {
            return response()->json([
                "Unexpcted error during upload procces. please try again"
            ],400);
        }

        $file = File::create([
            "storage_type" => "local",
            "resource" => $path,
            "display_name" => $name
        ]);
        
        // Add resource url to the response
        $file["resource_url"] = asset($path);

        return $file;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        return response()->json($file);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        //
    }
}
