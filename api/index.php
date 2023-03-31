<?php
require('./files/api_utils.php');
require('./files/bdd_utils.php');
require('./files/jwt_utils.php');
header("Content-Type:application/json");
$http_method = $_SERVER['REQUEST_METHOD'];
$isAuthentified = false;
$token = get_bearer_token();
$token_content = NULL;
if ($token != NULL) {
    $isAuthentified = is_jwt_valid($token);
    $token_content = json_decode(base64_decode(explode('.', $token)[1]));
}
# 0 = modo
# 1 = publisher
switch ($http_method) {
    case "GET":
        if (!$isAuthentified) {
            if (empty($_GET['id'])) {
                $matchingData = getAllArticleNonAuth();
            } else {
                if (!is_numeric($_GET['id'])) {
                    deliver_response(400, "Requête invalide id non numérique", NULL);
                    return;
                }
                $matchingData = getArticleId($_GET['id']);
            }
            if (empty($matchingData)) {
                deliver_response(404, "Aucun article trouvé", NULL);
                return;
            }
            deliver_response(200, "Article trouvé", $matchingData);
            return;
        }
        if (is_authorized(0)) {
            if (empty($_GET['id'])) {
                $matchingData = getArticleModo();
                deliver_response(200, "Article trouvé", $matchingData);
                return;
            }
            if (!is_numeric($_GET['id'])) {
                deliver_response(400, "Requête invalide id non numérique", NULL);
                return;
            }
            $matchingData = getArticleId($_GET['id']);
            if (empty($matchingData)) {
                deliver_response(404, "Aucun article trouvé", NULL);
                return;
            }
            deliver_response(200, "Article trouvé", $matchingData);
            return;
        }
        if (is_authorized(1)) {
            if (empty($_GET['id'])) {
                $matchingData = getAllArticle();
            } else {
                if (!is_numeric($_GET['id'])) {
                    deliver_response(400, "Requête invalide id non numérique", NULL);
                    return;
                }
                $matchingData = getArticleId($_GET['id']);
            }
            if (empty($matchingData)) {
                deliver_response(404, "Aucun article trouvé", NULL);
                return;
            }
            deliver_response(200, "Article trouvé", $matchingData);
        }
    case "POST":
        /// Récupération des données envoyées par le Client
        if (!$isAuthentified) {
            deliver_response(401, "Vous n'êtes pas authentifié", NULL);
            return;
        }
        if (!is_authorized(1)) {
            deliver_response(403, "Vous n'avez pas les droits pour ajouter un article", NULL);
            return;
        }
        $postedData = file_get_contents('php://input');
        $postedData = json_decode($postedData, true);
        if (empty($postedData['titre']) || empty($postedData['contenu'])) {
            deliver_response(400, "Requête invalide titre ou contenu indéfini", NULL);
            return;
        }
        $matchingData = addArticle($postedData['titre'], $postedData['contenu'], $token_content->login);
        if (empty($matchingData) || $matchingData == false) {
            deliver_response(500, "Erreur lors de l'ajout de l'article", NULL);
            return;
        }
        deliver_response(200, "Article ajouté", $matchingData);
        break;
        /// Cas de la méthode PUT
    case "PATCH":
        break;
    case "PUT":
        if (!$isAuthentified){
            deliver_response(401, "Vous n'êtes pas authentifié", NULL);
            return;             
        }
        if (!is_authorized(1)){
            deliver_response(403, "Vous n'avez pas les droits pour modifier un article", NULL);
            return;             
        }
        $postedData = file_get_contents('php://input');
        $postedData = json_decode($postedData, true);
        $matchingData = updateArticle($token_content->login, $postedData['id'], $postedData['contenu'], $postedData['titre']);
        if (empty($matchingData) || $matchingData == false) {
            deliver_response(500, "Erreur lors de la modification de l'article", NULL);
            return;
        }
        deliver_response(200, "Article modifié", $matchingData);
        break;
        /// Cas de la méthode DELETE
    case "DELETE":
        if (!$isAuthentified) {
            deliver_response(401, "Vous n'êtes pas authentifié", NULL);
            return;
        }
        if (empty($_GET['id'])) {
            deliver_response(400, "Requête invalide id indéfini", NULL);
            return;
        }
        if (!is_numeric($_GET['id'])) {
            deliver_response(400, "Requête invalide id non numérique", NULL);
            return;
        }
        if (!is_authorized(1) && $token_content->login != getArticleId($_GET['id'])['login']) {
            deliver_response(403, "Vous n'avez pas les droits pour supprimer cet article ce n'est pas le vôtre", NULL);
            return;
        }
        $matchingData = deleteArticleModo($_GET['id']);
        if (empty($matchingData) || $matchingData == false) {
            deliver_response(500, "Erreur lors de la suppression de l'article", NULL);
            return;
        }
        deliver_response(200, "Article supprimé", $matchingData);
    default:
        /// Envoi de la réponse au Client
        deliver_response(400, "Aucune action effectuée relisez la documentation ", NULL);
        break;
}
# METHODES POST :

