<?php
require('./files/api_utils.php');
require('./files/bdd_utils.php');
header("Content-Type:application/json");
/// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];
switch ($http_method) {
        /// Cas de la méthode GET
    case "AUTH":
        break;
    case "GET":
        /// Récupération des critères de recherche envoyés par le Client
        if (!empty($_GET['last'])) {
            if (!is_numeric($_GET['last'])) {
                deliver_response(400, "Requête invalide", NULL);
                exit;
            }
            $limit = $_GET['last'];
        } else {
            $limit = null;
        }
        $orderByVote = isset($_GET['orderByVote']);
        $signal = isset($_GET['signal']);
        if (empty($_GET['id'])) {
            $matchingData = getData(null, $limit, $orderByVote, $signal);
        } else {
            if (!is_numeric($_GET['id'])) {
                deliver_response(400, "Requête invalide", NULL);
                exit;
            }
            $matchingData = getData($_GET['id'], $limit);
        }
        /// Envoi de la réponse au Client
        deliver_response(200, "Voici les données demandées !", $matchingData);
        break;
        /// Cas de la méthode POST
    case "POST":
        /// Récupération des données envoyées par le Client
        $postedData = file_get_contents('php://input');

        if (empty($postedData)) {
            deliver_response(400, "Requête invalide", NULL);
            return;
        }
        $postedData = json_decode($postedData, true);
        if (empty($postedData['phrase'])) {
            deliver_response(400, "Requête invalide", NULL);
            return;
        }
        $matchingData = addphrase($postedData['phrase']);
        if (empty($matchingData) || $matchingData == false) {
            deliver_response(500, "Erreur lors de l'insertion de la phrase", NULL);
            return;
        }
        deliver_response(201, "Phrase insérée", $matchingData);
        break;
        /// Cas de la méthode PUT
    case "PATCH":
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
        if (isset($postedData['vote'])) {
            $vote = $postedData['vote'];
            votePhrase($_GET['id'], $vote);
            deliver_response(200, "Vote altéré", NULL);
            return;
        }
        if (isset($postedData['signalement'])) {
            $signalement = $postedData['signalement'];
            signalPhrase($_GET['id'], $signalement);
            deliver_response(200, "Signalement altéré", NULL);
            return;
        }
        if (isset($postedData['faute'])) {
            $faute = $postedData['faute'];
            fautePhrase($_GET['id'], $faute);
            deliver_response(200, "Faute altérée", NULL);
            return;
        }
        deliver_response(400, "Requête invalide", NULL);
        break;
    case "PUT":
        if (empty($_GET['id'])) {
            deliver_response(400, "Requête invalide id indéfini", NULL);
            return;
        }
        if (!is_numeric($_GET['id'])) {
            deliver_response(400, "Requête invalide id non numérique", NULL);
            return;
        }
        /// Récupération des données envoyées par le Client
        $postedData = file_get_contents('php://input');
        if (empty($postedData['phrase'])) {
            $phrase = null;
        } else {
            $phrase = $postedData['phrase'];
        }
        if (empty($postedDate['vote'])) {
            $vote = -1;
        } else {
            $vote = $postedData['vote'];
        }
        if (empty($postedData['signalement'])) {
            $signalement = -1;
        } else {
            $signalement = $postedData['signalement'];
        }
        if (empty($postedData['faute'])) {
            $faute = -1;
        } else {
            $faute = $postedData['faute'];
        }
        try {
            $matchingData = updateAFact($_GET['id'], $phrase, $vote, $signalement, $faute);
            deliver_response(200, "Phrase mise à jour", $matchingData);
        } catch (Exception $e) {
            deliver_response(500, "Erreur lors de la mise à jour de la phrase", NULL);
        }
        break;
        /// Cas de la méthode DELETE
    case "DELETE":
        /// Récupération de l'identifiant de la ressource envoyé par le Client
        if (!empty($_GET['id'])) {
            $matchingData = deleteArticle($_GET['id']);
            deliver_response(200, "Pas d'erreurs", $matchingData);
            return;
        }
        /// Envoi de la réponse au Client
        deliver_response(401, "Id indéfini", NULL);
        break;
        /// Cas par défaut
    default:
        /// Envoi de la réponse au Client
        deliver_response(400, "Aucune action effectuée relisez ", NULL);
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
        $matchingData = getAllArticle($pdo);
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





















function getArticleTag($pdo, $tag)
{
    $sql = "SELECT ARTICLE.* FROM ARTICLE JOIN TAGS ON ARTICLE.ID=TAGS.ID WHERE TAG = ?";
    $values = array($tag);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
function getArticleDate($pdo, $date)
{
    $sql = "SELECT * FROM ARTICLE WHERE datep = ?";
    $values = array($date);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
function getArticleTitre($pdo, $titre)
{
    $sql = "SELECT * FROM ARTICLE WHERE titre = ?";
    $values = array($titre);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
function getAllUsers($pdo)
{
    $sql = "SELECT login FROM ARTICLE";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
function getAllArticle($pdo)
{
    $sql = "SELECT * FROM ARTICLE ORDER BY datep DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
function getArticleAuteur($pdo, $login)
{
    $sql = "SELECT * FROM ARTICLE WHERE login = ?";
    $values = array($login);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
function deleteArticle($id)
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
function getLikeData($pdo, $id)
{
    $sql = "SELECT COUNT(likes) FROM REAGIR WHERE id = ? and likes > 0";
    $values = array($id);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
function getDislikeData($pdo, $id)
{
    $sql = "SELECT COUNT(likes) FROM REAGIR WHERE id = ? and likes < 0";
    $values = array($id);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $matchingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $matchingData;
}
