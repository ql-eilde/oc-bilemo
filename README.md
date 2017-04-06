[![SensioLabsInsight](https://insight.sensiolabs.com/projects/fab54935-c9d1-40e4-8914-78002d1aa870/mini.png)](https://insight.sensiolabs.com/projects/fab54935-c9d1-40e4-8914-78002d1aa870)

Bilemo
==========

This is the public repository of my 7th project on OpenClassrooms. The goal of this project is to create a RESTful API with OAuth authentification and proper documentation.

# Installation
## 1. Récupérer le code

1. Via Git en clonant ce dépôt.

## 2. Télécharger les vendors et définir les paramètres d'application
Avec Composer bien évidemment :

    composer install

On vous demande à la fin de l'installation de définir les paramètres de l'application (database et mailer), complétez les informations demandées et validez.

*Attention, n'oubliez pas de remplir les paramètres du mailer afin de recevoir les identifiants OAuth par mail ainsi que la version du serveur mysql que vous utilisez.*
## 3. Créez la base de données
Si la base de données que vous avez renseignée dans l'étape 2 n'existe pas déjà, créez-la :

    php bin/console doctrine:database:create

Puis créez les tables correspondantes au schéma Doctrine :

    php bin/console doctrine:schema:update --force

Enfin, ajoutez les fixtures :

    php bin/console doctrine:fixtures:load

## 4. Créez un user et lancez la commande de création d'un client OAuth
Il va falloir créer un user , pour cela, lancez la commande suivante :

    php bin/console fos:user:create

Saisissez un username, un email et un mot de passe.

Il faut ensuite lancer la commande qui permet de créer un client pour l'API. Pour cela, lancez la commande suivante :

    php bin/console oauth-server:client-create <VOTRE-ADRESSE-EMAIL>

Vous recevrez alors sur l'adresse email indiquée en paramètre de la commande votre client id et votre client secret.

## 5. Obtenez un access token pour l'API
Avec Postman.

Faites une requête de type `POST /oauth/v2/token` avec les paramètres suivant dans l'onglet 'Body' (en json) :

    {
    	"grant_type": "password",
    	"client_id": "VotreClientId",
    	"client_secret": "VotreCLientSecret",
    	"username": "VotreUsername",
    	"password": "VotrePassword"
    }

N'oubliez pas de sélectionnez le format 'raw' et indiquer que l'on envoie du JSON (application/json).

Vous recevrez en retour un access token ainsi qu'un refresh token qu'il faudra conserver dans un fichier txt par exemple.

## 6. Connectez vous à l'API avec votre access token
Avec Postman.

Faites une requête de type `GET /products` et ajoutez le header suivant :

    Authorization : Bearer VotreAccessToken

Vous voilà authentifier !

## 7. Explorez l'API
Vous pouvez maintenant utiliser l'API, pour cela il y a une documentation que vous trouverez à cette adresse `/doc`.

Votre access token expire au bout d'une heure. Il faudra donc refaire une demande en faisant une requête à cette adresse `POST /oauth/v2/token` avec les paramètres suivants :

    {
        "grant_type": "refresh_token",
        "client_id": "VotreClientId",
        "client_secret": "VotreClientSecret",
        "refresh_token": "VotreRefreshToken"
    }

## Et profitez !
