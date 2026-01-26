# 📘 AtlasHR - Documentation Technique & Guide d'Installation

**Version :** 1.0.0
**Date :** 26/01/2026
**Public Cible :** Administrateurs IT / Intégrateurs Système

---

## 1. Vue d'Ensemble du Projet
**AtlasHR** est un tableau de bord léger de gestion des ressources humaines conçu pour suivre les statistiques des employés, les salaires (en MAD) et générer des rapports PDF/CSV.

### Stack Technique
*   **Frontend :** HTML5, CSS3 (Glassmorphism), JavaScript (Chart.js, jsPDF).
*   **Backend :** PHP 7.4+ (Vanilla, sans framework).
*   **Base de Données :** MySQL / MariaDB.
*   **Serveur :** Apache (HTTPD).
*   **Connectivité :** Accès Internet requis pour les CDN (Polices, Icônes, Graphiques).

---

## 2. Prérequis Système

### Matériel (Hardware)
*   **CPU :** 1 vCPU minimum.
*   **RAM :** 512 Mo minimum (1 Go recommandé).
*   **Stockage :** 500 Mo d'espace libre.

### Logiciel (Software)
*   **OS :** Linux (Ubuntu/Debian recommandé) ou Windows Server avec XAMPP/WAMP.
*   **Serveur Web :** Apache 2.4+.
*   **PHP :** Version 7.4 à 8.2.
    *   *Extensions requises :* `mysqli`.
*   **Base de Données :** MySQL 5.7+ ou MariaDB 10.3+.

---

## 3. Guide d'Installation (Linux/Debian)

### Étape 1 : Installer les Dépendances
Mettez à jour votre gestionnaire de paquets et installez la pile LAMP.

```bash
sudo apt update
sudo apt install apache2 mysql-server php php-mysql libapache2-mod-php -y
```

### Étape 2 : Configurer la Base de Données
1.  **Sécuriser l'installation MySQL** (Optionnel mais recommandé) :
    ```bash
    sudo mysql_secure_installation
    ```
2.  **Importer le Schéma :**
    Copiez le fichier `src/rh/init.sql` sur votre serveur et exécutez :
    ```bash
    sudo mysql -u root -p < init.sql
    ```
    *Ceci crée la base de données `rh_db` et la table `employees` avec des données initiales.*

3.  **Créer l'Utilisateur Applicatif :**
    Connectez-vous à MySQL (`sudo mysql -u root -p`) et exécutez :
    ```sql
    CREATE USER 'rh_user'@'localhost' IDENTIFIED BY 'rh_app_password';
    GRANT ALL PRIVILEGES ON rh_db.* TO 'rh_user'@'localhost';
    FLUSH PRIVILEGES;
    EXIT;
    ```
    *Note : Si vous changez le nom d'utilisateur/mot de passe ici, vous DEVEZ mettre à jour `db.php`.*

### Étape 3 : Déployer le Code Applicatif
1.  **Préparer le Répertoire Web :**
    ```bash
    sudo mkdir -p /var/www/html/rh
    ```
2.  **Copier les Fichiers :**
    Transférez tous les fichiers de `src/rh/` vers `/var/www/html/rh/`.
    ```bash
    # Exemple si git est utilisé
    cp -r src/rh/* /var/www/html/rh/
    ```
3.  **Définir les Permissions :**
    ```bash
    sudo chown -R www-data:www-data /var/www/html/rh
    sudo chmod -R 755 /var/www/html/rh
    ```

### Étape 4 : Configuration
Ouvrez `/var/www/html/rh/db.php` et vérifiez que les identifiants correspondent à votre configuration de base de données.

```php
$servername = "localhost";
$username = "rh_user"; 
$password = "rh_app_password"; // Changez ceci si vous avez défini un mot de passe DB différent
$dbname = "rh_db";
```

### Étape 5 : Sécurisation (Niveau Initial)
Le projet inclut un script automatisé pour mettre en place les mesures de sécurité de base (HTTPS, Redirection, Certificat Auto-signé).

Exécutez le script suivant :
```bash
cd scripts
chmod +x setup_ssl.sh
./setup_ssl.sh
```
*Ce script va :*
1.  Générer un certificat TLS auto-signé (valide 365 jours).
2.  Configurer Apache pour utiliser HTTPS (Port 443).
3.  Forcer la redirection du trafic HTTP vers HTTPS.

Pour la production, il est recommandé de remplacer le certificat auto-signé par un certificat **Let's Encrypt** (via Certbot).

---

## 4. Dépannage (Troubleshooting)

### ❌ Erreur de Connexion Base de Données
*   **Symptôme :** "Connection failed: Access denied..."
*   **Solution :** Vérifiez `db.php`. Assurez-vous que `rh_user` existe dans MySQL et a les privilèges sur `rh_db`.
*   **Solution :** Assurez-vous que le service MySQL est démarré (`sudo systemctl status mysql`).

### ❌ Erreur HTTP 500
*   **Symptôme :** Page blanche ou "Internal Server Error".
*   **Solution :** Vérifiez les logs Apache : `tail -f /var/log/apache2/error.log`.
*   **Solution :** Assurez-vous que l'extension PHP MySQL est installée (`sudo apt install php-mysql`).

### ❌ Graphiques ne chargent pas
*   **Symptôme :** Espaces vides là où les graphiques devraient être.
*   **Solution :** Assurez-vous que le serveur (ou le navigateur client) a accès à Internet pour charger les scripts depuis `cdn.jsdelivr.net`.
*   **Solution :** Vérifiez la console du navigateur (F12) pour les erreurs JavaScript.

### ❌ Problèmes CSS (Barre latérale cassée)
*   **Solution :** Videz le cache du navigateur (Ctrl+F5). Le CSS est intégré dans les fichiers PHP, donc les problèmes de cache sont rares mais possibles lors des mises à jour.

---

## 5. Recommandations de Sécurité pour la Production
1.  **Désactiver le Rapport d'Erreurs :** Dans `index.php`, `analytics.php`, et `reports.php`, commentez ces lignes :
    ```php
    // ini_set('display_errors', 1);
    // error_reporting(E_ALL);
    ```
2.  **Mots de passe faibles :** Les mots de passe par défaut (`rh123`, `it123`) sont faibles. Changez-les immédiatement via la base de données ou ajoutez une fonctionnalité de changement de mot de passe.
3.  **HTTPS :** Servez toujours l'application via HTTPS en utilisant SSL (ex: Let's Encrypt).
