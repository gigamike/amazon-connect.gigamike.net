<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::all();

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'mobile_no' => 'required',
        ]);

        return redirect()->route('customers.index')->with('success', 'User created successfully.');
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
        $customer = Customer::findOrFail($id);

        return view('customers.edit', compact('customer'));
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

    public function ajaxIsCcpLoggedin(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = Auth::user();
            if (!$user->is_ccp_logged_in) {
                echo json_encode(['successful' => false]);
                return;
            }
        }

        echo json_encode(['successful' => true]);
        return;
    }

    public function windowOpenAmazonConnectStream()
    {
        $user = Auth::user();

        return view('customers.window_open_amazon_connect_stream', compact('user'));
    }

    public function ajaxAgentInit(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = Auth::user();

            if ($request->session()->get('user_amazon_connect_ccp_init')) {
                $request->session()->put('user_amazon_connect_ccp_init', true);

                echo json_encode(['successful' => true]);
                return;
            }

            $request->session()->put('key', 'value');
        }

        echo json_encode(['successful' => false]);
        return;
    }

    public function ajaxAgentStateChange(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = Auth::user();

            $oldState = $request['oldState'];
            $newState = $request['newState'];

            switch ($newState) {
                case 'Training':
                    $user->update([
                        'is_ccp_logged_in' => 1,
                        'ccp_status' => '',
                    ]);
                    echo json_encode(['successful' => true]);
                    return;
                    break;
                case 'Meeting':
                    $user->update([
                        'is_ccp_logged_in' => 1,
                        'ccp_status' => 'Meeting',
                    ]);
                    echo json_encode(['successful' => true]);
                    return;
                    break;
                case 'Break':
                    $user->update([
                        'is_ccp_logged_in' => 1,
                        'ccp_status' => 'Break',
                    ]);
                    echo json_encode(['successful' => true]);
                    return;
                    break;
                case 'Available':
                    $user->update([
                        'is_ccp_logged_in' => 1,
                        'ccp_status' => 'Available',
                    ]);
                    echo json_encode(['successful' => true]);
                    return;
                    break;
                case 'Offline':
                    $user->update([
                        'is_ccp_logged_in' => 1,
                        'ccp_status' => '',
                    ]);
                    echo json_encode(['successful' => true]);
                    return;
                    break;
                default:
            }
        }

        echo json_encode(['successful' => false]);
        return;
    }

    public function ajaxAgentUpdateCurrentStatus(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = Auth::user();

            $oldState = $request['oldState'];
            $newState = $request['newState'];

            $user->update([
                'ccp_status' => $newState,
            ]);
        }

        echo json_encode(['successful' => false]);
        return;
    }
}
