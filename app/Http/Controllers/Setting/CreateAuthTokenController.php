<?php

namespace App\Http\Controllers\Setting;

use App\Clients\ClientXML;
use App\Clients\MsClient;
use App\Http\Controllers\BD\getMainSettingBD;
use App\Http\Controllers\Config\getSettingVendorController;
use App\Http\Controllers\Config\Lib\AppInstanceContoller;
use App\Http\Controllers\Config\Lib\cfg;
use App\Http\Controllers\Config\Lib\VendorApiController;
use App\Http\Controllers\Controller;
use App\Services\workWithBD\DataBaseService;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CreateAuthTokenController extends Controller
{
    private getSettingVendorController $Setting;
    private getMainSettingBD $SettingBD;
    private MsClient $Client;


    public function getCreateAuthToken(Request $request, $accountId): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $isAdmin = $request->isAdmin;
        $SettingBD = new getSettingVendorController($accountId);
        $Setting = new getMainSettingBD($accountId);
        $Client = new MsClient($SettingBD->TokenMoySklad);
        $organization = $Client->get('https://online.moysklad.ru/api/remap/1.2/entity/organization')->rows;

        if ($Setting->urlOrganization != null){
           $SettingBool = true;
           $organizationID = basename($Setting->urlOrganization);
           $AUTH_RSA256 = $Setting->AUTH_RSA256;
           $RSA256 = $Setting->RSA256;
           $GOSTKNCA = $Setting->GOSTKNCA;
           $PASS_ESF = $Setting->Password;
        }else {
            $SettingBool = false;
            $organizationID = '';
            $AUTH_RSA256 = '';
            $RSA256 = '';
            $GOSTKNCA = '';
            $PASS_ESF = '';
        }

        if (isset($request->message)) {
            return view('setting.authToken', [
                'accountId' => $accountId,
                'isAdmin' => $isAdmin,

                'organizationID' => $organizationID,
                'AUTH_RSA256' => $AUTH_RSA256,
                'RSA256' => $RSA256,
                'GOSTKNCA' => $GOSTKNCA,
                'PASS_ESF' => $PASS_ESF,

                'organization' => $organization,

                'message' => $request->message,
            ]);
        }

        return view('setting.authToken', [
            'accountId' => $accountId,
            'isAdmin' => $isAdmin,

            'organizationID' => $organizationID,
            'AUTH_RSA256' => $AUTH_RSA256,
            'RSA256' => $RSA256,
            'GOSTKNCA' => $GOSTKNCA,
            'PASS_ESF' => $PASS_ESF,

            'organization' => $organization,
        ]);
    }

    public function postCreateAuthToken(Request $request, $accountId)
    {
        $this->initialization($accountId);

        //dd($request->all());

        $AUTH_RSA256 = $request->file('AUTH_RSA256');
        $RSA256 = $request->file('RSA256');
        $GOSTKNCA = $request->file('GOSTKNCA');
        if ($GOSTKNCA == null) $GOSTKNCA = $request->file('GOSTKNCA_CER');
        if ($AUTH_RSA256 && $RSA256 && $GOSTKNCA) {
            Storage::putFileAs('public/' . $accountId . '/folder', $AUTH_RSA256, 'AUTH_RSA256.p12');
            Storage::putFileAs('public/' . $accountId . '/folder', $RSA256, 'RSA256.p12');
            if ($request->file('GOSTKNCA') == null) {
                Storage::putFileAs('public/' . $accountId . '/folder', $GOSTKNCA, 'GOSTKNCA.cer');
            } else Storage::putFileAs('public/' . $accountId . '/folder', $GOSTKNCA, 'GOSTKNCA.p12');

            for ($i = 0; $i < 3; $i++) {

                $p12Password = $request->PASS_ESF;
                $message = '';
                if ($i == 0) {
                    $message = 'AUTH_RSA256';
                    if ($request->PASS_AUTH_RSA256 != null) $p12Password = $request->PASS_AUTH_RSA256;
                }
                if ($i == 1) {
                    $message = 'RSA256';
                    if ($request->PASS_RSA256 != null) $p12Password = $request->PASS_RSA256;
                }
                if ($i == 2) {
                    $message = 'GOSTKNCA';
                    if ($request->file('GOSTKNCA') == null) {
                        if (!file_get_contents(asset('/storage/' . $accountId . '/folder/' . $message . '.cer')) != '') {
                            return to_route('getCreateAuthToken', [
                                    'accountId' => $accountId,
                                    'isAdmin' => $request->isAdmin,
                                    'message' => 'Файл сертификата: ' . $message . ' пустой!'
                                ]
                            );
                        }
                        Storage::delete('public/' . $accountId . '/folder/GOSTKNCA.p12');
                        $message = '';
                    } else {
                        if ($request->PASS_GOSTKNCA != null) $p12Password = $request->PASS_GOSTKNCA;
                    }
                }
                if ($message != '') {
                    $p12Content = file_get_contents(asset('/storage/' . $accountId . '/folder/' . $message . '.p12'));
                    if (!openssl_pkcs12_read($p12Content, $p12Data, $p12Password)) {
                        return to_route('getCreateAuthToken', [
                                'accountId' => $accountId,
                                'isAdmin' => $request->isAdmin,
                                'message' => 'Не верный пароль от ' . $message
                            ]
                        );
                    }
                }

            }


        } else {
            return to_route('getCreateAuthToken', [
                    'accountId' => $accountId,
                    'isAdmin' => $request->isAdmin,
                    'message' => 'Отсутствует ключи ЭЦП'
                ]
            );
        }

        try {
            $ms = $this->Client->get('https://online.moysklad.ru/api/remap/1.2/entity/organization/' . $request->organizationID);
            $msEmployee = $this->Client->get('https://online.moysklad.ru/api/remap/1.2/entity/employee?filter=uid~admin@')->rows;
            $INT_OR_STR = intval($msEmployee[0]->position);
            if (property_exists($msEmployee[0], 'inn')) {
                $iin = $msEmployee[0]->inn;
            } elseif (property_exists($msEmployee[0], 'position')) {
                if ($this->validateString($msEmployee[0]->position)) {
                    $iin = $msEmployee[0]->position;
                } else {
                    return to_route('getCreateAuthToken', [
                            'accountId' => $accountId,
                            'isAdmin' => $request->isAdmin,
                            'message' => 'Да вы можете добавить в поле должность иин, потом прийти настройки и обратно поставить должность, у данного сотрудника: ' . $msEmployee[0]->fullName,
                        ]
                    );
                }
            } else {
                return to_route('getCreateAuthToken', [
                        'accountId' => $accountId,
                        'isAdmin' => $request->isAdmin,
                        'message' => 'Отсутствует иин в сотруднике' . $msEmployee[0]->fullName,
                    ]
                );
            }
        } catch (BadResponseException $e) {
            return to_route('getCreateAuthToken', [
                    'accountId' => $accountId,
                    'isAdmin' => $request->isAdmin,
                    'message' => 'Ошибка: ' . $e->getMessage() . ' Обратитесь в поддержку, через правое меню ПОМОЩЬ->КОНТАКТЫ'
                ]
            );
        }


        if (!DataBaseService::TrueOrFalseMainSetting($accountId)) {
            DataBaseService::createMainSetting(
                $accountId,
                $this->Setting->TokenMoySklad,
                'https://online.moysklad.ru/api/remap/1.2/entity/organization/' . $request->organizationID,
                $request->PASS_AUTH_RSA256,
                $request->PASS_RSA256,
                $request->PASS_GOSTKNCA,
                $ms->inn,
                $iin,
                $request->PASS_ESF);

        } else {
            DataBaseService::updateMainSetting(
                $accountId,
                $this->Setting->TokenMoySklad,
                'https://online.moysklad.ru/api/remap/1.2/entity/organization/' . $request->organizationID,
                $request->PASS_AUTH_RSA256,
                $request->PASS_RSA256,
                $request->PASS_GOSTKNCA,
                $ms->inn,
                $iin,
                $request->PASS_ESF);
        }

        $ClientXML = new ClientXML($accountId);
        $Session = $ClientXML->AuthSessionID();
        if (isset($Session['faultcode'])){
            return to_route('getCreateAuthToken', [
                    'accountId' => $accountId,
                    'isAdmin' => $request->isAdmin,
                    'message' => 'Ошибка: ' . $Session['faultcode'] . ' Обратитесь в поддержку, через правое меню ПОМОЩЬ->КОНТАКТЫ '
                ]
            );
        }

        $metadata = $this->Client->get('https://online.moysklad.ru/api/remap/1.2/entity/organization/metadata/attributes/')->rows;
        foreach ($metadata as $item) {
            $postBool = false;
            if ($item->name == 'AUTH_RSA256 (ESF)') {
                $id = 0;
                $postBool = true;
                $message = 'AUTH_RSA256.p12';
            }
            if ($item->name == 'RSA256 (ESF)') {
                $id = 1;
                $postBool = true;
                $message = 'RSA256.p12';
            }
            if ($item->name == 'GOSTKNCA (ESF)') {
                if ($request->file('GOSTKNCA') == null) $message = 'GOSTKNCA.cer'; else $message = 'GOSTKNCA.p12';
                $id = 2;
                $postBool = true;
            }

            if ($postBool) $attributes[] = [
                'meta' => [
                    'href' => $item->meta->href,
                    'type' => $item->meta->type,
                    'mediaType' => $item->meta->mediaType,
                ],
                'file' => [
                    'filename' => $message,
                    'content' => base64_encode(file_get_contents(asset('/storage/' . $accountId . '/folder/' . $message))),
                ]
            ];
        }
        $this->Client->put('https://online.moysklad.ru/api/remap/1.2/entity/organization/' . $request->organizationID, ['attributes' => $attributes]);

        $cfg = new cfg();
        $app = AppInstanceContoller::loadApp($cfg->appId, $accountId);
        $app->status = AppInstanceContoller::ACTIVATED;
        $vendorAPI = new VendorApiController();
        $vendorAPI->updateAppStatus($cfg->appId, $accountId, $app->getStatusName());
        $app->persist();

        return to_route('getWorker', [
                'accountId' => $accountId,
                'isAdmin' => $request->isAdmin,
            ]
        );

    }


    private function initialization($accountId)
    {
        $this->Setting = new getSettingVendorController($accountId);
        $this->SettingBD = new getMainSettingBD($accountId);
        $this->Client = new MsClient($this->Setting->TokenMoySklad);
    }


    private function validateString($inputString)
    {
        // Проверяем длину строки
        if (strlen($inputString) !== 12) {
            return false;
        }

        // Проверяем, содержит ли строка только цифры
        if (!ctype_digit($inputString)) {
            return false;
        }
        // Если все проверки пройдены, строка является валидной
        return true;
    }
}
