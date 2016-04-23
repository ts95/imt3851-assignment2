<?php

include_once __DIR__ . '/settings.php';

class CustomPDO extends PDO {
    
    public function __construct($settings) {
        $dbname = $settings['dbname'];
        $username = $settings['username'];
        $password = $settings['password'];

        parent::__construct("mysql:", $username, $password);
        parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        parent::setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

        $query = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'";
        $dbExists = (bool) parent::query($query)->fetchColumn();

        if (!$dbExists) {
            parent::query('CREATE DATABASE ' . $dbname);
            parent::query('USE ' . $dbname);

            $createSQL = file_get_contents(__DIR__ . '/../sql/create.sql');
            parent::exec($createSQL);

            $seedSQL = file_get_contents(__DIR__ . '/../sql/seed.sql');
            parent::exec($seedSQL);
        } else {
            parent::query('USE ' . $dbname);
        }
    }

    /**
     * Both of these signatures are possible:
     * #query('SELECT * FROM user WHERE id = ?', [1]);
     * #query('SELECT * FROM user WHERE id = :id', [
     *     ':id' => 1,
     * ]);
     */
    public function query($q, $args = []) {
        $sth = parent::prepare($q);

        if ($this->isAssoc($args)) {
            foreach ($args as $param => $value) {
                $sth->bindValue($param, $value);
            }
            $sth->execute();
            return $sth;
        } else {
            $sth->execute($args);
            return $sth;
        }
    }

    private function isAssoc($array) {
        // Keys of the array
        $keys = array_keys($array);
        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }
}

$db = new CustomPDO([
    'dbname' => DB_NAME,
    'username' => DB_USERNAME,
    'password' => DB_PASSWORD,
]);