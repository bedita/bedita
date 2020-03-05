#!/bin/bash

function print_usage () {
  printf "Usage: ./reindex.sh -t|--types <types> [-c|--concurrency <number>] [-s|--session <name>]\n"
  printf "\n"
  printf "\t-t|--types\t\tcomma-separated list of types\n"
  printf "\t-c|--concurrency\treindex this many types concurrently\n"
  printf "\t-s|--session\t\ttmux session name, will be created if doesn't exists\n"
}

# default
SESSION='reindex'
CONCURRENCY=4
TYPES=()

if [ "$#" -eq "0" ]; then
  print_usage
  exit 0
fi

# read parameters (thanks https://stackoverflow.com/a/14203146/2270403)
while [[ "$#" -gt "0" ]]; do
  key="$1"

  case $key in
      -h|--help)
      print_usage
      exit 0
      ;;
      -t|--types)
      IFS=',' read -r -a TYPES <<< "$2"
      shift
      shift
      ;;
      -c|--concurrency)
      CONCURRENCY="$2"
      shift
      shift
      ;;
      -s|--session)
      SESSION="$2"
      shift
      shift
      ;;
      *)    # unknown option, ignore
      shift
      ;;
  esac
done

if [ "${#TYPES[@]}" -eq "0" ]; then
  printf "No types selected\n"
  exit 1
fi

printf "Deleting old index and starting reindex for type '%s'\n" "${TYPES[0]}"

if ! tmux has-session -t "$SESSION" > /dev/null 2>&1; then
  tmux new-session -d -s "$SESSION" "./cake.sh dbadmin rebuildIndex -delete -type ${TYPES[0]}"
  tmux select-window -t "$SESSION":0
else
  tmux select-window -t "$SESSION":0
  # account for panels already open
  ((CONCURRENCY += $(tmux list-panes -s -t "$SESSION" | wc -l)))
  tmux split-window "./cake.sh dbadmin rebuildIndex -delete -type ${TYPES[0]}"
  tmux select-layout tiled
fi

sleep 10

for type in "${TYPES[@]:1}"; do
  while [ "$(tmux list-panes -s -t "$SESSION" | wc -l)" -ge "$CONCURRENCY" ]; do
    sleep 3
  done

  printf "Starting reindex for type '%s'\n" "$type"
  tmux split-window -t "$SESSION":0.0 "./cake.sh dbadmin rebuildIndex -type $type"
  tmux select-layout -t "$SESSION":0 tiled
  sleep 1
done
