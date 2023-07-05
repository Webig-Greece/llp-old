#!/bin/bash
find database/migrations -type f > file_structure.txt
echo "File structure generated and saved to file_structure.txt."
read -rsp $'Press any key to continue...\n'