<?php
/**
 * Rakaba
 *
 * @author nikserg
 */

//Подключение файла настроек базы данных.
//Подобная информация обычно разнится от сервера к серверу, поэтому
//не включается в репозиторий кода.
//Также отделение этого файла от основного кода помогает быстро находить
//и менять глобальные настройки.
include('config.php');

//Установка соединения с сервером базы данных.
//Наш сайт и сервер базы данных - это две разные программы, независимые друг от друга. Поэтому для
//работы с ним нужно создать подключение - с его помощью две программы смогут обмениваться
//информацией.
$dbConnection = mysql_connect($config['db']['host'], $config['db']['name'], $config['db']['password']);
if ($dbConnection == false)
{
    //Если подключение установить не удалось, mysql_connect вернет false. В этом случае, продолжать
    //работу бесполезно: без базы данных наш сайт не сможет работать.
    //Функция с остроумным названием die() отправит пользователю сообщение и прекратит выполнение программы.
    die('Невозможно подключиться к серверу баз данных.');
}

//После установки соединения с сервером, мы выбираем базу данных, в которой хранятся нужные нам
//таблицы. Обычно одно приложение работает в рамках одной базы данных.
$db = mysql_select_db($config['db']['name']);
if ($db == false)
{
    die('Невозможно выбрать базу данных.');
}

//Проверяем, существует ли таблица с постами. Если нет - значит, вероятно, это первый запуск программы,
//и таблицы нужно создать.
if (mysql_num_rows(mysql_query("SHOW TABLES LIKE 'posts'")) != 1)
{
    //Создаем необходимые таблицы из SQL-кода
    $initSql = file_get_contents('init.sql');

    //Разделяем на отдельные команды
    $mysqlCommands = explode(';', $initSql);

    //Выполняем каждую в отдельности
    foreach ($mysqlCommands as $mysqlCommand)
    {
        if (!$mysqlCommand)
        {
            //Пустые команды не выполняем
            continue;
        }
        if (mysql_query($mysqlCommand) === false)
        {
            die('Невозможно создать начальные таблицы: ' . mysql_error());
        }
    }
}



//В конце работы мы отключаемся от базы данных. Это поможет сократить нагрузку на сервер.
mysql_close($dbConnection);