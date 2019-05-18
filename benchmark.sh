#!/usr/bin/env bash

function dt() {
	if [ -x "$(command -v gdate)" ]
	then
		echo $(gdate +%s.%N)
	else
		echo $(date +%s.%N)
	fi
}

maxsize=${1:-8}
start=$(dt)
size_duration=()

for ((i=4;i<=$maxsize;i++))
do
	board_duration=()
	size_start=$(dt)
	for ((j=1;j<=$i;j++))
	do
		for ((k=1;k<=$i;k++))
		do
			board_start=$(dt)
			php index.php $j $k $i 0
			board_end=$(dt)
			bdiff=$(python -c "print((${board_end} - ${board_start}) * 1000)")
			board_duration+=($bdiff)
		done
	done
	IFS='+' avg_time=$(echo "scale=12;(${board_duration[*]})/${#board_duration[@]}"|bc)
	size_duration[i]=$avg_time
	printf "%s\t%s\n" "Board Size:" "$i" "Avg. Time:" "${avg_time}"
done

for i in "${!size_duration[@]}"; do 
  printf "%s\t%s\n" "Board Size:" "$i" "Avg. Time:" "${size_duration[$i]}"
done