# AJOUTE UN ARTICLE

function addArticle($titre, $contenu, $login)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "INSERT INTO ARTICLE (titre, contenu, login) VALUES (?, ?, ?)";
    $values = array($titre, $contenu, $login);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
# METHODES GET :

# RETOURNE UN ARTICLE AVEC UN ID
function getArticleId($id)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT * FROM ARTICLE WHERE id = ?";
    $values = array($id);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
# RETOURNE UN ARTICLE AVEC UN TAG(DESTINATION, ACTIVITE, ...)
function getArticleTag($tag)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT ARTICLE.* FROM ARTICLE JOIN TAGS ON ARTICLE.ID=TAGS.ID WHERE TAG = ?";
    $values = array($tag);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
# RETOURNE UN ARTICLE AVEC UNE DATE MAIS ON UTILISE PAS AU FINAL
function getArticleDate($date)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT * FROM ARTICLE WHERE datep = ?";
    $values = array($date);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
# RETOURNE UN/DES ARTICLE(S) DEPUIS TITRE
function getArticleTitre($titre)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT * FROM ARTICLE WHERE titre = ?";
    $values = array($titre);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
# RETOURNE TOUS LES UTILISATEURS DE LA BASE DE DONNEES
function getAllUsers()
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT login FROM ARTICLE";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
# RETOURNE TOUS LES ARTICLES POUR UNE PERSONNNE NON AUTHENTIFIE SOIT ARTICLE(LOGIN, DATEP, CONTENU)
function getAllArticleNonAuth()
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT Titre, Login, datep, Contenu FROM ARTICLE ORDER BY datep DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
# RETOURNE TOUS LES ARTICLES POUR UNE PERSONNNE AUTHENTIFIE AVEC TOUS LES CHAMPS
function getAllArticle()
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT * FROM ARTICLE ORDER BY datep DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
# RETOURNE TOUS LES ARTICLES D'UN AUTEUR/PUBLISHER
function getArticleAuteur($login)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT * FROM ARTICLE WHERE login = ?";
    $values = array($login);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
