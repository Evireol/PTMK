
# Выполнение запросов с 100 000 записей

## Режимы работы приложения:

PowerShell : **myApp 1** - Создание таблицы с полями справочника сотрудников, представляющими "Фамилию Имя Отчество", "дату рождения", "пол"

PowerShell : **php myApp.php 2 "Лунова Луна Луновична" "2000-01-15" "Женский"** - Заполнение записи, и расчет возраста по дате

PowerShell : **myApp 3** - Вывод всех строк справочника сотрудников, с уникальным значением ФИО+дата, отсортированным по ФИО. Вывод ФИО, Даты рождения, пола, кол-ва полных лет.

PowerShell : **myApp 4** - Заполнение автоматически 100 000 строк справочника сотрудников. (Можно изменить на нужно количество )

```php
($mode === 4) {
    $employeeDirectory->generateAndInsertEmployees(1000000);
```

PowerShell : **myApp 5** - выборки из таблицы по критерию: пол мужской, Фамилия начинается с "F" (с оптимизацией вывода)


Можно заменить функцию в коде и выбрать вывести с либо без оптимизации: 

```php

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

```


## Результаты :

**myApp 1**:

![result](https://github.com/Evireol/PTMK/blob/main/pngForReadme/result%201.png)

**myApp 4**

![result](https://github.com/Evireol/PTMK/blob/main/pngForReadme/result%204.png)

**myApp 3**

![result](https://github.com/Evireol/PTMK/blob/main/pngForReadme/result%203.png)

**myApp 5** без оптимизации

![result](https://github.com/Evireol/PTMK/blob/main/pngForReadme/result5-1.png)

**myApp 5** с оптимизацией

![result](https://github.com/Evireol/PTMK/blob/main/pngForReadme/result5-2.png)

