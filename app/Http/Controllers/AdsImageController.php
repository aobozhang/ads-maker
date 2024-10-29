<?php

namespace App\Http\Controllers;

use App\Models\AdsImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Str;

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

        $adsImage = $request->user()->ads_images()->create($data);

        return redirect(route('ads-image.show', $adsImage));
    }

    /**
     * Display the specified resource.
     */
    public function show(AdsImage $adsImage)
    {
        return Inertia::render('AdsImage/CreateAndEdit', [
            'item' => $adsImage,
        ]);
    }

    /**
     * download the specified resource image.
     */
    public function down(AdsImage $adsImage, $redirect = false)
    {
        return response()->download($adsImage->path);
    }

    /**
     * upload file
     */
    public function upload(Request $request)
    {
        $name = Carbon::now()->format('Ymd-His-u');
        $path = $request->file('file')->storePubliclyAs(
            'ads-items',
            $name . '.jpg',
            'public'
        );
        $from = $request->input('from');
        $data = $request->except(['file', 'from']);

        $data['name'] = $name;
        $data['type'] = 'picture';
        $data['path'] = storage_path('app/public/' . $path);
        $data['url']  = asset('storage/' . $path);

        $adsImage = $request->user()->ads_items()->create($data);

        return back()->with('upload', $adsImage);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdsImage $adsImage)
    {
        return Inertia::render('AdsImage/CreateAndEdit', [
            'item' => $adsImage,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdsImage $adsImage)
    {
        $name = Carbon::now()->format('Ymd-His-u');
        $path = $request->file('file')->storePubliclyAs(
            'ads-images',
            $name . '.jpg',
            'public'
        );

        $adsImage->name = $name;
        $adsImage->path = storage_path('app/public/' . $path);
        $adsImage->url  = asset('storage/' . $path);

        $adsImage->save();

        return redirect(route('ads-image.show', $adsImage));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdsImage $adsImage)
    {
        $adsImage->destroy();

        return redirect(route('ads-image.index'));
    }
}
