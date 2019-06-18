<?php
//Обращение по адресу /files/<имя файла>.exe должно возвратить файл (file.exe),
// расположенный в корне сайта, а также установить cookie с параметром referrer равным домену,
// с которого пришел данный пользователь для закачки этого файла.
// Имя отдаваемого файла должно быть таким же, какое было в запросе.
//Например, если на сайте www.cnet.com поместили прямую ссылку на
// http://www.auslogics.com/files/myfile.exe и посетитель щелкает по ней,
// то он сможет скачать файл /files/myfile.exe (а на самом деле /file.exe)
// и на его компьютере будет оставлена cookie с referrer = cnet.com.
//Напишите скрипт (PHP), реализующий этот функционал, приведите текст .htaccess, если нужен.


$refsite = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) : '';// вычисляем с какого домена пришел клиент
$pathFragments = explode('/', $_SERVER['REQUEST_URI']);// вычисляем путь до реального размещения файла
$error = 'Такого файла нет!';//ошибка если файла нет
$file = 'file.exe';//имя реального файла
$_SERVER['REQUEST_URI'] = '/';

if ($pathFragments[2] == 'files'){

    if(file_exists($file)) {

        $real_uri = end($pathFragments);// отдаем файл (в браузере адрес изменится на реальный)
        setcookie("referrer", parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST), time() + 3600);// ставим куку
        saveFile($real_uri, $file);//сачиваем файл

    }else{
        echo $error;
    }
}

function saveFile($real_uri, $file){

    //способ один !
//    header('Content-Type: application/octet-stream');
//    header('Content-Disposition: attachment; filename=' . $real_uri);
//    exit(readfile($file));

    //способ два !
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $real_uri);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    if ($f = fopen($file, 'rb')) {
        while (!feof($f)) {
            print fread($f, 1024);
        }
        fclose($f);
    }
    exit;
}