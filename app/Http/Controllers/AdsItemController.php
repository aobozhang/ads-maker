<?php

namespace App\Http\Controllers;

use App\Models\AdsItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdsItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return AdsItem::where('user_id', $request->user()->id)->paginate(30);
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

        $adsItem = $request->user()->ads_items()->create($data);

        return back()->with('upload', $adsItem);

    }

    /**
     * download the specified resource image.
     */
    public function down(AdsItem $adsItem)
    {
        \Log::info($adsItem);

        return response()->download($adsItem->path);
    }

    /**
     * Display the specified resource.
     */
    public function show(AdsItem $adsItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdsItem $adsItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdsItem $adsItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdsItem $adsItem)
    {
        $adsItem->delete();

        return back();
    }
}
