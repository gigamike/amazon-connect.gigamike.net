#!/bin/bash
unlink Archive.zip
zip -r Archive.zip *

aws lambda update-function-code --function-name gigamikeOutbound --zip-file fileb://Archive.zip
