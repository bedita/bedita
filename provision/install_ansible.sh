#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

if [[ ! -x `which ansible-playbook` ]] || [[ ! -x `which git` ]]
then
    echo 'Installing Ansible and Git...'
    apt-get update -y -qq
    apt-get install -y -qq git python-pip
    apt-get build-dep -y -qq ansible
    pip install -q ansible
    echo 'Ansible and Git installed successfully!'
fi
