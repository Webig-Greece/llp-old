#!/bin/bash

# Generate file structure for migrations
find database/migrations -type f > file_structure.txt

# Generate file structure for Models
find app/Models -type f >> file_structure.txt

# Generate file structure for Seeders
find database/seeders -type f >> file_structure.txt

# Generate file structure for Middleware
find app/Http/Middleware -type f >> file_structure.txt

# Generate file structure for Controllers
find app/Http/Controllers -type f >> file_structure.txt

echo "File structure generated and saved to file_structure.txt."
read -rsp $'Press any key to continue...\n'