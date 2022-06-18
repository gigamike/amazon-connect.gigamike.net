const https = require('https');

module.exports = function postHttps(host, query, headers, data) {
    return new Promise(((resolve, reject) => {
        let options = {
            host: host,
            port: 443,
            path: query,
            method: 'POST',
            headers: headers
        };

        const request = https.request(options, (response) => {
            response.setEncoding('utf8');
            let returnData = '';

            response.on('data', (chunk) => {
                returnData += chunk;
            });

            response.on('data', (d) => {
                process.stdout.write(d)
            })

            response.on('end', () => {
                resolve(JSON.parse(returnData));
            });

            request.on('error', (error) => {
                console.error(error)
            })
        });

        request.write(data);
        request.end();
    }));
}
