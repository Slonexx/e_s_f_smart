<?php

namespace App\Clients;


use App\Http\Controllers\BD\getMainSettingBD;
use Illuminate\Support\Facades\Config;
use SoapClient;

class MySoapClient
{
    private SoapClient $client;
    private mixed $ConfigENV;
    private getMainSettingBD $Setting;
    private mixed $RSA_KEY;

    public function __construct($accountId)
    {
        $this->ConfigENV = Config::get("Global");
        $this->Setting = new getMainSettingBD($accountId);

        $this->RSA_KEY = ($this->RSA_KEY('AUTH_RSA256_6750719d5982547be3d0633e3d34c58d1a7d62ef.p12'))['cert'];

        $client = new SoapClient('https://test3.esf.kgd.gov.kz:8443/esf-web/ws/api1/VersionService?wsdl');
        dd($client->someMethod());

       /* $this->client = new SoapClient([
            'base_uri' => $this->ConfigENV['urlOver'],
            'headers' => [
                'Content-Type' => 'text/xml; charset=utf-8',
            ],
            'verify' => false
        ]);*/
    }

    public function test($body): mixed
    {

        try {
            $send = $this->client->post($this->ConfigENV['urlOver'] . 'SessionService', [
                'body' => $body,
            ])->getBody()->getContents();
        } catch (BadResponseException $e) {
            $plainXML = $this->ParseSOAP($e->getResponse()->getBody()->getContents());
            $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            dd($arrayResult);
        }
        dd($send);
        $plainXML = $this->ParseSOAP($send);
        $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arrayResult;
    }



    public function SessionService($esf): mixed
    {
        $token = $this->token();
        $Username = $this->Setting->Username;
        $Password = $this->Setting->Password;

        $Envelope_end = PHP_EOL . '</soapenv:Envelope>';
        $HeadXML = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:esf="esf">
   <soapenv:Header>
      <wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
         <wsse:UsernameToken wsu:Id="' . $token . '">
            <wsse:Username>' . $Username . '</wsse:Username>
            <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">' . $Password . '</wsse:Password>
         </wsse:UsernameToken>
      </wsse:Security></soapenv:Header>';


        $body = $HeadXML . PHP_EOL . $esf . $Envelope_end;

        try {
            $send = $this->client->post($this->ConfigENV['urlOver'] . 'SessionService', [
                'body' => $body,
            ]);
            return $send->getBody()->getContents();
        } catch (BadResponseException $e) {
            $plainXML = $this->ParseSOAP($e->getResponse()->getBody()->getContents());
            $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            dd([0 => $arrayResult, 1 => $body]);
        }

        $plainXML = $this->ParseSOAP($send);
        return json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }


    public function AwpWebService($esf): mixed
    {
        $token = $this->token();
        $Username = $this->Setting->Username;
        $Password = $this->Setting->Password;

        $Envelope_end = PHP_EOL . '</soapenv:Envelope>';
        $HeadXML = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:esf="esf">
   <soapenv:Header>
      <wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
         <wsse:UsernameToken wsu:Id="' . $token . '">
            <wsse:Username>' . $Username . '</wsse:Username>
            <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">' . $Password . '</wsse:Password>
         </wsse:UsernameToken>
      </wsse:Security></soapenv:Header>';


        $body = $HeadXML . PHP_EOL . $esf . $Envelope_end;

        try {
            $send = $this->client->post($this->ConfigENV['urlOver'] . 'SessionService', [
                'body' => $body,
            ]);
            return $send->getBody()->getContents();
        } catch (BadResponseException $e) {
            $plainXML = $this->ParseSOAP($e->getResponse()->getBody()->getContents());
            $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            dd([0 => $arrayResult, 1 => $body]);
        }

        $plainXML = $this->ParseSOAP($send);
        return json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }


