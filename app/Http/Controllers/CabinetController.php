<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
class CabinetController extends Controller
{
     
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cab =  DB::table('cabinets')//->where('cabinets.id', 1)->select("cabinets.*", "folders.cabinet_id")
        //->join('folders', 'folders.cabinet_id', '=', 'cabinets.id')
        ->get();
    return response()->json($cab);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:cabinets',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        } else {
            Cabinet::create($request->all());
            return response()->json('cabinet success');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cabinet  $cabinet
     * @return \Illuminate\Http\Response
     */
    public function show($cabinet_id)
    {
        $cab =  DB::table('cabinets')->where('cabinets.id', $cabinet_id)
        ->get();
    return response()->json($cab);
    }
    public function cabinetName($name)
    {
        $cab =  DB::table('cabinets')->where('cabinets.name', $name)
        ->get();
    return response()->json($cab);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cabinet  $cabinet
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cab = Cabinet::where('id',$id)->first();
        return response()->json($cab);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cabinet  $cabinet
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $input = $request->all();                     
      $cab = Cabinet::find($id);
        $cab->name =  $input['name'];
        $cab->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cabinet  $cabinet
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Cabinet::where('id',$id)->delete();

        return response()->json('The folder successfully deleted');
    }
}
