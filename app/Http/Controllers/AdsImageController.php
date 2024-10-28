<?php

namespace App\Http\Controllers;

use App\Models\AdsImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdsImageController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('AdsImage/Index', [
            'list' => fn() => AdsImage::orderBy('created_at', 'desc')->paginate(30),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('AdsImage/CreateAndEdit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $name = Carbon::now()->format('Ymd-His-u');
        $path = $request->file('file')->storePubliclyAs(
            'ads-images',
            $name . '.jpg',
            'public'
        );

        $data = $request->except(['file']);

        $data['name'] = $name;
        $data['path'] = storage_path('app/public/' . $path);
        $data['url']  = asset('storage/' . $path);

        if ($request->user()) {
            $data['user_id'] = $request->user()->id;
        }

        $adsImage = AdsImage::create($data);

        return $adsImage;
    }

    /**
     * Display the specified resource.
     */
    public function show(AdsImage $adsImage)
    {
        return Inertia::render('AdsImage/Index', [
            'list' => fn() => AdsImage::orderBy('created_at', 'desc')->paginate(30),
            'item' => $adsImage,
        ]);
    }

    /**
     * download the specified resource image.
     */
    public function down(AdsImage $adsImage)
    {
        return response()->download($adsImage->path);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdsImage $adsImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdsImage $adsImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdsImage $adsImage)
    {
        //
    }
}