    public function sessionId(){
        $token = $this->token();
        $Username = $this->Setting->Username;
        $Password = $this->Setting->Password;
        $businessProfileType = 'ADMIN_ENTERPRISE';
        $x509Certificate = '<x509Certificate>'.$this->RSA_KEY.'</x509Certificate>';

    $body = '
    <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:esf="esf">
        <soapenv:Header>
            <wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                <wsse:UsernameToken wsu:Id="' . $token . '">
                    <wsse:Username>' . $Username . '</wsse:Username>
                    <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">' . $Password . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
        </soapenv:Header>'.PHP_EOL.'
        <soapenv:Body>
            <esf:createSessionRequest>
                <tin>'.$Username.'</tin>
                <!--Optional:-->
                <projectCode>'.rand(1, 100000).'</projectCode>
                <businessProfileType>'. $businessProfileType .'</businessProfileType>'
                .$x509Certificate.'
                <sourceType>OTHER</sourceType>
            </esf:createSessionRequest>
        </soapenv:Body>'.PHP_EOL. '
    </soapenv:Envelope>';

        try {
            $send = $this->client->post($this->ConfigENV['urlOver'] . 'SessionService', [
                'body' => $body,
            ]);

            $sendSessionId = json_decode(json_encode(simplexml_load_string( $this->ParseSOAP($send->getBody()->getContents()) )));
            dd($sendSessionId->soap_Body->ns2_createSessionResponse->sessionId, $x509Certificate, $this->RSA_KEY('RSA256_348bb7da4b1090d0a19bf00bbc55c410dcc03b27.p12'));
            return $sendSessionId->soap_Body->ns2_createSessionResponse->sessionId;
        } catch (BadResponseException $e) {
            $plainXML = $this->ParseSOAP($e->getResponse()->getBody()->getContents());
            $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            dd([0 => $arrayResult, 1 => $body]);
        }
    }

    private function token()
    {

        $Username = $this->Setting->Username;
        $body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:esf="esf">
            <soapenv:Header/>
                <soapenv:Body>
                    <esf:createAuthTicketRequest>
                        <iin>' . $Username . '</iin>
                    </esf:createAuthTicketRequest>
                </soapenv:Body>
            </soapenv:Envelope>';

        $send = $this->client->post($this->ConfigENV['urlOver'] . 'AuthService', [
            'body' => $body,
        ])->getBody()->getContents();

        $plainXML = $this->ParseSOAP($send);
        $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        return json_decode(json_encode(simplexml_load_string($arrayResult['soap_Body']['ns2_createAuthTicketResponse']['authTicketXml'])))->state;
    }


    public function ParseSOAP($xml)
    {
        $obj = SimpleXML_Load_String($xml);
        if ($obj === FALSE) return $xml;

        // GET NAMESPACES, IF ANY
        $nss = $obj->getNamespaces(TRUE);
        if (empty($nss)) return $xml;

        // CHANGE ns: INTO ns_
        $nsm = array_keys($nss);
        foreach ($nsm as $key) {
            // A REGULAR EXPRESSION TO MUNG THE XML
            $rgx
                = '#'               // REGEX DELIMITER
                . '('               // GROUP PATTERN 1
                . '\<'              // LOCATE A LEFT WICKET
                . '/?'              // MAYBE FOLLOWED BY A SLASH
                . preg_quote($key)  // THE NAMESPACE
                . ')'               // END GROUP PATTERN
                . '('               // GROUP PATTERN 2
                . ':{1}'            // A COLON (EXACTLY ONE)
                . ')'               // END GROUP PATTERN
                . '#'               // REGEX DELIMITER
            ;
            // INSERT THE UNDERSCORE INTO THE TAG NAME
            $rep
                = '$1'          // BACKREFERENCE TO GROUP 1
                . '_'           // LITERAL UNDERSCORE IN PLACE OF GROUP 2
            ;
            // PERFORM THE REPLACEMENT
            $xml = preg_replace($rgx, $rep, $xml);
        }

        return $xml;

    }

    public function RSA_KEY($RSA){
        $file = file_get_contents($RSA);
        $certificates = [];
        $cert = "";
        $pkey = "";



       if (openssl_pkcs12_read($file,$certificates, 'eiprGQ86')){
           $index = 0;
           $Count = count(preg_split("/((\r?\n)|(\r\n?))/", $certificates['cert']));
           foreach(preg_split("/((\r?\n)|(\r\n?))/", $certificates['cert']) as $line){
               $index++;
               if ($index == 1 or ($index > $Count-2)) {

               }   else {
                   $cert = $cert.$line;
               }

           }

           $index = 0;
           $Count = count(preg_split("/((\r?\n)|(\r\n?))/", $certificates['pkey']));
           foreach(preg_split("/((\r?\n)|(\r\n?))/", $certificates['pkey']) as $line){
               $index++;
               if ($index == 1 or ($index > 27)) {

               }   else {
                   $pkey = $pkey.$line;
               }

           }

           //dd($certificates, $cert, $pkey);

       }
        return  [ 'cert'=>$cert, 'pkey'=>$pkey ];
    }

}
