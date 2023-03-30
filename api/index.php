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
        if ($isAuthentified) {
            if (is_authorized(0)) {
                if (empty($_GET['id'])) {
                    $matchingData = getArticleModo();
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
            return;
        }
        if (empty($_GET['id'])) {
            $matchingData = getAllArticleNonAuth();
        } else {
            if (!is_numeric($_GET['id'])) {
                deliver_response(400, "Requête invalide id non numérique", NULL);
                return;
            }
            $matchingData = getArticleId($_GET['id']);
            deliver_response(200, "Article trouvé", $matchingData);
            return;
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
        break;
    case "PUT":
        deliver_response(410, 'Méthode PUT non implémentée', NULL);
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

function getData($id = null, $limit = null)
{
    $pdo = DBConnection::getInstance()->getConnection();
    if ($limit != null) {
        $matchingData = getAllUsers($pdo);
    } else if ($limit != null) {
        $matchingData = getLikeData($pdo, $id);
    } else if ($limit != null) {
        $matchingData = getDislikeData($pdo, $id);
    } else {
        $matchingData = getAllArticle();
    }
    return $matchingData;
}

function getLimitedData($pdo, $limit)
{
    $sql = "SELECT * FROM travel ORDER BY date_ajout DESC, vote DESC LIMIT ?";
    $values = array($limit);
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}

function getLimitedDataOrderedByVotes($pdo, $limit)
{
    $sql = "SELECT * FROM travel ORDER BY vote DESC LIMIT ?";
    $values = array($limit);
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}

function getDataOrderedByVotes($pdo)
{
    $sql = "SELECT * FROM travel ORDER BY vote DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}

function getSingleData($pdo, $id)
{
    $sql = "SELECT * FROM travel WHERE id = ?";
    $values = array($id);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}

function updateAFact($id, $phrase = null, $vote = -1, $faute = -1, $signalement = -1)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $current_phrase = getData($id);
    if ($phrase == null) {
        $phrase = $current_phrase[0]['phrase'];
    }
    if ($vote == -1) {
        $vote = $current_phrase[0]['vote'];
    } else {
        $vote = $current_phrase[0]['vote'] + 1;
    }
    if ($faute == -1) {
        $faute = $current_phrase[0]['faute'];
    } else {
        $faute = $current_phrase[0]['faute'] + 1;
    }
    if ($signalement == -1) {
        $signalement = $current_phrase[0]['signalement'];
    } else {
        $signalement = $current_phrase[0]['signalement'] + 1;
    }
    $sql = "UPDATE travel SET phrase = ?, vote = ?, faute = ?, signalement = ? , date_modif = ? WHERE id = ?";
    $values = array($phrase, $vote, $faute, $signalement, date("Y-m-d H:i:s", time()), $id);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $matchingData;
}
function addphrase($phrase)
{
    // Transaction pour récupérer l'id de la phrase
    try {
        $pdo = DBConnection::getInstance()->getConnection();
        $pdo->beginTransaction();
        $sql = "INSERT INTO travel (phrase, vote, date_ajout,faute, signalement ) VALUES (?, ?, ?, ?, ?)";
        $values = array($phrase, 0, date("Y-m-d H:i:s", time()), 0, 0);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $id = $pdo->lastInsertId();
        $matchingData = getData($id);
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $matchingData = false;
    }
    return $matchingData;
}
function votePhrase($id, $vote)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $matchingData = array();
    if ($vote) {
        $vote = '+';
    } else {
        $vote = '-';
    }
    try {
        $sql = "UPDATE travel SET vote = vote " . $vote . " 1 WHERE id = ?";
        $values = array($id);
        $stmt = $pdo->prepare($sql);
        $matchingData[0] = ($stmt->execute($values));
    } catch (Exception $e) {
        $matchingData[0] = $e->getMessage();
    }
    return $matchingData;
}
function signalPhrase($id, $signalement)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $matchingData = array();
    if ($signalement) {
        $signalement = '+';
    } else {
        $signalement = '-';
    }
    try {
        $sql = "UPDATE travel SET signalement = signalement " . $signalement . " 1 WHERE id = ?";
        $values = array($id);
        $stmt = $pdo->prepare($sql);
        $matchingData[0] = ($stmt->execute($values));
    } catch (Exception $e) {
        $matchingData[0] = $e->getMessage();
    }
    return $matchingData;
}
function fautephrase($id, $faute)
{
    $pdo = DBConnection::getInstance()->getConnection();
    $matchingData = array();
    if ($faute) {
        $faute = '+';
    } else {
        $faute = '-';
    }
    try {
        $sql = "UPDATE travel SET faute = faute " . $faute . " 1 WHERE id = ?";
        $values = array($id);
        $stmt = $pdo->prepare($sql);
        $matchingData[0] = ($stmt->execute($values));
    } catch (Exception $e) {
        $matchingData[0] = $e->getMessage();
    }
    return $matchingData;
}




















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
function getAllUsers()
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT login FROM ARTICLE";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
function getAllArticleNonAuth()
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT Login, datep, Contenu FROM ARTICLE ORDER BY datep DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
function getAllArticle()
{
    $pdo = DBConnection::getInstance()->getConnection();
    $sql = "SELECT * FROM ARTICLE ORDER BY datep DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
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
function getArticleModo()
{
    $pdo = DBConnection::getInstance()->getConnection();

    // Récupérer tous les articles
    $sql_articles = "SELECT * FROM ARTICLE ORDER BY datep DESC";
    $stmt_articles = $pdo->prepare($sql_articles);
    $stmt_articles->execute();
    $articles = $stmt_articles->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les réactions de type "like"
    $sql_likes = "SELECT REAGIR.ID, GROUP_CONCAT(REAGIR.LOGIN) AS LIKES FROM REAGIR WHERE REAGIR.LIKES = 1 GROUP BY REAGIR.ID";
    $stmt_likes = $pdo->prepare($sql_likes);
    $stmt_likes->execute();
    $likes = $stmt_likes->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les réactions de type "dislike"
    $sql_dislikes = "SELECT REAGIR.ID, GROUP_CONCAT(REAGIR.LOGIN) AS DISLIKES FROM REAGIR WHERE REAGIR.LIKES = -1 GROUP BY REAGIR.ID";
    $stmt_dislikes = $pdo->prepare($sql_dislikes);
    $stmt_dislikes->execute();
    $dislikes = $stmt_dislikes->fetchAll(PDO::FETCH_ASSOC);

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
            if ($like['ID_ARTICLE'] == $id_article) {
                $matchingData[$id_article]['likes'] = explode(",", $like['LIKES']);
            }
        }
        foreach ($dislikes as $dislike) {
            if ($dislike['ID_ARTICLE'] == $id_article) {
                $matchingData[$id_article]['dislikes'] = explode(",", $dislike['DISLIKES']);
            }
        }
    }
    return $matchingData;
}
function getArticlePubli()
{
    $pdo = DBConnection::getInstance()->getConnection();

    // Récupérer tous les articles
    $sql_articles = "SELECT * FROM ARTICLE ORDER BY datep DESC";
    $stmt_articles = $pdo->prepare($sql_articles);
    $stmt_articles->execute();
    $articles = $stmt_articles->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les réactions de type "like" (sans les logins)
    $sql_likes = "SELECT REAGIR.ID, COUNT(REAGIR.LIKES) AS NB_LIKES FROM REAGIR WHERE REAGIR.LIKES = 1 GROUP BY REAGIR.ID";
    $stmt_likes = $pdo->prepare($sql_likes);
    $stmt_likes->execute();
    $likes = $stmt_likes->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les réactions de type "dislike"
    $sql_dislikes = "SELECT REAGIR.ID, COUNT(REAGIR.LIKES) AS NB_DISLIKES FROM REAGIR WHERE REAGIR.LIKES = -1 GROUP BY REAGIR.ID";
    $stmt_dislikes = $pdo->prepare($sql_dislikes);
    $stmt_dislikes->execute();
    $dislikes = $stmt_dislikes->fetchAll(PDO::FETCH_ASSOC);

    // Combinez les résultats dans un tableau associatif
    $matchingData = array();
    foreach ($articles as $article) {
        $id_article = $article['ID'];

        $matchingData[$id_article] = array(
            'article' => $article,
            'nb_likes' => 0,
            'nb_dislikes' => 0
        );

        foreach ($likes as $like) {
            if ($like['ID_ARTICLE'] == $id_article) {
                $matchingData[$id_article]['nb_likes'] = $like['NB_LIKES'];
            }
        }

        foreach ($dislikes as $dislike) {
            if ($dislike['ID_ARTICLE'] == $id_article) {
                $matchingData[$id_article]['nb_dislikes'] = $dislike['NB_DISLIKES'];
            }
        }
    }

    return $matchingData;
}
function addArticle($contenu, $login, $titre)
{
    try {
        $pdo = DBConnection::getInstance()->getConnection();
        $pdo->beginTransaction();
        $sql = "INSERT INTO ARTICLE (Contenu, Login, Titre ) VALUES (?, ?, ?)";
        $values = array($contenu, $login, $titre);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $id = $pdo->lastInsertId();
        $matchingData = getArticleId($id);
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $matchingData = false;
    }
    return $matchingData;
}
function addLikes($login, $id)
{
    try {
        $pdo = DBConnection::getInstance()->getConnection();
        $pdo->beginTransaction();
        $sql = "INSERT INTO REAGIR (login, id, likes) VALUES (?, ?, 1)";
        $values = array($login, $id);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $id = $pdo->lastInsertId();
        $matchingData = getLikeData($id);
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $matchingData = false;
    }
    return $matchingData;
}
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
