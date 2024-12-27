#!/bin/bash

LOG="$PWD/../../../writable/logs/writable.txt"
touch $LOG

for i in ../../../writable/pullthis/*
do
    PROJECT=${i##*/}
    while IFS= read -r line
    do
        echo $line | while read col
        do
            IFS="|"
            set - $col
                REPOFOLDER=$1
                REPOBRANCH=$2

                rm $i
                echo "Pulling $REPOFOLDER"
                cd $REPOFOLDER
                if [ "$PROJECT" = "self" ] && [ "$REPOBRANCH" = "main" ]
                then
                    git pull
                else
                    git pull origin $REPOBRANCH
                fi
                echo $(date -u) "- $REPOFOLDER - Successfully pulled"  >> $LOG
        done
    done < $i
done
