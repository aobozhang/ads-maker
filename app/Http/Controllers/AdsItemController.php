<?php

namespace App\Http\Controllers;

use App\Models\AdsItem;
use Arr;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdsItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return AdsItem::where('user_id', $request->user()->id)
            ->with('user')->paginate(30);
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
        $name  = Carbon::now()->format('Ymd-His-u');
        $refer = $request->input('refer');
        $data  = $request->except(['file']);

        if ($request->has('url')) {

            $url = $request->input('url');

            $try = AdsItem::where('url', $url)
                ->orWhere('remote', $url)
                ->first();

            if ($try) {
                return redirect($refer)->with('upload', $try);
            }

            $data['remote'] = $url;

        }

        if ($request->hasFile('file')) {

            $file     = $request->file('file');
            $hash     = hash_file('sha256', $file);
            $filename = $hash . "." . ($request->file->extension() ?? 'jpg');
            $path     = 'ads-items/' . $filename;

            if (file_exists(storage_path('app/public/' . $path))) {
                $adsItem = $request->user()->ads_items()->firstOrCreate(
                    ['path' => storage_path('app/public/' . $path)],
                    [
                        'url'  => asset('storage/' . $path),
                        'name' => $name,
                        'type' => 'picture',
                    ]
                );

                return redirect($refer)->with('upload', $adsItem);
            }

            $path = $request->file('file')->storePubliclyAs(
                'ads-items',
                $filename,
                'public'
            );

            $data['path'] = storage_path('app/public/' . $path);

            $data['url']  = asset('storage/' . $path);
            $data['name'] = $name;
            $data['type'] = 'picture';

            $adsItem = $request->user()->ads_items()->create($data);

            return redirect($refer)->with('upload', $adsItem);
        } else {
            return response(null, 500);
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
        return response()->download($adsItem->path);
    }

    /**
     * fav the specified resource image.
     */
    public function fav(Request $request, AdsItem $adsItem)
    {
        // 更新时间戳的方案
        // $adsItem->update(
        //     ['status' => \toggleNumAtPosition($adsItem->status, 0)]
        // );

        // 不更新时间戳的方案
        \DB::table('ads_items')
            ->where('id', $adsItem->id)
            ->update([
                'status' => \toggleNumAtPosition($adsItem->status, 0),
            ]);

        // return back();
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
