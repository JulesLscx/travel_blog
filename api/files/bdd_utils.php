<?php

class DBConnection
{
    private static $instance = null;
    private $conn;

    /**
     * Construit un nouvel objet de connexion à la base de données.
     *
     * Cette fonction crée un nouvel objet `PDO` qui représente une connexion à une base de données MySQL.
     * Les paramètres de connexion, tels que l'hôte, le nom de la base de données, l'utilisateur, le mot de passe et le port,
     * sont spécifiés comme des constantes dans la fonction.
     *
     * @note Les attributs `PDO::ATTR_ERRMODE` et `PDO::ATTR_EMULATE_PREPARES` de la connexion
     * peuvent être définis sur `PDO::ERRMODE_EXCEPTION` et `false`, respectivement, pour activer la signalisation des erreurs
     * et désactiver les instructions préparées émulées. Ces deux lignes de code peuvent être mises en commentaires si désiré.
     */

    private function __construct()
    {
        $host = "15.188.174.107";
        $db = 'travel';
        $user = "sae";
        $password = "root";
        $port = 3306;
        try {
            $this->conn = new PDO("mysql:host=$host;dbname=$db;charset=UTF8;port=$port", $user, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    /**
     *Singleton pointant sur la base de données
     * @note regardez le fichier bdd.php et son constructeur pour modifier certains problemes
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new DBConnection();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
