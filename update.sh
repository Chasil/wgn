#!/bin/bash

read -p "Czy na pewno chcesz wykonać git pull i uruchomić aplikację? [t/n]: " -n 1 -r
echo    # (opcjonalne) przeniesienie do nowej linii

if [[ $REPLY =~ ^[Tt]$ ]]
then
    git pull origin master
    
    echo "manual clear cache."
    
    mv app/cache/prod app/cache/prod---
    
    rm -rf app/cache/prod---
else
    echo "Operacja anulowana."
fi
