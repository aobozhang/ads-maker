<?php

namespace App\Http\Controllers;

use App\Models\AdsImage;
use App\Models\AdsItem;
use Arr;
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
            'list'    => fn()    => AdsImage::where('user_id', $request->user()->id)->orderBy('updated_at', 'desc')->paginate(30),
            'favlist' => fn() => AdsImage::where('user_id', $request->user()->id)->where('status', '&', (1 << 0))->orderBy('updated_at', 'desc')->paginate(30),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return Inertia::render('AdsImage/CreateAndEdit', [
            'adsItems' => fn() => AdsItem::where('user_id', $request->user()->id)->paginate(30),
            'favItems' => fn() => AdsItem::where('user_id', $request->user()->id)->where('status', '&', (1 << 0))->paginate(30),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $name = Carbon::now()->format('Ymd-His-u') . "." . $request->file->extension();
        $path = $request->file('file')->storePubliclyAs(
            'ads-images',
            $name,
            'public'
        );

        if (!$request->file('file')->isValid()) {
            return back()->with('message', 'upload faild.');
        }

        $data = $request->except(['file']);

        $data['name'] = $name;
        $data['path'] = storage_path('app/public/' . $path);
        $data['url']  = asset('storage/' . $path);

        $adsImage = $request->user()->ads_images()->create($data);

        return redirect()->route('ads-image.show', $adsImage);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, AdsImage $adsImage)
    {
        return Inertia::render('AdsImage/CreateAndEdit', [
            'adsItems' => fn() => AdsItem::where('user_id', $request->user()->id)->paginate(30),
            'favItems' => fn() => AdsItem::where('user_id', $request->user()->id)->where('status', '&', (1 << 0))->paginate(30),
            'item'     => $adsImage,
        ]);
    }

    /**
     * download the specified resource image.
     */
    public function down(AdsImage $adsImage)
    {
        return response()->download($adsImage->path, $adsImage->name);
    }

    /**
     * fav the specified resource image.
     */
    public function fav(Request $request, AdsImage $adsImage)
    {
        $adsImage->status = \toggleNumAtPosition($adsImage->status, 0);
        $adsImage->save();
        return back();
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
        $name = Carbon::now()->format('Ymd-His-u') . "." . $request->file->extension();
        $path = $request->file('file')->storePubliclyAs(
            'ads-images',
            $name,
            'public'
        );

        $adsImage->name = $name;
        $adsImage->path = storage_path('app/public/' . $path);
        $adsImage->url  = asset('storage/' . $path);

        $adsImage->save();

        return back();
        // return redirect()->route('ads-image.show', $adsImage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdsImage $adsImage)
    {
        $adsImage->delete();

        return to_route('ads-image.index');
    }
}
