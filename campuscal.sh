#!/bin/bash
CKFILE="cookie`date +%s`"
curl -c $CKFILE -s -L -d "u=$1" -d "p=$2" 'https://www.campus.rwth-aachen.de/office/views/campus/redirect.asp' > /dev/null
curl -b $CKFILE -s -L 'https://www.campus.rwth-aachen.de/office/views/calendar/iCalExport.asp?startdt='`date -d "-1 month" +%d.%m.%Y`'&enddt='`date -d "+8 months" +%d.%m.%Y`
rm -f $CKFILE
