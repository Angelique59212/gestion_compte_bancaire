# api_gestion_compte_bancaire

L'objectif de ce TP est de développer une api pour la gestion de comptes bancaires.
L'API devra permettre la création, la consultation, la mise à jour et la suppression de comptes bancaires
De plus, des fonctionnalités avancées telles que des virements, les relevés de compte et les opérations courantes devront être prises en compte.

**Fonctionnalités requises:**

- Création d'un compte bancaire avec les détails du titulaire et le solde initial.
- Consultation des détails d'un compte bancaire spécifique.
- Mise à jour des détails d'un compte bancaire (par exemple, changement de solde, mise à jour des informations du titulaire).
- Suppression d'un compte bancaire spécifique.
- Réalisation de transactions entre comptes bancaires (virements)
- Recherche de compte par le nom du titulaire.
- Implémentation de fonctionnalités de gestion des opérations courantes telles que les dépôts et les retraits.
- CRUD classique des entités
- Ajout du montant épargner ( compte épargne )


**Exigences techniques:**

Utilisation du framework Symfony pour le développement de l'API.
Utilisation d'une base de données pour stocker les détails des comptes bancaires et des transactions.
Utilisation de fixtures
Utilisation de bonnes pratiques de codage, y compris la documentation appropriée du code.
Utilisation de migrations Doctrine pour la gestion de la base de données.


**Données minimale :**

_**Entité Client**:_

id (identifiant unique du client)
nom (nom du client)
prénom (prénom du client)
adresse (adresse du client)
numéro de téléphone (numéro de téléphone du client)
adresse e-mail (adresse e-mail du client)
autres détails pertinents du client (le cas échéant)

_**Entité Compte Bancaire**:_

id (identifiant unique du compte)
numéro de compte (numéro de compte unique)
type de compte (courant ou épargne)
solde (solde actuel du compte)
client (référence au client associé à ce compte)
autorisation de découvert (booléen indiquant si le compte courant est autorisé à être à découvert)
taux d'intérêt (uniquement pour les comptes d'épargne) max 15% min 1%
