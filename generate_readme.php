<?php

$projectName = "Progetto Laravel";
$description = "Progetto in cui si svolgono degli esercizi";
$installation = <<<EOT
1. Clonare il progetto
2. Navigare nella directory principale del progetto utilizzando il terminale
3. Creare il file `.env` usando questo comando nel terminale
    ```sh
    cp .env.example .env
    ```
4. Eseguire l'installazione di Composer
    ```sh
    composer install
    ```
5. Eseguire l'installazione di npm
    ```sh
    npm install
    ```
6. Impostare la chiave dell'applicazione
    ```sh
    php artisan key:generate --ansi
    ```
7. Eseguire le migrazioni e popolare i dati
    ```sh
    php artisan migrate --seed
    ```
    poi scrivere "yes" per creare il database;

8. Avviare il server Vite
    ```sh
    npm run dev
    ```
9. Avviare il server Artisan
    ```sh
    php artisan serve
    ```
EOT;

$usage = <<<EOT
### PROVA PROGETTO
1. Importa su Postman la collection e l'environment che sono dentro la cartella "postman"
2. Effettua la request "register request" per creare l'utente (nome, mail e password)
3. Effettua la login "login request"
4. Per visualizzare la lista degli asset con i relativi criteri di ricerca, eseguire la "get_Asset_classes request":

#### Esempi dei criteri di ricerca
1. Cercare per id (/api/asset-classes?id=1)
2. Cercare per name (/api/asset-classes?name=Quadro)
3. Cercare per service (/api/asset-classes?service=Elettrico)
4. Cercare per commission (/api/asset-classes?commission=Commessa maia)
5. Combinando più criteri di ricerca (/api/asset-classes?name=Quadro&service=Elettrico&commission=Commessa maia)
EOT;

$readmeContent = <<<EOT
# $projectName

## Descrizione
$description

## Installazione
$installation

## Utilizzo
$usage

EOT;

file_put_contents('README.md', $readmeContent);

echo "README.md generato con successo!";
