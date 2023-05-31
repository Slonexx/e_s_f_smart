<?php

namespace App\Services\AdditionalServices;

use App\Clients\MsClient;
use GuzzleHttp\Exception\ClientException;

class AttributeService
{

    private string $TokenMS;
    private string $accountId;

    public function setAllAttributesMs($data): void
    {
        $this->TokenMS = $data['tokenMs'];
        $accountId = $data['accountId'];


        $this->createOrganizationAttributes();
    }

  /*  private function createOrderAttributes($apiKeyMs): void
    {
        $bodyAttributes = $this->getDocAttributes();
        $url = "https://online.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes";
        $client = new MsClient($apiKeyMs);
        $this->getBodyToAdd($client, $url, $bodyAttributes);
    }*/

    private function createOrganizationAttributes()
    {
        $body =  [
            $this->postBody(1),
            $this->postBody(2),
            $this->postBody(3),
        ];

        $Client = new MsClient($this->TokenMS);
        $Client->post('https://online.moysklad.ru/api/remap/1.2/entity/organization/metadata/attributes/', $body);
    }

    private function postBody(int $id)
    {

        switch ($id){
            case 1:{
                return [
                    "name" => "AUTH_RSA256 (ESF)",
                    "type" => "file",
                    "required" => false,
                    "description" => "файл ЭЦП аутентификации.",
                ];
            }
            case 2:{
                return [
                    "name" => "RSA256 (ESF)",
                    "type" => "file",
                    "required" => false,
                    "description" => "файл ЭЦП физического лица.",
                ];
            }
            case 3:{
                return [
                    "name" => "GOSTKNCA (ESF)",
                    "type" => "file",
                    "required" => false,
                    "description" => "файл ЭЦП юридического лица.",
                ];
            }
        }
    }


}
