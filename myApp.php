<?php
class EmployeeDirectory {
    private $db; // Объект PDO для подключения к базе данных

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

    public function insertEmployee($full_name, $birth_date, $gender) {

        $sql = "INSERT INTO employees (full_name, birth_date, gender) VALUES (:full_name, :birth_date, :gender)";

        try {

            $stmt = $this->db->prepare($sql);

            // Привязать параметры
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':birth_date', $birth_date);
            $stmt->bindParam(':gender', $gender);

            $stmt->execute();

            echo "Запись сотрудника успешно добавлена в базу данных.\n";
        } catch (PDOException $e) {
            die("Ошибка при вставке записи: " . $e->getMessage());
        }
    }

    public function calculateAge($birth_date) {
        $birth_date_datetime = new DateTime($birth_date);

        // DateTime для текущей даты и времени
        $current_datetime = new DateTime();
        
        // Вычисление разницы между датами
        $interval = $birth_date_datetime->diff($current_datetime);
        echo $interval->format('Возраст: %y лет');
    }


}

if (count($argv) < 2) {
    die("Использование: php myApp.php [режим] [дополнительные аргументы]\n");
}

$mode = (int)$argv[1];

$employeeDirectory = new EmployeeDirectory();

if ($mode === 1) {
    // Режим 1: Создание таблицы справочника сотрудников
    $employeeDirectory->createEmployeeTable();
} elseif ($mode === 2) {
    // Режим 2: Создание записи сотрудника
    if (count($argv) < 5) {
        die("Использование: php myApp.php 2 [ФИО] [дата рождения] [пол]\n");
    }

    $full_name = $argv[2];
    $birth_date = $argv[3];
    $gender = $argv[4];

    $employeeDirectory->insertEmployee($full_name, $birth_date, $gender);
    $employeeDirectory->calculateAge($birth_date);
} else {
    die("Неверный режим. Поддерживаемые режимы: 1 (создание таблицы), 2 (создание записи сотрудника).\n");
}
?>