# RETOURNE ABSOLUMENT TOUS LES ARTICLES 
function getArticleModo()
{
    $pdo = DBConnection::getInstance()->getConnection();
    $articles = getArticles($pdo);
    $likes = getLikes($pdo);
    $dislikes = getDislikes($pdo);

    // Combinez les résultats dans un tableau associatif
    $matchingData = array();
    foreach ($articles as $article) {
        $id_article = $article['ID'];
        $matchingData[$id_article] = array(
            'article' => $article,
            'likes' => array(),
            'dislikes' => array()
        );
        foreach ($likes as $like) {
            if ($like['ID'] == $id_article) {
                $matchingData[$id_article]['likes'] = explode(",", $like['LIKES']);
            }
        }
        foreach ($dislikes as $dislike) {
            if ($dislike['ID'] == $id_article) {
                $matchingData[$id_article]['dislikes'] = explode(",", $dislike['DISLIKES']);
            }
        }
    }
    return $matchingData;
}
# RETOURNE TOUS LES ARTICLES POUR UN PUBLISHER MAIS PAS LA LISTE DES GENS QUI ONT LIKES/DISLIKES
function getArticlePubli()
{
    $pdo = DBConnection::getInstance()->getConnection();

    $articles = getArticles($pdo);

    $matchingData = array();
    foreach ($articles as $article) {
        $id_article = $article['ID'];

        $matchingData[$id_article] = array(
            'article' => $article,
            'nb_likes' => 0,
            'nb_dislikes' => 0
        );
        $matchingData[$id_article]['nb_likes'] = getLikeData($id_article);
        $matchingData[$id_article]['nb_dislikes'] = getDislikeData($id_article);
    }

    return $matchingData;
}
# Chunk de code pour récupérer les articles
function getArticles($pdo)
{
    $sql_articles = "SELECT * FROM ARTICLE ORDER BY datep DESC";
    $stmt_articles = $pdo->prepare($sql_articles);
    $stmt_articles->execute();
    $articles = $stmt_articles->fetchAll(PDO::FETCH_ASSOC);

    return $articles;
}
# Chunk de code pour récupérer les likes
function getLikes($pdo)
{
    $sql_likes = "SELECT REAGIR.ID, COUNT(REAGIR.LIKES) AS NB_LIKES FROM REAGIR WHERE REAGIR.LIKES = 1 GROUP BY REAGIR.ID";
    $stmt_likes = $pdo->prepare($sql_likes);
    $stmt_likes->execute();
    $likes = $stmt_likes->fetchAll(PDO::FETCH_ASSOC);

    return $likes;
}
# Chunk de code pour récupérer les dislikes
function getDislikes($pdo)
{
    $sql_dislikes = "SELECT REAGIR.ID, COUNT(REAGIR.LIKES) AS NB_DISLIKES FROM REAGIR WHERE REAGIR.LIKES = -1 GROUP BY REAGIR.ID";
    $stmt_dislikes = $pdo->prepare($sql_dislikes);
    $stmt_dislikes->execute();
    $dislikes = $stmt_dislikes->fetchAll(PDO::FETCH_ASSOC);

    return $dislikes;
}
# AJOUTE UN DISLIKE (UNIQUEMENT UN PUBLISHER PEUT FAIRE CELA)
function addDislikes($login, $id)
{
    try {
        $pdo = DBConnection::getInstance()->getConnection();
        $pdo->beginTransaction();
        $sql = "INSERT INTO REAGIR (login, id, likes) VALUES (?, ?, -1)";
        $values = array($login, $id);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $id = $pdo->lastInsertId();
        $matchingData = getDislikeData($id);
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $matchingData = false;
    }
    return $matchingData;
}

# METHODE DELETE

# SUPPRIME UN ARTICLE DEPUIS UN ID (UNIQUEMENT UN MODERATEUR PEUT FAIRE CELA)
function deleteArticleModo($id)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $matchingData = array();
    try {
        $sql = "DELETE * FROM ARTICLE WHERE id = ?";
        $values = array($id);
        $stmt = $pdo->prepare($sql);
        $matchingData[0] = ($stmt->execute($values));
    } catch (Exception $e) {
        $matchingData[0] = $e->getMessage();
    }
    return $matchingData;
}
# SUPPRIME SON PROPRE ARTICLE DEPUIS UN ID,LOGIN (UNIQUEMENT UN PUBLISHER PEUT FAIRE CELA)
function deleteArticleAuteur($id, $login)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $matchingData = array();
    try {
        $sql = "DELETE * FROM ARTICLE WHERE id = ? AND login = ?";
        $values = array($id, $login);
        $stmt = $pdo->prepare($sql);
        $matchingData[0] = ($stmt->execute($values));
    } catch (Exception $e) {
        $matchingData[0] = $e->getMessage();
    }
    return $matchingData;
}

# METHODE PUT

# MODIFIE UN ARTICLE DEPUIS UN ID,LOGIN,CONTENU et TITRE  (UNIQUEMENT UN PUBLISHER PEUT FAIRE CELA)
function updateArticle($login, $id, $contenu, $titre)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $current_article = getArticleId($id);
    if ($contenu == null) {
        $contenu = $current_article[0]['contenu'];
    }
    if ($titre == null) {
        $titre = $current_article[0]['titre'];
    }

    $sql = "UPDATE ARTICLE SET Contenu = ?, Titre = ? WHERE id = ? AND login = ?";
    $values = array($login, $id, $contenu, $titre);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $matchingData;
}
# RETOURNE LES LIKES D'UN ARTICLE DEPUIS UN ID POUR LA FONCTION addLikes
function getLikeData($id)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT COUNT(likes) FROM REAGIR WHERE id = ? and likes > 0";
    $values = array($id);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
# RETOURNE LES DISLIKES D'UN ARTICLE DEPUIS UN ID POUR LA FONCTION addDislikes
function getDislikeData($id)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT COUNT(likes) FROM REAGIR WHERE id = ? and likes < 0";
    $values = array($id);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
