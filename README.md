# Travel Blog API

**L'api n'est finalement pas hébergé mais la base de données si.**

Cette API permet de se connecter à nos serveurs contenant des Articles et Post de blog concernant le voyage. Les articles peuvent êtres chercher par tags. Vous devez créer un compte pour poster des blogs pour vous connecter aller voir [Comment se connecter](/travel_blog#serveur-dauthentification).
Le serveur d'API est accessible à l'IP : [15.188.174.107](http://15.188.174.107)
<br>
## Base de données

Voici le MCD : ![mcd_php.jpg](.media/img_0.jpeg)

## Serveur d'authentification

Le serveur d'authentification est accessible à l'adresse : [http://15.188.174.107/api/authentifier/](http://15.188.174.107/api/authentifier/)
<br>
### Comment se connecter

Il faut envoyer une requête POST avec les paramètres suivants :

* login (en dur)
* mdp (en dur)
Exemple de body de requête :

``` json
{
    "login": "user1",
    "mdp": "mdp1"
}
```
<br>
Exemple de réponses :

``` json
{
    "status": 200,
    "status_message": "OK",
    "data": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJsb2dpbiI6InVzZXIxIiwicHJpdmlsZWdlcyI6MCwiZXhwIjoxNjc5NTAwNDY3fQ.8SsQVBxTZMRspokXWagnpDqp9Qh_IxBs9XXczTGEy0c"
}
```
<br>
Erreurs possibles :

* Mauvais identifiants

``` json
{
    "status": 401,
    "status_message": "Unauthorized, invalid login or password",
    "data": null
}
```

* Erreur de connexion à la base de données

``` json
{
    "status": 500,
    "status_message": "Internal Server Error",
    "data": null
}
```
