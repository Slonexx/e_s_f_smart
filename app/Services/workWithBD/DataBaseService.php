<?php

namespace App\Services\workWithBD;


use App\Models\addSettingModel;
use App\Models\documentModel;
use App\Models\mainSetting;
use App\Models\settingModel;
use App\Models\userLoadModel;
use App\Models\wordersModel;
use App\Models\Worker;
use GuzzleHttp\Exception\BadResponseException;

class DataBaseService
{
    public static function createPersonal($accountId, $email, $name, $status){
        userLoadModel::create([
            'accountId' => $accountId,
            'email' => $email,
            'name' => $name,
            'status' => $status,
        ]);
    }
    public static function showPersonal($accountId): array
    {
        $find = userLoadModel::query()->where('accountId', $accountId)->first();
        try {
            $result = $find->getAttributes();
        } catch (\Throwable $e) {
            $result = [
                'accountId' => $accountId,
                'email' => null,
                'name' => null,
                'status' => null,
            ];
        }
        return $result;
    }
    public static function updatePersonal($accountId, $email, $name, $status){
        $find = userLoadModel::query()->where('accountId', $accountId);
        $find->update([
            'email' => $email,
            'name' => $name,
            'status' => $status,
        ]);
    }

    public static function createMainSetting($accountId, $tokenMs, $urlOrganization, $AUTH_RSA256, $RSA256, $GOSTKNCA, $tin, $Username, $Password){
        settingModel::create([
            'accountId' => $accountId,
            'tokenMs' => $tokenMs,

            'urlOrganization' => $urlOrganization,
            'AUTH_RSA256' => $AUTH_RSA256,
            'RSA256' => $RSA256,
            'GOSTKNCA' => $GOSTKNCA,

            'tin' => $tin,
            'Username' => $Username,
            'Password' => $Password,
        ]);
    }
    public static function showMainSetting($accountId): array
    {
        $find = settingModel::query()->where('accountId', $accountId)->first();
        try {
            $result = $find->getAttributes();
        } catch (\Throwable $e) {
            $result = [
                'accountId' => $accountId,
                'tokenMs' => null,

                'urlOrganization' => null,
                'AUTH_RSA256' => null,
                'RSA256' => null,
                'GOSTKNCA' => null,

                'tin' => null,
                'Username' => null,
                'Password' => null,
            ];
        }
        return $result;
    }
    public static function TrueOrFalseMainSetting($accountId): bool
    {
        $find = settingModel::query()->where('accountId', $accountId)->first();
        if ($find) return true; else  return false;

    }
    public static function updateMainSetting($accountId, $tokenMs, $urlOrganization, $AUTH_RSA256, $RSA256, $GOSTKNCA, $tin, $Username, $Password){
        $find = settingModel::query()->where('accountId', $accountId);
        $find->update([
            'tokenMs' => $tokenMs,

            'urlOrganization' => $urlOrganization,
            'AUTH_RSA256' => $AUTH_RSA256,
            'RSA256' => $RSA256,
            'GOSTKNCA' => $GOSTKNCA,

            'tin' => $tin,
            'Username' => $Username,
            'Password' => $Password,
        ]);
    }

    public static function getAccessByAccountId($accountId): array
    {
        $Workers = [];
        $find = wordersModel::query()->where('accountId', $accountId)->get();

        foreach ($find as $item) {
            $json = json_encode($item->getAttributes());
            $Workers[] = json_decode($json);
        }

        return $Workers;
    }

    public static function showWorkerFirst(mixed $id): array
    {
        $find = wordersModel::query()->where('id', $id)->first();
        try {
            $result = $find->getAttributes();
        } catch (\Throwable $e) {
            $result = [
                'id' => $id,
                'accountId' => null,
                'access' => null,
            ];
        }
        return $result;
    }
    public static function createWorker(mixed $id, mixed $accountId, mixed $access)
    {
        wordersModel::create([
            'id' => $id,
            'accountId' => $accountId,
            'access' => $access,
        ]);
    }
    public static function updateWorker(mixed $id, mixed $access)
    {
        $find = wordersModel::query()->where('id', $id);
        $find->update([
            'access' => $access,
        ]);
    }
}
