<?php
class EmployeeDirectory {
    private $db;

    public function __construct() {
        $dsn = 'mysql:host=localhost;dbname=PTMK;charset=utf8';
        $username = 'root';
        $password = '';

        try {
            $this->db = new PDO($dsn, $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    public function createEmployeeTable() {

        $sql = "CREATE TABLE IF NOT EXISTS employees (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(255) NOT NULL,
            birth_date DATE NOT NULL,
            gender ENUM('Male', 'Female') NOT NULL
        )";

        try {

            $this->db->exec($sql);
            echo "Таблица справочника сотрудников успешно создана.\n";
        } catch (PDOException $e) {
            die("Ошибка при создании таблицы: " . $e->getMessage());
        }
    }
}


if (count($argv) < 2) {
    die("Использование: php myApp.php 1\n");
}

$mode = (int)$argv[1];

if ($mode === 1) {
    $employeeDirectory = new EmployeeDirectory();
    $employeeDirectory->createEmployeeTable();
} else {
    die("Неверный режим. Используйте 1 для создания таблицы сотрудников.\n");
}
?>
