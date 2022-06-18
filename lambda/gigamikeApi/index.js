/*
 *
 * https://docs.aws.amazon.com/connect/latest/adminguide/connect-lambda-functions.html
 * https://aws.amazon.com/premiumsupport/knowledge-center/connect-change-default-queue-message/
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
        const apiUser = process.env.API_USER;
        const apiPassword = process.env.API_PASSWORD;

        console.log('apiHost', apiHost);

        (async () => {
            let query = '/api/contact-api/store';
            let data = JSON.stringify({
                "contactId": contactId,
                "customerEndpointAddress": customerEndpointAddress,
                "systemEndpointAddress": systemEndpointAddress
            });
            console.log('API Payload', data);
            let headers = {
                'Content-Type': "application/json",
            };
            let response = await postHttps(apiHost, query, headers, data);
            if (!response.successful) {
                console.log('Error PLAY API');
                console.log('response', response);
            }

            let resultMap = {
                Message: response.Message,
                CustomerEndpointAddress: response.CustomerEndpointAddress,
                SystemEndpointAddress: response.systemEndpointAddress,
                ContactId: response.ContactId,
                CustomerId: response.CustomerId,
                CustomerFirstName: response.CustomerFirstName
            };

            callback(null, resultMap);
        })();
    }
};
