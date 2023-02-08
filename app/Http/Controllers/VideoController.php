<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreVideoRequest;
use Illuminate\Support\Facades\Redirect;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        // $videos = Auth::user()?->videos;
        $videos = User::find(2)?->videos;

        return inertia('logiciel', [
            'videos' => $videos,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreVideoRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreVideoRequest $request)
    {
        $file = $request->file('video');
        $extension = $file->getClientOriginalExtension();
        $size = $file->getSize();

        $allowedExtension = ['mp4', 'mov', 'avi'];
        if(! in_array($extension, $allowedExtension)) {
            return redirect()->back()->with([
                'error' => __('client.error.videoExtensionNotSupported')
            ]);
        }

        $fileName = time().'.'.$extension;
        $file->move(public_path('upload'), $fileName); 
        
        // Store in database
        $video = Video::updateOrCreate([
                'user_id' => 2,
                'url' => $fileName
            ], [
                'size' => $size
            ]
        );
        $statusCode = $video->wasRecentlyCreated ? 201 : 200;

        return redirect()->back()->with([
            'success' => __('client.success.videoStored')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $video = Video::firstWhere('id', $request->video['id']);

        // Delete video
        if(! $video) {
            return redirect()->back()->with([
                'error' => __('client.success.errorOnVideoDeletion')
            ]);
        }

        // Delete video
        $video->delete();
        return redirect()->back()->with([
            'success' => __('client.success.videoDeleted')
        ]);
    }
}
