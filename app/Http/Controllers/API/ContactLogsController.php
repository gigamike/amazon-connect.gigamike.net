<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\ContactLogs;
use Validator;

class ContactLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'contactId' => 'required',
            'customerEndpointAddress' => 'required',
            'systemEndpointAddress' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $customer = Customer::where('mobile_no', $request['customerEndpointAddress'])->first();
       
        $contactLogs = ContactLogs::create([
            'contact_id' => $request['contactId'],
            'customer_endpoint_address' => $request['customerEndpointAddress'],
            'system_endpoint_address' => 'systemEndpointAddress',
            'customer_id' => $customer->id,
        ]);

        return response()->json(['Contact logs created successfully.', $contactLogs]);
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
        //
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
