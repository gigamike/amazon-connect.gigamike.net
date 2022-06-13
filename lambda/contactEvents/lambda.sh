#!/bin/bash
unlink Archive.zip
zip -r Archive.zip *

# Trigger
# EventBridge

# Tags Format
# Utilihub-Instance	us
# Utilihub-Environment demo|development|production|testing|uat

# US
aws lambda update-function-code --function-name contactEvents --zip-file fileb://Archive.zip
