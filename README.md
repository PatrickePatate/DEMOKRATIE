# DEMOKRATIE
DEMOKRATIE est un intranet permettant à des associations ou organisations de soumettrent au vote des propositions à un panel d'utilisateurs

**Fonctionnalités**

DEMOKRATIE permet de ...
* Créer des votations ayant différents niveaux de confidentialités (Public, privé, gestion de groupes d'utilisateurs)
* Visualiser simplement et en temps réel les résultats de vos votes
* Créer des sous-votes (vote dans un vote)
* Créer des votes et sous-votes à bulletins secrets

**DEMOKRATIE** est idéal pour gérer la vie démocratique interne de votre association ou organisation simplement, sans passer par un tier (parfois payant) et sans déployer une machine de guerre pour une utilisation *"simple"*.

**DEMOKRATIE** est encore en développement (plus ou moins régulier) pour ajouter de nouvelles fonctionnalités et corriger certains bugs, n'hésitez pas à ouvrir une issue si vous trouvez un bug ou si vous voulez suggérer une fonctionnalité.

# Installation
**DEMOKRATIE** nécéssite simplement un serveur web avec une version récente de PHP et une base de donnée MySQL, la structure de la base de donnée est contenue dans le fichier c5vote.sql (à la racine). Théoriquement (je ne l'ai pas testé sur une autre machine) l'utilisateur par défaut est admin@admin.fr adm1n_<br />
-Premièrement créez la base de données avec le fichier SQL fourni<br />
-Deuxièmement, configurez correctement votre base de donnée dans le fichier inc/config.php en y renseignant les accès ainsi que les infos complémentaires comme une clé d'API TinyCloud, ...<br />
-Et enfin, rendez vous sur <votre_domaine.fr>/inc/slir/install pour initialiser correctement la bibliothèque SLIR utilisée pour les images.<br />
DEMOKRATIE est prêt à être utilisé !

## 

L'utilisation de DEMOKRATIE est libre et encouragée, cependant merci de me créditer quelque part et de ne pas en tirer profit. 
