# Add the logic to auto import and export database
# Add the logic to take in account the frontend project structure
# Add a readme how to create a new Project from the default template
# Add to the readme how to download the openAPI Redoc
# creer une commande qui lit tous les entités disponible et creer les permissions CRUD+P (create, read, update, delete + patch) pour chaque entité: ce qui permetra de gerer les roles et les droits d'access. le super User devra avoir tous les droits --> ajouter un Entity SuperUserAccess, un user a un SuperUserAccess, l'access SuperUser peut appatenir a plusieurs Users
# pendant le app-build une commande devrait lire le .super.user et recuperer les informations du super user pour creer par defaut son compte. si le fichier n'existe par lire celui par defaut dans le fichier .env