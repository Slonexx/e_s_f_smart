<?php

namespace App\Http\Controllers\Setting;

use App\Clients\ClientXML;
use App\Http\Controllers\Controller;
use DOMDocument;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;



class testController extends Controller
{
    public function testController(Request $request,)
    {

        $p12File = 'RSA256_348bb7da4b1090d0a19bf00bbc55c410dcc03b27.p12';

// Пароль для файла P12
        $p12Password = 'eiprGQ86';

// Загрузка содержимого файла P12
        $p12Content = file_get_contents($p12File);

// Открытие файла P12 и извлечение сертификата и закрытого ключа
        if (openssl_pkcs12_read($p12Content, $p12Data, $p12Password)) {
            // Извлечение сертификата
            $certificate = openssl_x509_read($p12Data['cert']);

            // Генерация онлайн-подписи
            $dataToSign = '.';
            $signature = '';

            if (openssl_sign($dataToSign, $signature, $p12Data['pkey'], OPENSSL_ALGO_SHA256)) {
                // Преобразование подписи в формат base64
                $base64Signature = base64_encode($signature);

                // Убедитесь, что длина подписи равна 88 символам
                $signatureLength = strlen($base64Signature);
                if ($signatureLength !== 88) {
                    // Добавьте код обработки ошибки, если длина подписи некорректна
                }

                // Вывод подписи
                dd($base64Signature,$signatureLength);
            } else {
                // Обработка ошибки при генерации подписи
            }
        }






















        $xml = '<ESF><CompletionDate>2023-05-19</CompletionDate></ESF>';

// Путь к файлу с ключами и сертификатом p12
        $p12File = 'RSA256_348bb7da4b1090d0a19bf00bbc55c410dcc03b27.p12';

// Пароль для доступа к файлу p12
        $p12Password = 'eiprGQ86';

// Загрузка ключей и сертификата из файла p12
        $keyStore = array();
        if (openssl_pkcs12_read(file_get_contents($p12File), $keyStore, $p12Password)) {
            $privateKey = $keyStore['pkey']; // Закрытый ключ
            $cert = $keyStore['cert']; // Сертификат
        } else {
            die('Ошибка загрузки файла p12 или неверный пароль.');
        }

// Создание хэша XML-документа
        $hash = hash('sha256', $xml, true);

// Создание цифровой подписи
        openssl_sign($hash, $signature, $privateKey, OPENSSL_ALGO_SHA256);

// Кодирование цифровой подписи в строку
        $signatureString = base64_encode($signature);
        $publicKey = openssl_pkey_get_public($cert);
        $signatureValid = openssl_verify($hash, base64_decode($signatureString), $publicKey, OPENSSL_ALGO_SHA256);



dd($signatureValid, $signatureString);











        $accountId = "1dd5bd55-d141-11ec-0a80-055600047495";

        $p12File = 'RSA256_348bb7da4b1090d0a19bf00bbc55c410dcc03b27.p12';
        $password = 'eiprGQ86';

        $privateKey = null;
        $RSA256 = null;
        openssl_pkcs12_read(file_get_contents($p12File), $RSA256, $password);
        $privateKey = $RSA256['pkey'];

        $privateKey = $RSA256['pkey'];
        $publicKey = openssl_pkey_get_public($RSA256['cert']);
        $certInfo = openssl_x509_parse($RSA256['cert']);

        $dataToSign = 'Data to be signed';
        $signature = '';

        openssl_sign($dataToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        // Преобразуйте сигнатуру в формат base64


        dd($publicKey,$certInfo,  $base64Signature = base64_encode($signature));

        //$privateKey = "MIIEezCCBCWgAwIBAgIUViudVywx90JxrW5xhr7T1mQDjO4wDQYJKoMOAwoBAQECBQAwUzELMAkGA1UEBhMCS1oxRDBCBgNVBAMMO9Kw0JvQotCi0KvSmiDQmtCj05jQm9CQ0J3QlNCr0KDQo9Co0Ksg0J7QoNCi0JDQm9Cr0pogKEdPU1QpMB4XDTIyMDgwNDA3NDMxOFoXDTIzMDgwNDA3NDMxOFowggElMSEwHwYDVQQEDBjQodCQ0JvQkNCl0KPQotCU0JjQndCe0JIxLjAsBgNVBAMMJdCh0JDQm9CQ0KXQo9Ci0JTQmNCd0J7QkiDQoNCj0KHQotCQ0JwxGDAWBgNVBAUTD0lJTjg2MDUwNTMwMDQ5MjELMAkGA1UEBhMCS1oxGDAWBgNVBAsMD0JJTjE3MDk0MDAwMDU1MDEbMBkGA1UEKgwS0KDQmNCd0JDQotCe0JLQmNCnMXIwcAYDVQQKDGnQotCe0JLQkNCg0JjQqdCV0KHQotCS0J4g0KEg0J7Qk9Cg0JDQndCY0KfQldCd0J3QntCZINCe0KLQktCV0KLQodCi0JLQldCd0J3QntCh0KLQrNCuICJTTUFSVCBJTk5PVkFUSU9OUyIwbDAlBgkqgw4DCgEBAQEwGAYKKoMOAwoBAQEBAQYKKoMOAwoBAwEBAANDAARAsV2j8R3mED3IDbe2xzBmfIgvVforRPuu8So+AIx9SPSAL/H/KtS3bpHMOAxPJGSF5ZYYE0c/e30NVv/n9fUjJqOCAeswggHnMA4GA1UdDwEB/wQEAwIGwDAoBgNVHSUEITAfBggrBgEFBQcDBAYIKoMOAwMEAQIGCSqDDgMDBAECATAPBgNVHSMECDAGgARbanPpMB0GA1UdDgQWBBTwMrlEFjfUNGIDkpKAeeJTZp3JcDBeBgNVHSAEVzBVMFMGByqDDgMDAgEwSDAhBggrBgEFBQcCARYVaHR0cDovL3BraS5nb3Yua3ovY3BzMCMGCCsGAQUFBwICMBcMFWh0dHA6Ly9wa2kuZ292Lmt6L2NwczBYBgNVHR8EUTBPME2gS6BJhiJodHRwOi8vY3JsLnBraS5nb3Yua3ovbmNhX2dvc3QuY3JshiNodHRwOi8vY3JsMS5wa2kuZ292Lmt6L25jYV9nb3N0LmNybDBcBgNVHS4EVTBTMFGgT6BNhiRodHRwOi8vY3JsLnBraS5nb3Yua3ovbmNhX2RfZ29zdC5jcmyGJWh0dHA6Ly9jcmwxLnBraS5nb3Yua3ovbmNhX2RfZ29zdC5jcmwwYwYIKwYBBQUHAQEEVzBVMC8GCCsGAQUFBzAChiNodHRwOi8vcGtpLmdvdi5rei9jZXJ0L25jYV9nb3N0LmNlcjAiBggrBgEFBQcwAYYWaHR0cDovL29jc3AucGtpLmdvdi5rejANBgkqgw4DCgEBAQIFAANBAN9Catl/MZSAyqadaAAjWO8fg3/BVoaNOf1ve4MufBogb7PEez5j5xqFBleKA0NuysMyzdhygGgKUB8rl3osNBs=";
        $data = utf8_encode($privateKey);
        //dd($data);


        //$data = $RSA256['pkey'];
        openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        $base64Signature = base64_encode($signature);
        //dd($base64Signature);

        $Client = new ClientXML($accountId);

        $index = 0;
        $cert = null;
        $Count = count(preg_split("/((\r?\n)|(\r\n?))/", $RSA256['cert']));
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $RSA256['cert']) as $line){
            $index++;
            if ($index == 1 or ($index > $Count-2)) {

            }   else {
                $cert = $cert.$line;
            }

        }
        $RSA256['cert'] = $cert ;

        $index = 0;
        $cert = null;
        $Count = count(preg_split("/((\r?\n)|(\r\n?))/", $RSA256['pkey']));
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $RSA256['pkey']) as $line){
            $index++;
            if ($index == 1 or ($index > $Count-2)) {

            }   else {
                $cert = $cert.$line;
            }

        }
        $RSA256['pkey'] = $cert ;




        $res = [
            'Client' => $Client->sessionId(),
            'Signature' => $base64Signature,
            'RSA256' => $RSA256,
        ];
        dd($res);
    }




}
