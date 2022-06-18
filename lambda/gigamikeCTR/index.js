/*
 * In Amazon Connect, under data streaming, enable data streaming. Select Contact Trace Records then Kinesis Streams
 * Create a lambda and from your lambda add trogger Kinesis. Make sure the role of your lambda (Lambda function >> Configuration >> Permissions >> Execution role) has a AWSLambdaKinesisExecutionRole policy
 *
 * https://docs.aws.amazon.com/connect/latest/adminguide/data-streaming.html
 * https: //docs.aws.amazon.com/lambda/latest/dg/with-kinesis-example.html
 * https://docs.aws.amazon.com/connect/latest/adminguide/ctr-data-model.html#ctr-ContactTraceRecord
 *
 */

const aws = require("aws-sdk");

exports.handler = function(event, context, callback) {
    // console.log('Event Payload:', JSON.stringify(event));

    event.Records.forEach(function(record) {
        // Kinesis data is base64 encoded so decode here
        let payload = Buffer.from(record.kinesis.data, 'base64').toString('ascii');
        console.log('Decoded payload:', payload);

        payloadObj = JSON.parse(payload);
        let channel = payloadObj.Channel;
        let contactId = payloadObj.ContactId;
        let customerEndpointAddress = payloadObj.CustomerEndpoint.Address;
        let disconnectReason = payloadObj.DisconnectReason;

        // we need to check Agent, coz possible there is a conversation between Agent and Customer and Customer dropped the call
        let agent = payloadObj.Agent;
        let agentUsername = null;
        if (agent != null) {
            agentUsername = payloadObj.Agent.Username;
        }

        let recording = payloadObj.Recording;
        recordingLocation = null;
        if (recording != null) {
            recordingLocation = payloadObj.Recording.Location;
        }

        console.log('channel:', channel);
        console.log('contactId:', contactId);
        console.log('customerEndpointAddress:', customerEndpointAddress);
        console.log('disconnectReason:', disconnectReason);
        console.log('agent:', agent);
        console.log('agentUsername:', agentUsername);
        console.log('recording:', recording);
        console.log('recordingLocation:', recordingLocation);

        if (channel == 'VOICE') {
            const postHttps = require('./helpers/postHttps');

            const apiHost = process.env.API_HOST;

            console.log('apiHost', apiHost);

            (async () => {
                let query = '/api/contact-ctr/store';
                let data = JSON.stringify({
                    "contactId": contactId,
                    "customerEndpointAddress": customerEndpointAddress,
                    "disconnectReason": disconnectReason,
                    "agentUsername": agentUsername,
                    "recordingLocation": recordingLocation,
                    "kinesisPayload": payload
                });
                console.log('API Payload', data);
                let headers = {
                    'Content-Type': "application/json",
                };
                let response = await postHttps(apiHost, query, headers, data);
                if (!response.successful) {
                    console.log('Error CTR API');
                    console.log('response:', response);
                }
            })();
        }
    });
};
