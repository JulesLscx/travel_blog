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
            $matchingData = getArticleIdModo($_GET['id']);
            if (empty($matchingData)) {
                deliver_response(404, "Aucun article trouvé", NULL);
                return;
            }
            deliver_response(200, "Article trouvé", $matchingData);
            return;
        }
        if (is_authorized(1)) {
            if (!empty($_GET['account'])) {
                $matchingData = getArticleAuteur($token_content->login);
                deliver_response(200, "Article trouvé", $matchingData);
                return;
            }
            if (empty($_GET['id'])) {
                $matchingData = getArticlePubli();
            } else {
                if (!is_numeric($_GET['id'])) {
                    deliver_response(400, "Requête invalide id non numérique", NULL);
                    return;
                }
                $matchingData = getArticleIdPubli($_GET['id']);
            }
            if (empty($matchingData)) {
                deliver_response(404, "Aucun article trouvé", NULL);
                return;
            }
            deliver_response(200, "Article trouvé", $matchingData);
        }
        break;
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
        if (!$isAuthentified) {
            deliver_response(401, "Vous n'êtes pas authentifié", NULL);
            return;
        }
        if (!is_authorized(1)) {
            deliver_response(403, "Vous n'avez pas les droits pour liker un article", NULL);
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
        $article = getArticleId($_GET['id']);
        if (empty($article)) {
            deliver_response(404, "Aucun article trouvé", NULL);
            return;
        }
        if ($article[0]['Login'] == $token_content->login) {
            deliver_response(403, "Vous n'allez pas liker votre propre article", NULL);
            return;
        }
        $postedData = file_get_contents('php://input');
        $postedData = json_decode($postedData, true);
        if (empty($postedData['like'])) {
            deliver_response(400, "Requête invalide like indéfini", NULL);
            return;
        }
        if ($postedData['like'] == 1) {
            $matchingData = addLikes($token_content->login, $_GET['id']);
            if (empty($matchingData) || $matchingData == false) {
                deliver_response(500, "Erreur lors du like de l'article", NULL);
                return;
            }
            deliver_response(200, "Article liké", $matchingData);
            return;
        }
        if ($postedData['like'] == -1) {
            $matchingData = addDislikes($token_content->login, $_GET['id']);
            if (empty($matchingData) || $matchingData == false) {
                deliver_response(500, "Erreur lors du dislike de l'article", NULL);
                return;
            }
            deliver_response(200, "Article disliké", $matchingData);
            return;
        }
        deliver_response(400, "Requête invalide like non conforme", NULL);
        break;
    case "PUT":
        if (!$isAuthentified) {
            deliver_response(401, "Vous n'êtes pas authentifié", NULL);
            return;
        }
        if (!is_authorized(1)) {
            deliver_response(403, "Vous n'avez pas les droits pour modifier un article", NULL);
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
        $postedData = file_get_contents('php://input');
        $postedData = json_decode($postedData, true);
        $article = getArticleId($_GET['id']);
        if (empty($article)) {
            deliver_response(404, "Aucun article trouvé", NULL);
            return;
        }
        if ($article[0]['Login'] != $token_content->login) {
            deliver_response(403, "Vous n'avez pas les droits pour modifier cet article ce n'est pas le vôtre", NULL);
            return;
        }
        if (empty($postedData['titre']) || empty($postedData['contenu'])) {
            deliver_response(400, "Requête invalide titre ou contenu indéfini", NULL);
            return;
        }
        $matchingData = updateArticle($token_content->login, $_GET['id'], $postedData['contenu'], $postedData['titre']);
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
        $article = getArticleId($_GET['id']);
        if (empty($article)) {
            deliver_response(404, "Aucun article trouvé", NULL);
            return;
        }
        if (is_authorized(1) &&  $article[0]['Login'] != $token_content->login) {
            deliver_response(403, "Vous n'avez pas les droits pour supprimer cet article ce n'est pas le vôtre", NULL);
            return;
        }
        $matchingData = deleteArticle($_GET['id']);
        if (empty($matchingData) || $matchingData == false) {
            deliver_response(500, "Erreur lors de la suppression de l'article", NULL);
            return;
        }
        deliver_response(200, "Article supprimé", $matchingData);
        break;
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
    $pdo->beginTransaction();
    $sql = "INSERT INTO ARTICLE (titre, contenu, login) VALUES (?, ?, ?)";
    $values = array($titre, $contenu, $login);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $sql = "SELECT * FROM ARTICLE ORDER BY DATEP DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo->commit();
    return $matchingData;
}
# METHODES GET :

# RETOURNE UN ARTICLE AVEC UN ID
function getArticleId($id)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT Titre, Login, datep, Contenu FROM ARTICLE WHERE id = ?";
    $values = array($id);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}

function getArticleIdModo($id)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT *  FROM ARTICLE WHERE id = ?";
    $values = array($id);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = $matchingData[0];
    $result['LIKES'] = getLikes($pdo, $id);
    $result['DISLIKES'] = getDislikes($pdo, $id);
    return $result;
}

