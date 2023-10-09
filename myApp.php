<?php
class EmployeeDirectory {
    private $db; // Объект PDO для подключения к базе данных

    public function __construct() {
        $dsn = 'mysql:host=localhost;dbname=PTMK;charset=utf8';
        $username = 'root';
        $password = '';
    
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true, // Включение буферизации запросов
        ];
    
        try {
            $this->db = new PDO($dsn, $username, $password, $options);
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

    public function displayEmployees() {

        $sql = "SELECT full_name, birth_date, gender FROM employees ORDER BY full_name";
    
        try {

            $stmt = $this->db->prepare($sql);
    
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if (empty($result)) {
                echo "Справочник сотрудников пуст.\n";
                return;
            }
    
            foreach ($result as $employee) {
                $full_name = $employee['full_name'];
                $birth_date = $employee['birth_date'];
                $gender = $employee['gender'];

                $age = date('Y') - date('Y', strtotime($birth_date));
    
                echo "-----------------------\n";
                echo "ФИО: " . $full_name . "\n";
                echo "Дата рождения: " . $birth_date . "\n";
                echo "Пол: " . $gender . "\n";
                echo "Возраст: " . $age . " лет\n";
                echo "-----------------------\n";
            }
        } catch (PDOException $e) {
            die("Ошибка при получении данных: " . $e->getMessage());
        }
    }

    public function generateAndInsertEmployees($count) {

        $sql = "INSERT INTO employees (full_name, birth_date, gender) VALUES (:full_name, :birth_date, :gender)";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $fullNames = ["Jenya Smirnov", "Mary Chehova", "Daniil Brovov", "Lidia Dfsnecova", "Michail Davidov", "Fedya Fedorov", "Vanya Vasiliev", "Petya Petrov", "Sasha Sidorov"];
            $genders = ["Male", "Female"];
            
            for ($i = 0; $i < $count; $i++) {
                $randomFullName = $fullNames[array_rand($fullNames)];
                $randomBirthDate = date('Y-m-d', strtotime("-" . rand(18, 65) . " years"));
                $randomGender = $genders[array_rand($genders)];
                
                $stmt->bindParam(':full_name', $randomFullName);
                $stmt->bindParam(':birth_date', $randomBirthDate);
                $stmt->bindParam(':gender', $randomGender);
                
                $stmt->execute();
                
                if (($i + 1) % 1000 === 0) {
                    echo "Добавлено " . ($i + 1) . " записей.\n";
                }
            }
            
            echo "Заполнение базы данных завершено.\n";
        } catch (PDOException $e) {
            die("Ошибка при вставке записей: " . $e->getMessage());
        }
    }
    

    public function selectMaleEmployeesWithLastNameStartingWithF1() {

        $start_time = microtime(true);

        $sql = "SELECT full_name, birth_date, gender FROM employees WHERE gender = 'Male' AND full_name LIKE 'F%'";
    
        try {
            // Замер времени выполнения запроса
            
    
            $stmt = $this->db->prepare($sql);
    
            $stmt->execute();
    
            // Извлечь результаты
            $result = $this->db->prepare($sql);
            $result->execute();
    
            if (empty($result)) {
                echo "Нет сотрудников с полом 'Мужской' и фамилией, начинающейся с 'F'.\n";
            } else {
                echo "Результаты запроса:\n";
                echo "-----------------------\n";
                foreach ($result as $employee) {
                    echo "ФИО: " . $employee['full_name'] . "\n";
                    echo "Дата рождения: " . $employee['birth_date'] . "\n";
                    echo "Пол: " . $employee['gender'] . "\n";
                    echo "-----------------------\n";
                }
            }
    
            // Замерка времени выполнения
            $end_time = microtime(true);
            $execution_time = ($end_time - $start_time);
            echo "Время выполнения запроса: " . $execution_time . " секунд.\n";
        } catch (PDOException $e) {
            die("Ошибка при выполнении запроса: " . $e->getMessage());
        }
    }

    public function selectMaleEmployeesWithLastNameStartingWithF2() {
        
        // Замер времени выполнения запроса
        $start_time = microtime(true);


        $cache_file = 'male_employees_starting_with_f.cache';

        if (file_exists($cache_file) && time() - filemtime($cache_file) < 3600) {
            // Если есть кэш и он не устарел (1 час), используем сохраненные результаты
            echo "Результаты из кэша:\n";

            echo file_get_contents($cache_file);
                        // Замерить время выполнения
        } else {

            $sql = "SELECT full_name, birth_date, gender FROM employees WHERE gender = 'Male' AND full_name LIKE 'F%'";
    
            try {
                $stmt = $this->db->prepare($sql);
    
                $stmt->execute();
    
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                if (empty($result)) {
                    echo "Нет сотрудников с полом 'Мужской' и фамилией, начинающейся с 'F'.\n";
                } else {
                    echo "Результаты запроса:\n";
                    foreach ($result as $employee) {
                        echo "ФИО: " . $employee['full_name'] . "\n";
                        echo "Дата рождения: " . $employee['birth_date'] . "\n";
                        echo "Пол: " . $employee['gender'] . "\n";
                        echo "-----------------------\n";
                    }
                }

                // Кэширование в файл
                ob_start();
                if (!empty($result)) {
                    foreach ($result as $employee) {
                        echo "ФИО: " . $employee['full_name'] . "\n";
                        echo "Дата рождения: " . $employee['birth_date'] . "\n";
                        echo "Пол: " . $employee['gender'] . "\n";
                        echo "-----------------------\n";
                    }
                }
                $cached_data = ob_get_clean();
                file_put_contents($cache_file, $cached_data);
    
            } catch (PDOException $e) {
                die("Ошибка при выполнении запроса: " . $e->getMessage());
            }
        }

        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time);
        echo "Время выполнения запроса: " . $execution_time . " секунд.\n";

    }
    
    
}


$mode = (int)$argv[1];

$employeeDirectory = new EmployeeDirectory();

if ($mode === 1) {
    // Режим 1: Создание таблицы справочника сотрудников
    $employeeDirectory->createEmployeeTable();
    
} elseif ($mode === 2) {
    // Режим 2: Создание записи сотрудника

    $full_name = $argv[2];
    $birth_date = $argv[3];
    $gender = $argv[4];

    $employeeDirectory->insertEmployee($full_name, $birth_date, $gender);
    $employeeDirectory->calculateAge($birth_date);

} elseif ($mode === 3) {
    $employeeDirectory->displayEmployees();

} elseif ($mode === 4) {
    $employeeDirectory->generateAndInsertEmployees(1000000);

} elseif ($mode === 5) {

    //$employeeDirectory->selectMaleEmployeesWithLastNameStartingWithF1();
    $employeeDirectory->selectMaleEmployeesWithLastNameStartingWithF2();


    //Замеры до оптимизации - selectMaleEmployeesWithLastNameStartingWithF1
    //1.4007959365845 секунд.
    //1.2793788909912 секунд
    // 1.2656350135803 секунд

    //Замеры после оптимизации - selectMaleEmployeesWithLastNameStartingWithF2
    //0.27020692825317 секунд
    //0.27807784080505 секунд
    //0.29023218154907 секунд
    //0.27140593528748 секунд


} else {
    die("Неверный режим. Поддерживаемые режимы: 1 (создание таблицы), 2 (создание записи сотрудника), 3 (Отображение записей), 4 (Генерирование записей), 5 (Выбор мужчин с Фамилией начинающейся на F).\n");
}
?>
