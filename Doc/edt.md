# Documentation - Page PHP pour l'emploi du temps (EDT)

## À quoi sert ce fichier ?
Ce fichier PHP sert à afficher une page web qui montre un emploi du temps (EDT). Il permet de :
- Naviguer entre les semaines.
- Afficher un emploi du temps adapté à l'utilisateur.
- Télécharger l'emploi du temps en format PDF.
- Gérer les rôles et les accès des utilisateurs.

---

## Ce que contient la page

### **1. La structure principale**
- **Titre** : Le titre de la page est "EDT".
- **Style** : Le fichier CSS `CSSBasique.css` est utilisé pour la mise en page.
- **Scripts JavaScript** :
    - Bibliothèques `jspdf` et `jspdf-autotable` : Utilisées pour créer des fichiers PDF.
    - Menu interactif : Un menu burger est utilisé pour afficher ou cacher les liens de navigation.

---

### **2. Le contenu affiché**
- **Un logo** :
    - L'image du logo renvoie à la page principale `EDT.php` si on clique dessus.

- **Un menu de navigation** :
    - Propose plusieurs liens comme :
        - "Emploi du temps"
        - "Messagerie"
        - "Créer un compte" (affiché uniquement pour certains rôles)
        - "Salles disponibles"
        - "Déconnexion"

- **Une barre pour choisir la semaine** :
    - Deux boutons permettent d’aller à la semaine précédente ou suivante.
    - On peut aussi sélectionner une date pour voir l’emploi du temps de la semaine correspondante.

- **Informations de contexte** :
    - Affiche la classe et l'année sélectionnées, en fonction des cookies de l'utilisateur.

- **Un pied de page** :
    - Indique les auteurs du projet : Rémi, Dorian, Matthéo, Bastien et Noah.

---

## Fonctionnalités principales

### **1. Gestion des utilisateurs**
- **Connexion et rôles** :
    - L'utilisateur doit être connecté pour accéder à cette page. Sinon, il est redirigé vers une page de connexion.
    - Les rôles de l'utilisateur (par exemple, étudiant ou administrateur) déterminent ce qui est affiché dans le menu.

- **Utilisation des cookies** :
    - Deux cookies (`groupe` et `annee`) sont utilisés pour identifier la classe et l'année de l'utilisateur.

### **2. Navigation dans les semaines**
- L'utilisateur peut changer de semaine grâce aux boutons ou sélectionner une date manuellement.
- Le programme calcule automatiquement la date du lundi de la semaine choisie.

### **3. Affichage de l'emploi du temps**
- La classe et l'année définies par les cookies permettent d’afficher un emploi du temps personnalisé.
- L'objet `Edt` est utilisé pour générer l’emploi du temps.

---

## Fonctionnalités JavaScript

### **1. Menu interactif**
- Le menu burger permet d’afficher ou de cacher les options de navigation.

### **2. Génération de PDF**
- Un bouton "Télécharger en PDF" permet de sauvegarder l’emploi du temps en format PDF.

---

## Points techniques importants

### **Sécurité**
- Les cookies et les sessions sont utilisés pour garantir que seules les personnes autorisées accèdent à la page.
- Si aucun rôle n'est détecté, l'utilisateur est redirigé vers la page de connexion.

### **Personnalisation**
- Les options affichées dans le menu dépendent du rôle de l'utilisateur (par exemple, étudiant ou administrateur).

### **Facilité d’évolution**
- Le code est organisé pour que de nouvelles fonctionnalités puissent être ajoutées facilement (par exemple, d'autres vues ou rôles).

---

## Auteurs
- **Noms** : Rémi, Dorian, Matthéo, Bastien et Noah
- **Projet** : SAE - Emploi du temps
- **Année** : 2024