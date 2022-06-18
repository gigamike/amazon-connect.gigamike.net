#!/bin/bash
unlink Archive.zip
zip -r Archive.zip *

# Trigger
# EventBridge

aws lambda update-function-code --function-name gigamikeContactEvents --zip-file fileb://Archive.zip
