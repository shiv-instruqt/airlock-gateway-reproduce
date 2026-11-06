#!/bin/sh
# Skip LAB 02: starts the REST solve (scripts/gateway/rest-solve-lab2.sh, on the Gateway)
# as a background job, because a solve request over ~30 s is cut off, and gives it up to
# 20 s. The checks wait for the job as well. Own lock and marker, so LAB 01's skip and
# this one do not block each other. Log: /var/log/lab-solve.log
. /opt/lab/scripts/lib/gw.sh

# Instruqt calls the solve once per condition (3x); a job that succeeded in the last
# 2 minutes covers them all.
[ -n "$(find "$LAB_STATE/solved-lab2" -mmin -2 2>/dev/null)" ] && exit 0
if mkdir "$LAB_STATE/solve-lab2.lock" 2>/dev/null; then
  setsid sh /opt/lab/scripts/lab2/solve-job.sh >> /var/log/lab-solve.log 2>&1 < /dev/null &
fi
i=0
while [ -d "$LAB_STATE/solve-lab2.lock" ] && [ $i -lt 20 ]; do i=$((i + 1)); sleep 1; done
exit 0
