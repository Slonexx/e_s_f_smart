<?php

namespace App\Http\Controllers\BD;

use App\Http\Controllers\Controller;
use App\Services\workWithBD\DataBaseService;

class getMainSettingBD extends Controller
{
    public mixed $accountId;
    public mixed $tokenMs;

    public mixed $urlOrganization;
    public mixed $AUTH_RSA256;
    public mixed $RSA256;
    public mixed $GOSTKNCA;

    public mixed $tin;
    public mixed $Username;
    public mixed $Password;

    /**
     * @param $accountId
     */
    public function __construct($accountId)
    {
        $this->accountId = $accountId;

        $BD = DataBaseService::showMainSetting($accountId);
        $this->accountId = $BD['accountId'];
        $this->tokenMs = $BD['tokenMs'];

        $this->urlOrganization = $BD['urlOrganization'];
        $this->AUTH_RSA256 = $BD['AUTH_RSA256'];
        $this->RSA256 = $BD['RSA256'];
        $this->GOSTKNCA = $BD['GOSTKNCA'];

        $this->tin = $BD['tin'];
        $this->Username = $BD['Username'];
        $this->Password = $BD['Password'];
    }


}
