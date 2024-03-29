<?php

namespace App\Http\Controllers;

use App\Services\ParkingTransactionService;
use Illuminate\Http\Request;

class ParkingTransactionController extends Controller
{
    protected $request;
    protected $service;

    public function __construct(Request $request, ParkingTransactionService $service)
    {
        $this->request = $request;
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = $this->service->getList();
        return response()->json([
            "message" => $response->message,
            "data" => $response->data
        ], $response->status);
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
        $this->validate($request, [
            "vehicle.plate_no" => "required|string|validateAlreadyParked",
            "vehicle.type" => "required|integer|between:0,2|validateVehicleType",
            "date" => "required",
            "time" => "required",
            "use_server_time" => "required",
        ]);

        $data = $request->all();
        $response = $this->service->create($data);
        return response()->json([
            "message" => $response->message,
            "data" => $response->data
        ], $response->status);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            "date" => "required",
            "time" => "required",
            "use_server_time" => "required",
        ]);

        $response = $this->service->unpark($id, $request->all());
        return response()->json([
            "message" => $response->message,
            "data" => $response->data
        ], $response->status);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
