# Travel Blog API

Cette API permet de se connecter à nos serveurs contenant des Articles et Post de blog concernant le voyage. Les articles peuvent êtres chercher par tags. Vous devez créer un compte pour poster des blogs pour vous connecter aller voir [Comment se connecter](/Travel%20Blog%20API/Serveur%20d'authentification/).
Le serveur d'API est accessible à l'IP : [15.188.174.107](http://15.188.174.107)
<br>
## Base de données

Voici le MCD : ![MCD de la base de l'API](https://media.discordapp.net/attachments/950780481350303806/1088127121467125850/mcd_php.jpg?width=1178&height=584)
<br>
## Serveur d'authentification

Le serveur d'authentification est accessible à l'adresse : [http://15.188.174.107/travel_blog/api/authentifier/](http://15.188.174.107/travel_blog/api/authentifier/)
<br>
### Comment se connecter
Il faut envoyer une requête POST avec les paramètres suivants :
- login (en dur)
- mdp (en dur)
Exemple de body de requête :
```json
{
    login: 'user1',
    mdp: 'mdp1'
}
```
<br>
Exemple de réponses : 
```json
{
    "status": 200,
    "status_message": "OK",
    "data": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJsb2dpbiI6InVzZXIxIiwicHJpdmlsZWdlcyI6MCwiZXhwIjoxNjc5NTAwNDY3fQ.8SsQVBxTZMRspokXWagnpDqp9Qh_IxBs9XXczTGEy0c"
}
```
<br>
Erreurs possibles :
-  Mauvais identifiants
```json
{
    "status": 401,
    "status_message": "Unauthorized, invalid login or password",
    "data": null
}
```
- Erreur de connexion à la base de données
```json
{
    "status": 500,
    "status_message": "Internal Server Error",
    "data": null
}
```
