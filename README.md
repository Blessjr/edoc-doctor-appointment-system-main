# Fonctionnalités
# Administrateur

- L’administrateur peut ajouter des médecins, modifier des médecins, supprimer des médecins
- Programmer de nouvelles séances pour les médecins, supprimer des séances
- Voir les détails des patients
- Voir les réservations des patients

# Médecins

- Voir leurs rendez-vous
- Voir leurs séances programmées
- Voir les détails des patients
- Supprimer le compte
- Modifier les paramètres du compte

# Patients (Clients)

- Prendre rendez-vous en ligne
- Créer eux-mêmes un compte
- Voir leurs anciennes réservations
- Supprimer le compte
- Modifier les paramètres du compte

    
| Admin Dashboard | Doctor Dashboard | Patient Dashboard |
| -------| -------| -------|
| Email: `admin@edoc.com` | Email: `doctor@edoc.com` |   Email: `patient@edoc.com` | 
| Password: `123` |  Password: `123` |  Password: `123` |
 
  
-----------------------------------------------


# DÉMARRER

Ouvrez votre panneau de contrôle XAMPP et démarrez Apache et MySQL.
Extrayez le fichier zip du code source téléchargé.
Copiez le dossier du code source extrait et collez-le dans le répertoire "htdocs" de XAMPP.
Ouvrez PHPMyAdmin dans un navigateur, par exemple : http://localhost/phpmyadmin
Créez une nouvelle base de données nommée edoc.
Importez le fichier SQL fourni. Le fichier s’appelle edoc.sql et se trouve dans le dossier racine du code source.
Parcourez le système de rendez-vous des médecins dans un navigateur, par exemple : http://localhost/edoc-echanneling-main/