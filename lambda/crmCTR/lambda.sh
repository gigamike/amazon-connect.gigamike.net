#!/bin/bash
unlink Archive.zip
zip -r Archive.zip *

# Trigger
# Kinesis

# Configuration >> Permissions >> Execution role >> Attach Policy
# AWSLambdaKinesisExecutionRole

aws lambda update-function-code --function-name crmCTR --zip-file fileb://Archive.zip
