/*
 *
 * https://docs.aws.amazon.com/connect/latest/adminguide/set-working-queue.html
 * https://docs.aws.amazon.com/connect/latest/adminguide/connect-attrib-list.html
 * https://docs.aws.amazon.com/connect/latest/adminguide/attribs-with-lambda.html
 * 
 */
exports.handler = function(event, context, callback) {
    console.log('Event Payload:', JSON.stringify(event));

    let eventPayload = JSON.parse(JSON.stringify(event));
    let contactId = eventPayload.Details.ContactData.ContactId; // Unique ID per call
    let channel = eventPayload.Details.ContactData.Channel; // VOICE
    let customerEndpointAddress = eventPayload.Details.ContactData.CustomerEndpoint.Address;
    let systemEndpointAddress = eventPayload.Details.ContactData.SystemEndpoint.Address;

    console.log('contactId:', contactId);
    console.log('channel:', channel);
    console.log('customerEndpointAddress:', customerEndpointAddress);
    console.log('systemEndpointAddress:', systemEndpointAddress);

    if (channel == 'VOICE') {
        const postHttps = require('./helpers/postHttps');

        const apiHost = process.env.API_HOST;
        const apiAccessToken = process.env.API_ACCESS_TOKEN;

        console.log('apiHost', apiHost);

        (async () => {
            let query = '/api/contact-logs';
            let data = JSON.stringify({
                "contactId": contactId,
                "customerEndpointAddress": customerEndpointAddress,
                "systemEndpointAddress": systemEndpointAddress
            });
            console.log('API Payload', data);
            let headers = {
                'Content-Type': "application/json",
                'Authorization': "Bearer " + apiAccessToken,
            };
            let response = await postHttps(apiHost, query, headers, data);
            if (!response.successful) {
                console.log('Error INBOUND APPLICATION API');
                console.log('response', response);
            }

            let resultMap = {
                CustomerEndpointAddress: response.CustomerEndpointAddress,
                SystemEndpointAddress: response.SystemEndpointAddress,
                ContactId: response.ContactId,
                CustomerFirstName: response.CustomerFirstName
            };

            callback(null, resultMap);
        })();
    }
};
