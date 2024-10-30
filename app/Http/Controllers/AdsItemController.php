<?php

namespace App\Http\Controllers;

use App\Models\AdsItem;
use Carbon\Carbon;
use GuzzleHttp\Client;
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
        $data = $request->except(['file']);

        if ($request->hasFile('file')) {

            $name .= "." . $request->file->extension();

            $path = $request->file('file')->storePubliclyAs(
                'ads-items',
                $name,
                'public'
            );

            $data['path'] = storage_path('app/public/' . $path);
            $data['url']  = asset('storage/' . $path);
            $data['name'] = $name;
            $data['type'] = 'picture';

            $adsItem = $request->user()->ads_items()->create($data);
            return back()->with('upload', $adsItem);
        } else {
            return back();
        }

    }

    protected function downRemoteFile($url, $path = null)
    {
        if (!$path) {
            $name = Carbon::now()->format('Ymd-His-u');
            $path = storage_path('app/public/common' . $name);
        }

        $client = new Client();

        $headers = [
            'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept'          => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.9',
            'Referer'         => 'https://www.midjourney.com/', // 根据实际情况调整 Referer
        ];

        $response = $client->get($url, ['headers' => $headers]);

        if ($response->getStatusCode() === 200) {
            $fileContent = $response->getBody()->getContents();
            file_put_contents($path, $fileContent);
        }

        return [
            'status' => $response->getStatusCode(),
            'path'   => $path,
        ];
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
