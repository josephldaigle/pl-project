#!/usr/bin/env bash
#------------------------------------------------------
# Add enable swap file commands to default EB configuration
#------------------------------------------------------

printf "\nAdding enable swap file commands to default EB configuration...\n"

if ! free | awk '/^Swap:/ {exit !$2}'; then

    sudo dd if=/dev/zero of=/swapfile bs=1M count=1024
    sudo mkswap /swapfile
    sudo swapon /swapfile

    echo 'Swap enabled'

else

    echo 'Swap found'

fi