<?php

require_once '../src/modules/Logger.php';

/**
 * Classe Database
 * 
 * Cette classe représente une connexion à la base de données.
 */
class Database {
    private string $host;
    private string $database;
    private string $user;
    private string $password;

    private Logger $logger;
    private mixed $dbh = null;

    private static Database $instance;

    /**
     * Constructeur de la classe Connector.
     *
     * @param string $host L'hôte de la base de données.
     * @param string $database Le nom de la base de données.
     * @param string $user Le nom d'utilisateur pour se connecter à la base de données.
     * @param string $password Le mot de passe pour se connecter à la base de données.
     */
    function __construct(string $host, string $database, string $user, string $password) {
        $this->host = $host;
        $this->database = $database;
        $this->user = $user;
        $this->password = $password;

        $this->logger = new Logger($this, 'Database');
        $this->dbh = $this->connect();

        Database::$instance = $this;
    }

    /**
     * Renvoie une instance de la classe Database.
     *
     * @return Database L'instance de la classe Database.
     * @throws Error Si la base de données n'est pas initialisée.
     */
    public static function getInstance(): Database {
        if (Database::$instance == null)
            throw new Error('Database not initialized');
        return Database::$instance;
    }

    /**
     * Vérifie si la base de données est initialisée.
     *
     * @return bool Retourne true si la base de données est initialisée, sinon false.
     */
    public static function isInitialized(): bool {
        return Database::$instance != null;
    }

    /**
     * Établit une connexion à la base de données.
     *
     * @return PDO L'objet PDO représentant la connexion à la base de données.
     * @throws Error Si la connexion à la base de données échoue.
     */
    private function connect(): PDO {
        if ($this->dbh != null)
            return $this->dbh;

        try {
            $dbh = new PDO(
                "mysql:host=$this->host;dbname=$this->database",
                $this->user,
                $this->password,
                array(PDO::ATTR_PERSISTENT => true)
            );
        } catch (PDOException $e) {
            $this->logger->error("Impossible de se connecter au serveur SQL : " . $e->getMessage());
            throw new Error('Impossible de se connecter au serveur SQL');
        }

        return $dbh;
    }

    /**
     * Ferme la connexion à la base de données lors de la destruction de l'objet.
     *
     * Cette méthode est appelée automatiquement lorsque l'objet est détruit.
     * Elle ferme la connexion à la base de données en appelant la méthode close().
     */
    public function __destruct() {
        $this->close();
    }

    /**
     * Ferme la connexion à la base de données.
     *
     * Cette méthode ferme la connexion à la base de données en attribuant la valeur null à la propriété $dbh.
     *
     * @return void
     */
    public function close(): void {
        $this->dbh = null;
    }

    /**
     * Exécute une requête SQL préparée avec des paramètres et retourne un objet PDOStatement.
     *
     * @param string $query La requête SQL à exécuter.
     * @param array $params Les paramètres à lier à la requête (facultatif).
     * @return PDOStatement L'objet PDOStatement résultant de l'exécution de la requête.
     */
    public function query(string $query, array $params = array()): PDOStatement {
        $stmt = $this->dbh->prepare($query);

        $rawParams = $params;
        $params = [];
        foreach ($rawParams as $k => $value) {
            if ($value instanceof DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            }

            if (str_starts_with($k, ':'))
                $params[$k] = htmlspecialchars($value);
            else
                $params[':' + $k] = htmlspecialchars($value);
        }

        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Exécute une requête SQL et retourne la première ligne de résultat sous forme de tableau associatif.
     *
     * @param string $query La requête SQL à exécuter.
     * @param array $params Les paramètres de la requête (facultatif).
     * @return array Le résultat de la requête sous forme de tableau associatif.
     */
    public function fetch(string $query, array $params = array()): array {
        $stmt = $this->query($query, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les lignes résultantes d'une requête SQL.
     *
     * @param string $query La requête SQL à exécuter.
     * @param array $params Les paramètres de la requête SQL (optionnel).
     * @return array Les lignes résultantes de la requête SQL.
     */
    public function fetchAll(string $query, array $params = array()): array {
        $stmt = $this->query($query, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère la première colonne du résultat d'une requête.
     *
     * @param string $query La requête SQL à exécuter.
     * @param array $params Les paramètres de la requête (optionnel).
     * @return mixed La valeur de la première colonne du résultat.
     */
    public function fetchColumn(string $query, array $params = array()): mixed {
        $stmt = $this->query($query, $params);
        return $stmt->fetchColumn();
    }

    /**
     * Insère des données dans une table spécifiée.
     *
     * @param string $table Le nom de la table dans laquelle insérer les données.
     * @param array $data Les données à insérer dans la table.
     * @return void
     */
    public function insert(string $table, array $data): void {
        $rawData = $data;
        $data = [];
        foreach ($rawData as $k => $value) {
            if (str_starts_with($k, ':'))
                $data[$k] = htmlspecialchars($value);
            else
                $data[':' + $k] = htmlspecialchars($value);
        }

        $columns = implode(', ', array_keys($rawData));
        $values = implode(', ', array_keys($data));

        $query = "INSERT INTO $table ($columns) VALUES ($values)";
        $this->query($query, $data);
    }

    /**
     * Met à jour les données d'une table dans la base de données.
     *
     * @param string $table Le nom de la table à mettre à jour.
     * @param array $data Les données à mettre à jour, sous forme de tableau associatif.
     * @param string $where La clause WHERE pour spécifier les lignes à mettre à jour.
     * @param array $whereParams Les paramètres de la clause WHERE, sous forme de tableau associatif.
     * @return void
     */
    public function update(string $table, array $data, string $where, array $whereParams = array()): void {
        $rawData = $data;
        $data = [];
        foreach ($rawData as $k => $value) {
            if (str_starts_with($k, ':'))
                $data[$k] = htmlspecialchars($value);
            else
                $data[':' + $k] = htmlspecialchars($value);
        }

        $set = [];
        foreach ($rawData as $k => $v) {
            $set[] = "$k = :$k";
        }
        $set = implode(', ', $set);

        $query = "UPDATE $table SET $set WHERE $where";
        $this->query($query, array_merge($data, $whereParams));
    }

    /**
     * Supprime les enregistrements d'une table spécifiée en fonction d'une condition WHERE.
     *
     * @param string $table Le nom de la table à partir de laquelle supprimer les enregistrements.
     * @param string $where La condition WHERE pour filtrer les enregistrements à supprimer.
     * @param array $whereParams Les paramètres de la condition WHERE (facultatif).
     * @return void
     */
    public function delete(string $table, string $where, array $whereParams = array()): void {
        $query = "DELETE FROM $table WHERE $where";
        $this->query($query, $whereParams);
    }

    /**
     * Renvoie l'identifiant de la dernière ligne insérée dans la base de données.
     *
     * @return string L'identifiant de la dernière ligne insérée.
     */
    public function lastInsertId(): string {
        return $this->dbh->lastInsertId();
    }

    /**
     * Compte le nombre de lignes dans une table qui satisfont une condition donnée.
     *
     * @param string $table Le nom de la table.
     * @param string $column Le nom de la colonne à compter.
     * @param string $where La condition à satisfaire.
     * @param array $whereParams Les paramètres de la condition (facultatif).
     * @return int Le nombre de lignes qui satisfont la condition.
     */
    public function count(string $table, string $column, string $where, array $whereParams = array()): int {
        $query = "SELECT COUNT($column) FROM $table WHERE $where";
        $stmt = $this->query($query, $whereParams);
        return $stmt->fetchColumn();
    }
}
