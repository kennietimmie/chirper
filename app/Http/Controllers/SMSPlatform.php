<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SMSPlatform extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

        $validated = $request->validate([
            'sender_id' => 'required|string|min:3|max:11',
            'recipients' => 'required|regex:/^[0-9]+(\,\s*[0-9]+)/',
            'message' => 'required|string',
            'pages' => 'required|numeric',
        ]);

        $validated['recipients'] = preg_replace('/^([0]+)/', '234', $validated['recipients']);
        $validated['recipients'] = explode(',', preg_replace('/(\,\s*[0]+)/', ',234', $validated['recipients']));

        $amount = 0;
        $lines = File::lines(storage_path('PriceList.txt'));

         $lines->each(function(string $value) use($validated, &$amount) {
             if(strlen(trim($value))){
                $data = preg_split('/=/', $value);
               foreach($validated['recipients'] as $recipient){
                $first = collect($data)->first();
                if(preg_match("/^{$first}/", $recipient)){
                    $amount += ($validated["pages"] * collect($data)->last());
                    break;
                }
               }
            }
        });

        return back()->with('data', "pages: {$validated['pages']}, total: $amount");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
