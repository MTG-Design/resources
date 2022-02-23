#!/bin/sh
filename=$1
filename=$(echo "${filename%%.*}")
echo $filename
inkscape -p "$filename.svg" --batch-process -d 256 -o "$filename.png"