function getArticleIdPubli($id)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT * FROM ARTICLE WHERE id = ?";
    $values = array($id);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = $matchingData[0];
    $result['LIKES'] = getLikeData($id);
    $result['DISLIKES'] = getDislikeData($id);
    return $result;
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

# RETOURNE TOUS LES ARTICLES D'UN AUTEUR/PUBLISHER
function getArticleAuteur($login)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT * FROM ARTICLE WHERE login = ?";
    $values = array($login);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($matchingData as $key => $value) {
        $matchingData[$key]['LIKES'] = getLikeData($value['ID']);
        $matchingData[$key]['DISLIKES'] = getDislikeData($value['ID']);
    }
    return $matchingData;
}
# RETOURNE ABSOLUMENT TOUS LES ARTICLES 
function getArticleModo()
{
    $pdo = DBConnection::getInstance()->getConnection();
    $articles = getArticles($pdo);

    // Combinez les résultats dans un tableau associatif
    $matchingData = array();
    foreach ($articles as $article) {
        $id_article = $article['ID'];
        $matchingData[$id_article] = array(
            'article' => $article,
            'likes' => array(),
            'dislikes' => array()
        );
        $matchingData[$id_article]['likes'] = getLikes($pdo, $id_article);
        $matchingData[$id_article]['dislikes'] = getDislikes($pdo, $id_article);
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
function getLikes($pdo, $id)
{
    $sql_likes = "SELECT login FROM REAGIR WHERE id = ? and REAGIR.LIKES = 1 GROUP BY REAGIR.ID";
    $stmt_likes = $pdo->prepare($sql_likes);
    $stmt_likes->bindParam(1, $id);
    $stmt_likes->execute();
    $likes = $stmt_likes->fetchAll(PDO::FETCH_ASSOC);
    $useful_data = new ArrayObject();
    foreach ($likes as $like) {
        $useful_data->append($like['login']);
    }
    return $useful_data->getArrayCopy();
}
# Chunk de code pour récupérer les dislikes
function getDislikes($pdo, $id)
{
    $sql_dislikes = "SELECT login FROM REAGIR WHERE id = ? and REAGIR.LIKES = -1 GROUP BY REAGIR.ID";
    $stmt_dislikes = $pdo->prepare($sql_dislikes);
    $stmt_dislikes->bindParam(1, $id);
    $stmt_dislikes->execute();
    $dislikes = $stmt_dislikes->fetchAll(PDO::FETCH_ASSOC);
    $useful_data = new ArrayObject();
    foreach ($dislikes as $dislike) {
        $useful_data->append($dislike['login']);
    }
    return $useful_data->getArrayCopy();
}
# AJOUTE UN DISLIKE (UNIQUEMENT UN PUBLISHER PEUT FAIRE CELA)
function addDislikes($login, $id)
{
    try {
        $pdo = DBConnection::getInstance()->getConnection();
        $sql = "INSERT INTO REAGIR (login, id, likes) VALUES (?, ?, -1)";
        $values = array($login, $id);
        $stmt = $pdo->prepare($sql);
        $matchingData = $stmt->execute($values);
    } catch (Exception $e) {
        $matchingData = false;
    }
    return $matchingData;
}

# AJOUTE UN LIKE (UNIQUEMENT UN PUBLISHER PEUT FAIRE CELA)
function addLikes($login, $id)
{
    try {
        $pdo = DBConnection::getInstance()->getConnection();
        $sql = "INSERT INTO REAGIR (login, id, likes) VALUES (?, ?, 1)";
        $values = array($login, $id);
        $stmt = $pdo->prepare($sql);
        $matchingData = $stmt->execute($values);
    } catch (Exception $e) {
        $matchingData = false;
    }
    return $matchingData;
}

# METHODE DELETE

# SUPPRIME UN ARTICLE DEPUIS UN ID (UNIQUEMENT UN MODERATEUR PEUT FAIRE CELA)
function deleteArticle($id)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $matchingData = array();
    try {
        $sql = "DELETE FROM ARTICLE WHERE id = ?";
        $values = array($id);
        $stmt = $pdo->prepare($sql);
        $matchingData = $stmt->execute($values);
    } catch (Exception $e) {
        $matchingData = $e->getMessage();
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

    $sql = "UPDATE ARTICLE SET CONTENU = ?, TITRE = ? WHERE ID = ? AND login = ?";
    $values = array($contenu, $titre, $id, $login);
    $stmt = $pdo->prepare($sql);
    $matchingData = $stmt->execute($values);

    return $matchingData;
}
# RETOURNE LES LIKES D'UN ARTICLE DEPUIS UN ID POUR LA FONCTION addLikes
function getLikeData($id)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT COUNT(likes) AS DISLIKES FROM REAGIR WHERE id = ? and likes > 0";
    $values = array($id);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData[0]['DISLIKES'];
}
# RETOURNE LES DISLIKES D'UN ARTICLE DEPUIS UN ID POUR LA FONCTION addDislikes
function getDislikeData($id)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT COUNT(likes) as LIKES FROM REAGIR WHERE id = ? and likes < 0";
    $values = array($id);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData[0]['LIKES'];
}
