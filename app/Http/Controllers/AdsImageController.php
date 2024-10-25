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
            'data' => fn() => AdsImage::orderBy('created_at', 'desc')->paginate(30),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        $data['path'] = asset('storage/' . $path);

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
            'data'     => fn()     => AdsImage::orderBy('created_at', 'desc')->paginate(30),
            'adsImage' => $adsImage,
        ]);
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
