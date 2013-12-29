#!/bin/bash
netstat -n | grep :8085 | awk '{print $5}' | cut -d':' -f 1
