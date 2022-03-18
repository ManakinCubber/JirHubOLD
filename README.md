<p align="left">
    <img src="https://www.tiime.fr/hubfs/raw_assets/public/Tiime%20Theme%20by%20Markentive/images/logos/logo.svg" height="40px"  alt="logo Tiime">
</p>

## Installation de l'environement local
### Dépendances
Pour lancer l'environnement de travail en local, il est nécessaire d'avoir installé sur son poste de travail :
- [Docker](https://docs.docker.com/get-docker/)
_(⚠️ Veillez à bien faire [toutes les étapes d'installation](https://docs.docker.com/engine/install/linux-postinstall/#manage-docker-as-a-non-root-user))_ 
- [Docker Compose](https://docs.docker.com/compose/install/)

### Étapes
> Un fichier Makefile est disponible afin d'aider à interagir avec l'environement local. Ainsi, pas besoin de savoir manipuler Docker.

Après avoir cloné ce dépôt, rendez-vous à la racine du projet et commencez l'installation en executant la commande suivante : 
```bash
make build
```

Et voilà, vous pouvez désormais gérer votre environnement avec les commandes suivantes :
```bash
make start
make stop
make restart
```

Jirhub est disponible sur l'URL :
```text
http://localhost:8081
```