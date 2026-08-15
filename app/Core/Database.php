<?php

class Database
{
    public static ?PDO $instance = null;

    private function __construct() {}
   

    // ---------- Connexion (Singleton + fallback) ----------

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::connect();
        }

        return self::$instance;
    }

    public static function connect(): PDO
    {
        try {
            return self::connectPostgres();
        } catch (PDOException $e) {
            error_log("Connexion PostgreSQL échouée : " . $e->getMessage() . " — bascule sur SQLite.");
            return self::connectSqlite();
        }
    }

    public static function connectPostgres(): PDO
    {
        $dsn = "pgsql:host=localhost;port=5432;dbname=gest_commandes";

        return new PDO($dsn, 'postgres', 'mypostgres', [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public static function connectSqlite(): PDO
    {
        $dbPath = __DIR__ . '/../../erp.db';

        $pdo = new PDO("sqlite:{$dbPath}", null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $pdo->exec('PRAGMA foreign_keys = ON;');

        return $pdo;
    }

    
    
    public static function query(string $sql, array $params = []): array
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

   
    public static function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        $resultat = $stmt->fetch();

        return $resultat === false ? null : $resultat;
    }

    public static function insert(string $sql, array $params = []): int
{
    $pdo = self::getInstance();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return (int) $pdo->lastInsertId();
}

    
    public static function executeUpdate(string $sql, array $params = []): int
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    
    public static function beginTransaction(): void
    {
        self::getInstance()->beginTransaction();
    }

    public static function commit(): void
    {
        self::getInstance()->commit();
    }

    public static function rollBack(): void
    {
        self::getInstance()->rollBack();
    }
}