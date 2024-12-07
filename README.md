<center>
  <h1>
    Site de recherche de stages
  </h1>
</center>

Site web de recherche de stages permettant de poster, gérer et postuler à des offres pour des étudiants, tuteurs et entreprises.

---

# Sommaire

- [Contexte](#contexte)
- [Conception](#conception)
  - [Base de données](#base-de-données)
    - [Dictionnaire de données](#dictionnaire-de-données)
    - [MCD](#mcd)
    - [MLD](#mld)
  - [Mock-up](#mock-up)
    - [Page d'accueil](#page-daccueil)
      - [Invité](#invité)
      - [Administrateur](#administrateur)
      - [Pilote](#pilote)
      - [Étudiant](#étudiant)
    - [Page de connexion](#page-de-connexion)
    - [Page d'enregistrement](#page-denregistrement)
    - [Page de recherche](#page-de-recherche)
      - [Invité](#invité-1)
      - [Pilote](#pilote-1)
      - [Étudiant](#étudiant-1)
    - [Page d'une entreprise](#page-dune-entreprise)
      - [Invité](#invité-2)
      - [Pilote](#pilote-2)
      - [Étudiant](#étudiant-2)
    - [Page d'une offre](#page-dune-offre)
      - [Invité](#invité-3)
      - [Administrateur](#administrateur-1)
      - [Pilote](#pilote-3)
      - [Étudiant](#étudiant-3)
    - [Page d'utilisateur](#page-dutilisateur)
      - [Page d'élève](#page-délève)
      - [Page de pilote](#page-de-pilote)
    - [Page d'administration](#page-dadministration)
      - [Utilisateurs](#utilisateurs)
      - [Entreprises](#entreprises)
- [Résultats](#résultats)

---

# Contexte

Aujourd’hui les étudiants effectuent leur recherche de stage sur des sites comme LinkedIn ou Indeed. Afin de rendre cette dernière étape de recherche de stage plus facile et pratique, Nous sommes chargés de concevoir un site et une application qui permettra de stockée les données d’une entreprise qui a déjà pris un stagiaire et de faciliter les recherches avec différentes fonctionnalités.

# Conception

## Base de données

### Dictionnaire de données

Ce dictionnaire de données sert de guide pour comprendre la structure et les relations entre les différents éléments de la base de données du site web.
Il décrit chaque information(champ), son format (type de données), sa taille, ses contraintes, sa signification et la table auxquelles elle appartient.

| Nom de la colonne | Type | Taille | Contrainte | Description | Table |
| - | - | -:| - | - | - |
| ID_Application | Int auto increment | 4 | PRIMARY KEY | Identifiant du candidature | Applications |
| CV | TXT | 15 | NOT NULL | CV du postulant | Applications |
| Motivation_Letter | TXT | 15 | NOT NULL | Lettre de Motivation du postulant | Applications |
| ID_Offer | Int auto increment | 4 | PRIMARY KEY | Identifiant d'offre Offers  |
| Description | Varchar | 50 | NOT NULL | Description de l'offre | Offers |
| Pay | Varchar | 50 | NOT NULL | la base de rénumération | Offers |
| Start_Date | Date | 8 | NOT NULL | la date de début | Offers |
| Places | Int | 4 | NOT NULL | Nombre de places offertes | Offers |
| End_Date | Date | 8 | NOT NULL | date de fin | Offers |
| ID_Study_Level | Int auto increment | 4 | NOT NULL | Identifiant de la promotion | Study_Levels |
| Name | int | 4 | NOT NULL | Nom de la promotion | Study_Levels |
| ID_Skill | Int auto increment | 4 | PRIMARY KEY | L'identifiant du compétence | Skills |
| Name | Varchar | 50 | NOT NULL | La compétence | Skills |
| ID_Company | int auto increment | 4 | PRIMARY KEY | L'identifiant de l'entreprise | Companies |
| Name | Varchar | 50 | NOT NULL | Nom de l'entreprise | Companies |
| Creation_Date | Date | 8 | NOT NULL | la date de création de l'entreprise | Companies |
| ID_Account | Int auto increment | 4 | PRIMARY KEY | L'identifiant de l'utilisateur | Accounts |
| Username | VarChar | 50 | NOT NULL | login de l'utilisateur | Accounts |
| Creation_Date | Date | 8 | NOT NULLla date de création du compte | Accounts |
| Password | VarChar | 50 | NOT NULL | Mot de passe de l'utilisateur | Accounts |
| ID_Session | Int auto increment | 4 | PRIMARY KEY | Identifiant de la session | Sessions |
| Token | Varchar | 50 | NOT NULL | le token | Sessions |
| Creation_Date | Date | 8 | NOT NULL | Date de creation du token | Sessions |
| Update_Date | Date | 8 | NOT NULL | Date de réinitialisation du token | Sessions |
| UserAgent | Varchar | 50 | NOT NULL | la partie UserAgent du header | Sessions |
| Geolocation | Varchar | 50 | NOT NULL | la localisation par adresse IP | Sessions |
| ID_Class | Int auto increment | 4 | PRIMARY KEY | L'identifiant du promotion | Classes |
| Name | VarChar | 50 | NOT NULL | Nom de promotion | Classes |
| ID_Note | Int auto increment | 4 | PRIMARY KEY | L'identifiant de l'avis | Company_Notes |
| Note | Int | 4 | NOT NULL | Avis sur l'entreprise | Company_Notes |
| ID_Activity | Int auto increment | 4 | PRIMARY KEY | L'identifiant du secteur d'activité | Activities |
| Name | VarChar | 50 | NOT NULL | Nom du secteur d'activité | Activities |
| ID_Country | Int auto increment | 4 | PRIMARY KEY | L'identifiant du pays | Countries |
| Name | VarChar | 50 | NOT NULL | Nom de pays | Countries |
| ID_City | Int auto increment | 4 | PRIMARY KEY | l'identifiant de ville | Cities |
| Name | VarChar | 50 | NOT NULL | Nom du ville | Cities |
| Zip | VarChar | 50 | NOT NULL | ZIP du ville | Cities |
| ID_Student | Int auto increment | 4 | PRIMARY KEY | Identifiant de l'étudiant | Students |
| ID_Administrators | Int auto increment | 4 | PRIMARY KEY | Identifiant d'administrateur | Administrators |
| ID_Professors | Int auto increment | 4 | PRIMARY KEY | Identifiant du pilote | Professors |
| ID_Account | Int auto increment | 4 | PRIMARY KEY | Identifiant de l'utilisateur | Users |
| Firstname | VarChar | 50 | NOT NULL | Nom de l'utilisateur | Users |
| Lastname | VarChar | 50 | NOT NULL | Prénom de l'utilisateur | Users |
| ID_Duration | Int auto increment | 4 | PRIMARY KEY | Identifiant du durée de l'offre | Durations |
| Duration | Int | 4 | NOT NULL | La durée de l'offre | Durations |
| ID_CompanyLocation | Int auto increment | 4 | PRIMARY KEY | Identifiant de localisation | Locations |
| Creation_Date | Date | 8 | NOT NULL | Date de creation de l'offre | Locations |
| Description | Varchar | 50 | NOT NULL | Description de localisation | Locations |
| IsHeadquarters | Logical | 4 | NOT NULL | pour savoir si c'est le siége principal | Locations |
| ID_Address | Int auto increment | 4 | PRIMARY KEY | Identifiant de l'adresse | Addresses |
| Number | VarChar | 50 | NOT NULL | Numero de la rue | Addresses |
| Street | VarChar | 50 | NOT NULL | la rue | Addresses |
| Creation_Date | Date | 8 | NOT NULL | Date de creation de l'adresse | Addresses |
| ID_Type | Int auto increment | 4 | PRIMARY KEY | Identifiant du type | AccountTypes |
| Name | VarChar | 50 | NOT NULL | Nom du type | AccountTypes |

L’objectif de ce dictionnaire est de faciliter la compréhension et la manipulation de la base de données à
toutes les personnes impliquées dans le projet.

### MCD

Ce schéma représente un modèle conceptuel de données (MCD) optimisé qui respecte les règles de la troisième forme normale (3NF) pour notre site web.
Il sert à modéliser les relations entre les offres et les utilisateurs, les candidatures et les entreprises. Par exemple :
- Les Applications sont directement liées aux Users, indiquant que chaque candidature est soumise par 
un étudiant.
- Les Offers proviennent des Companies, montrant que les entreprises publient des offres d'emploi 
auxquelles les utilisateurs peuvent postuler.
- Les Study Levels et Skills sont les exigences que les offres d'emploi demandent.
- Les Sessions représentent l'activité des utilisateurs au site.

![MCD](https://github.com/user-attachments/assets/d0179411-eec5-4824-9d8d-7638bdb73a28)

### MLD

Notre Modèle Logique de Données (MLD) est représenté la façon dont les données sont structurées dans la base de données, en mettant l'accent sur les tables, les clés primaires (PK), les clés étrangères (FK), et les relations entre elles.
Les relations entre les tables indiquent les liens entre les différents éléments de données. Par exemple, la table "Is of interest to" relie les "Users" aux "Applications", montrant quel étudiant a postulé à quelle offre.

![MLD](https://github.com/user-attachments/assets/e686944d-c3f5-41fc-9178-b7ce2957cfd4)

## Mock-up
### Page d'accueil
#### Invité

![image](https://github.com/user-attachments/assets/3516235a-d4a4-4d15-9c90-08d46ae00f38)

#### Administrateur

![image](https://github.com/user-attachments/assets/e192eced-d307-4b4d-8270-cd1a00bd72d8)

#### Pilote

![image](https://github.com/user-attachments/assets/8ed30885-758c-493d-92c8-a5dd8f8709af)

#### Étudiant

![image](https://github.com/user-attachments/assets/d6337168-1fe1-473c-8532-2a8ce9a46f1f)

### Page de connexion

![image](https://github.com/user-attachments/assets/2f3f0ca2-50fb-48d6-ae4a-36c497b54579)

Pour la page de connexion, nous aurons un champs texte pour l’adresse mail de l’utilisateur et pour le mot de passe, le bouton “valider” pour se connecter et deux liens vers la page d’inscription ou vers une page de récupération de mot de passe.
Pour la récupération de mot de passe, nous enverrons un code à l’adresse mail de l’utilisateur et de taper après le nouveau mot de passe.

### Page d'enregistrement

![image](https://github.com/user-attachments/assets/f9419325-0e05-4468-9f9f-f721509508f9)

### Page de recherche

#### Invité

![image](https://github.com/user-attachments/assets/c5b42ea1-6928-4843-9041-e662fa095240)

Pour la page de recherche de stage, alternance…. Nous aurons un champs texte pour saisir les mots clés liés à la recherche, le lieu de travail ,un bouton chercher qui lance la recherche et des dropdowns des listes avec différents filtres comme domaine, durée contrat ,type et plus de filtres.

![image](https://github.com/user-attachments/assets/cdf53a8a-7b3e-4142-9650-1bcc33f40a42)

Et nous pouvons trier la liste des résultats par les plus récent, les plus ancien, ordre alphabétique, etc.

![image](https://github.com/user-attachments/assets/771d80e4-25e2-46dc-bd24-a518c5d20dc6)

#### Pilote

![image](https://github.com/user-attachments/assets/dd1b9c5b-50b4-441c-890c-ab0183fad098)

#### Étudiant

![image](https://github.com/user-attachments/assets/887f0964-36e7-4728-99f6-e950f934ec91)

### Page d'une entreprise

#### Invité

![image](https://github.com/user-attachments/assets/5e003ac5-a684-4054-9af0-80e227e7e40a)

#### Pilote

![image](https://github.com/user-attachments/assets/69911469-85d6-48aa-a487-d61eab94261f)

#### Étudiant

![image](https://github.com/user-attachments/assets/351e0a10-4001-40ea-be97-2051f9ae1ed1)

### Page d'une offre
#### Invité

![image](https://github.com/user-attachments/assets/c4070c9d-3a25-487b-8633-0bccd6c1b7fb)

#### Administrateur

![image](https://github.com/user-attachments/assets/68bd4373-1c02-4353-8acc-1eb493d4ed39)

#### Pilote

![image](https://github.com/user-attachments/assets/1787f893-d43f-4d4b-b6a1-33da47ba9050)

#### Étudiant

![image](https://github.com/user-attachments/assets/ab5dee69-2bad-4990-a1e8-e827ffcd8409)

### Page d'utilisateur
#### Page d'élève

![image](https://github.com/user-attachments/assets/a85244d9-c83f-49cc-ae70-841a7c1213d5)

Dans cette page nous allons afficher le profil d’un étudiant.
Sur cette page nous allons afficher les informations de celui-ci (nom, prénom, centre, promotion) ainsi que son avatar dans une bannière prévu pour ceux-ci.
Les compétences sont affichées en dessous des informations l’utilisateurs pourra rajouter ses compétences dans le pop-up « modifier ».
Pour accéder aux compétences en détails de l’utilisateur un bouton voir plus est à disposition pour afficher toutes les compétences de l’étudiant.
Dans la bannière en haut de la page nous retrouvons un bouton modifier afin que l’utilisateur puisse modifier toutes informations. Un pop-up apparaitra quand nous cliquerons sur le bouton comme ci-dessous : 

![image](https://github.com/user-attachments/assets/0e9c991c-29e2-4315-863d-3310e62298f9)

L’utilisateur pourra rajouter des compétences via le menu déroulant.

![image](https://github.com/user-attachments/assets/1df22d84-3096-4377-a129-1658b2688ef2)

Dans le pop-up, l’utilisateur pourra aussi supprimer son compte avec un bouton rouge en bas à gauche.
Ensuite dans les corps de la page nous allons afficher sa wish-list et les offres auxquelles il a candidaté. Il y aura un système de pagination pour la wish-list et les offres candidatées.

#### Page de pilote

![image](https://github.com/user-attachments/assets/bc14f8f3-6a32-4bf1-8315-85154fb67557)

Cette page est le profil d’un pilote visible par le pilote lui-même (il y a un bouton pour modifier le profil en plus par rapport à la vue des autres utilisateurs).
Nous pouvons y retrouver une bannière avec différentes informations sur le pilote comme son nom, prénom ou son lieu de travail. 
Nous y voyons aussi la liste des promotions suivis par ce pilote.
La page de profil du pilote est similaire à la page étudiant étant donné que les pop-ups seront les mêmes. La différence se fait au niveau du corp de la page. Nous affichons les promotions auxquelles le pilote est assigné. Un système de pagination est également mis en place pour les promotions assignées.
Dans la bannière en haut de la page nous retrouverons les informations du pilote (nom, prénom, centre).

### Page d'administration
#### Utilisateurs

![image](https://github.com/user-attachments/assets/65f6b449-e805-494c-a8e4-95cf36a5963d)
![image](https://github.com/user-attachments/assets/e1e5c462-dab4-456a-9daa-111864c1b23c)
![image](https://github.com/user-attachments/assets/7502b583-ac71-4106-8507-426c50a3e2b7)
![image](https://github.com/user-attachments/assets/79481f26-c1c7-4db4-8ab5-f64e9024af3e)
![image](https://github.com/user-attachments/assets/4c20adad-22f2-464c-921e-46bfb57b99f2)
![image](https://github.com/user-attachments/assets/578a0530-a370-4b7d-a356-f612819894c9)

Cette page et ses nombreuses fenêtres permet à un pilote de gérer les étudiants et à un administrateur de gérer les pilotes et les étudiants.
Le pilote/administrateur peut :
-	Modifier un utilisateur
-	Supprimer un utilisateur 
-	Modifier ses candidatures
-	Ajouter un utilisateur

#### Entreprises

![image](https://github.com/user-attachments/assets/60d5090d-a5c1-41a8-9753-3184ac742c10)
![image](https://github.com/user-attachments/assets/3a4eb912-1cb6-4819-abc1-3af32a1515f5)
![image](https://github.com/user-attachments/assets/6ba6d6d3-0b38-4b28-93ed-dca80a2ced2b)
![image](https://github.com/user-attachments/assets/7828f4d9-2310-435c-a469-8f316586b4be)
![image](https://github.com/user-attachments/assets/a5df2dbf-a815-4138-ba5b-99277f2fbe11)
![image](https://github.com/user-attachments/assets/6900b433-042c-4eb3-9820-7e7f668352e2)

Cette page et ses nombreuses fenêtres permet à un pilote et un administrateur de gérer les entreprises.
Le pilote/administrateur peut :
-	Modifier une entreprise
-	Supprimer une entreprise
-	Modifier ses offres de stages
-	Ajouter une entreprise

# Résultats

Page d'accueil : 
![image](https://github.com/user-attachments/assets/d7c90835-9967-431d-96af-9af7fc4dacf5)
![image](https://github.com/user-attachments/assets/df6d1f4d-4062-4f7a-8225-b8ad283ebfae)

Page de recherche : 
![image](https://github.com/user-attachments/assets/996631fe-df4a-4aff-a40a-316b16aea960)

Page de connexion : 
![image](https://github.com/user-attachments/assets/3353d8cf-2709-4483-a8ff-5b1552a5a97e)

Page d'enregistrement :
![image](https://github.com/user-attachments/assets/ea236f5a-001e-4e4c-b9f1-379d7c7766cb)

Page d'une offre : 
![image](https://github.com/user-attachments/assets/f5fafefd-ff33-4168-ac92-2f5f91a9daed)
