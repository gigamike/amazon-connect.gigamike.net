<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\ContactLogs;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;

use Aws\Connect;
use Aws\Exception\AwsException;
use Aws\Connect\Exception\ConnectException;
use Aws\Connect\ConnectClient;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
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
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        try {
            $client = new ConnectClient([
                'version' => 'latest',
                'region' => env('AWS_DEFAULT_REGION', null),
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID', null),
                    'secret' => env('AWS_SECRET_ACCESS_KEY', null),
                ],
            ]);

            list($firstName, $lastName) = explode(' ', $request['name']);

            $result = $client->createUser([
                // 'DirectoryUserId' => '<string>',
                // 'HierarchyGroupId' => '<string>',
                'IdentityInfo' => [
                    'Email' => $request['email'],
                    'FirstName' => $firstName,
                    'LastName' => $lastName,
                ],
                'InstanceId' => env('AWS_AMAZON_CONNECT_INSTANCE_ID', null), // REQUIRED
                'Password' => $request['password'], // At least 8 characters with an uppercase letter, a lowercase letter, and a number.
                'PhoneConfig' => [// REQUIRED
                    // 'AfterContactWorkTimeLimit' => <integer>,
                    // 'AutoAccept' => true || false,
                    // 'DeskPhoneNumber' => '<string>',
                    'PhoneType' => 'SOFT_PHONE', // REQUIRED
                ],
                'RoutingProfileId' => '36b2b7bd-630d-481c-853e-e5e295f8b2b8', // REQUIRED
                'SecurityProfileIds' => ['c4a4c478-3adb-46c0-a3b8-dfb442b4c49c'], // REQUIRED
                // 'Tags' => [],
                'Username' => $request['email'], // REQUIRED // Use up to 64 characters, a-z, A-Z, 0-9, _ - . @
            ]);

            User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'role' => 'agent',
                'password' => Hash::make($request['password']),
            ]);
        } catch (ConnectException $e) {
            // echo "getAwsRequestId:" . $e->getAwsRequestId() . "\n";
            // echo "getAwsErrorType:" . $e->getAwsErrorType() . "\n";
            // echo "getAwsErrorCode:" . $e->getAwsErrorCode() . "\n";
            // echo "getMessag:" . $e->getMessage();
            return;
        } catch (AwsException $e) {
            // echo "getAwsRequestId:" . $e->getAwsRequestId() . "\n";
            // echo "getAwsErrorType:" . $e->getAwsErrorType() . "\n";
            // echo "getAwsErrorCode:" . $e->getAwsErrorCode() . "\n";
            // echo "getMessag:" . $e->getMessage();
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
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

        return view('users.window_open_amazon_connect_stream', compact('user'));
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
                        'ccp_status' => 'Training',
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
                        'ccp_status' => 'Offline',
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

            echo json_encode(['successful' => true]);
            return;
        }

        echo json_encode(['successful' => false]);
        return;
    }

    public function ajaxAgentStatusLogout(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = Auth::user();
            $user->update([
                'is_ccp_logged_in' => null,
            ]);

            Auth::logout();

            echo json_encode(['successful' => true]);
            return;
        }

        echo json_encode(['successful' => false]);
        return;
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->update([
            'is_ccp_logged_in' => null,
        ]);

        Auth::logout();
        return redirect('/');
    }

    public function ajaxOpenNewWindow(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = Auth::user();
            
            $contactId = $request['contactId'];
            
            echo json_encode(['successful' => true]);
            return;
        }

        echo json_encode(['successful' => false]);
        return;
    }

    public function ajaxDisplayStatus(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = Auth::user();

            $contactId = $request['contactId'];


            echo json_encode(['successful' => true]);
            return;
        }

        echo json_encode(['successful' => false]);
        return;
    }
}
