<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Aws\Connect;
use Aws\Exception\AwsException;
use Aws\Connect\Exception\ConnectException;
use Aws\Connect\ConnectClient;

class ContactUsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('contactus');
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
            'phone' => 'required',
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

            // https://docs.aws.amazon.com/connect/latest/adminguide/transfer-to-agent.html
            $result = $client->startOutboundVoiceContact([
                // 'AnswerMachineDetectionConfig' => [
                //     'AwaitAnswerMachinePrompt' => false,
                //     'EnableAnswerMachineDetection' => false,
                // ],
                // 'Attributes' => [], // can pass name here
                // 'CampaignId' => '<string>',
                // 'ClientToken' => '<string>',
                'ContactFlowId' => env('AWS_AMAZON_CONNECT_CONTACTFLOW_ID', null), // REQUIRED
                'DestinationPhoneNumber' => $request['phone'], // REQUIRED
                'InstanceId' => env('AWS_AMAZON_CONNECT_INSTANCE_ID', null), // REQUIRED
                'QueueId' => env('AWS_AMAZON_CONNECT_QUEUE_ID', null),
                // 'SourcePhoneNumber' => '<string>',
                // 'TrafficType' => 'GENERAL|CAMPAIGN',
            ]);
            // print_r($result);

            return redirect()->route('contactus.index')->with('success', 'Thank you, we will call you shortly.');
        } catch (ConnectException $e) {
            echo "getAwsRequestId:" . $e->getAwsRequestId() . "\n";
            echo "getAwsErrorType:" . $e->getAwsErrorType() . "\n";
            echo "getAwsErrorCode:" . $e->getAwsErrorCode() . "\n";
            echo "getMessag:" . $e->getMessage();
            return;
        } catch (AwsException $e) {
            echo "getAwsRequestId:" . $e->getAwsRequestId() . "\n";
            echo "getAwsErrorType:" . $e->getAwsErrorType() . "\n";
            echo "getAwsErrorCode:" . $e->getAwsErrorCode() . "\n";
            echo "getMessag:" . $e->getMessage();
        }
    }
}
