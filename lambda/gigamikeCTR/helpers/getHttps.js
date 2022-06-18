const https = require('https');

module.exports = function getHttps(host, query, headers) {
    return new Promise(((resolve, reject) => {
        let options = {
            host: host,
            port: 443,
            path: query,
            method: 'GET',
            headers: headers
        };

        const request = https.request(options, (response) => {
            response.setEncoding('utf8');
            let returnData = '';

            response.on('data', (chunk) => {
                returnData += chunk;
            });

            response.on('end', () => {
                resolve(JSON.parse(returnData));
            });

            response.on('error', (error) => {
                reject(error);
            });
        });

        request.end();
    }));
}