/*
 * https://docs.aws.amazon.com/connect/latest/adminguide/contact-events.html
 * https://docs.aws.amazon.com/connect/latest/adminguide/ctr-data-model.html
 *
 * 1. Create a rule under EventBridge
 * https://aws.amazon.com/eventbridge/pricing/
 * Invocations $0.24 per million
 *
 * Please note Amazon Connect Eventbridge is per AWS REGION, meaning all CRM Regions and app instance i.e. demo|development|production|testing|uat are included
 *
 * 
 */
exports.handler = function(event, context) {
    console.log('Event Payload:', JSON.stringify(event));

    let eventPayload = JSON.parse(JSON.stringify(event));
    let eventType = eventPayload.detail.eventType; // INITIATED, QUEUED, CONNECTED_TO_AGENT, DISCONNECTED
    let contactId = eventPayload.detail.contactId; // Unique ID per call
    let channel = eventPayload.detail.channel; // VOICE
    let initiationMethod = eventPayload.detail.initiationMethod; // INBOUND, OUTBOUND, TRANSFER, CALLBACK, API, QUEUE_TRANSFER, DISCONNECT
    // let initiationTimestamp = eventPayload.detail.initiationTimestamp; // Event timestamp
    let amazonConnectInstacneArn = eventPayload.detail.instanceArn; // Amazon Connect Instance ARN example arn:aws:connect:us-west-2:667541373966:instance/ef9de430-c3bc-4ca0-9cd1-ffa6e1a32247
    let region = eventPayload.region;

    console.log('Event Type:', eventType);
    console.log('contactId:', contactId);
    console.log('channel:', channel);
    console.log('initiationMethod:', initiationMethod);
    // console.log('initiationTimestamp:', initiationTimestamp);

    if (channel == 'VOICE') {
        const postHttps = require('./helpers/postHttps');

        const apiHost = process.env.API_HOST;

        console.log('apiHost', apiHost);

        if (contactId && eventType && apiHost) {
            if (apiHost != 'false') {
                // we can set apiHost to false when it is inactive
                (async () => {
                    // request for API token
                    let query = '/api/contact-events/store';
                    let data = JSON.stringify({
                        "contactId": contactId,
                        "eventType": eventType
                    });
                    console.log('API Payload', data);
                    let headers = {
                        'Content-Type': "application/json",
                    };
                    let response = await postHttps(apiHost, query, headers, data);
                    if (!response.successful) {
                        console.log('Error CONTACT EVENTS API');
                        console.log('response:', response);
                    }
                })();
            }

        }
    }
};
