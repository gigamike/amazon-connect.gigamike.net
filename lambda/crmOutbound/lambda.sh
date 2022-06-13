#!/bin/bash
unlink Archive.zip
zip -r Archive.zip *

# Amazon Connect Contact Flow
# CRM-Outbound

# Tags Format
# Utilihub-Instance	us
# Utilihub-Environment demo|development|production|testing|uat

# US
#aws lambda update-function-code --function-name crmOutbound-us-demo --zip-file fileb://Archive.zip
aws lambda update-function-code --function-name crmOutbound-us-development --zip-file fileb://Archive.zip
#aws lambda update-function-code --function-name crmOutbound-us-production --zip-file fileb://Archive.zip
#aws lambda update-function-code --function-name crmOutbound-us-testing --zip-file fileb://Archive.zip
#aws lambda update-function-code --function-name crmOutbound-us-uat --zip-file fileb://Archive.zip
