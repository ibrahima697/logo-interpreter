Logo Interpreter
Bienvenue dans le projet Logo Interpreter ! Ce projet propose un interpréteur pour le langage Logo, permettant de contrôler une "tortue" virtuelle qui peut se déplacer et dessiner sur un canvas.

Installation
Pour exécuter ce projet localement, suivez ces étapes :

Clonez le dépôt :

bash

git clone https://github.com/ibrahima697/logo-interpreter.git
Accédez au répertoire du projet :

bash

cd logo-interpreter
Installez les dépendances :

bash

composer install
Copiez le fichier d'environnement .env :

bash

cp .env.example .env
Générez une clé d'application :

bash

php artisan key:generate
Configurez la base de données dans le fichier .env en spécifiant les paramètres de votre base de données.

Exécutez les migrations et les seeders :

bash

php artisan migrate --seed
Lancez le serveur de développement :

bash

php artisan serve
Utilisation
Accédez à l'application dans votre navigateur à l'adresse http://localhost:8000.

Utilisez le formulaire pour saisir les commandes Logo et appuyez sur le bouton "Exécuter".

Observez la tortue virtuelle se déplacer et dessiner sur le canvas en fonction des commandes que vous avez fournies.

Commandes Logo Prises en Charge
Voici quelques exemples de commandes Logo que vous pouvez essayer :

AV 50: Avancer de 50 unités.
TD 90: Tourner à droite de 90 degrés.
BC: Activer le mode dessin (la tortue laisse une trace).
VE: Désactiver le mode dessin (la tortue ne laisse pas de trace).
NETTOIE: Effacer l'écran sans changer la position de la tortue.
ORIGINE: Replacer la tortue au centre du canvas.
Contributions
Les contributions sont les bienvenues ! Si vous souhaitez améliorer ou étendre ce projet, n'hésitez pas à ouvrir une pull request.


GUISSE Ibrahima / iguisse97@gmail.com
Licence
Ce projet est sous licence MIT.
