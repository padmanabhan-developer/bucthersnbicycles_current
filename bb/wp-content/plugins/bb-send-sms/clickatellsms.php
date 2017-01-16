<?php
/*
    Send sms through Clickatell SMS gateway
    Author: Igor Malinovskiy
    Author URI: http://woto-info.com/
    License: GPLv2 or later
*/

    class ClickatellSms
    {
        private $baseurl;
        private $user;
        private $password;
        private $api_id;

        private $session_id;

        function __construct($user, $passwd, $api_id) 
        {
            $this->baseurl = "https://api.clickatell.com";
            $this->user = $user;
            $this->password = $passwd;
            $this->api_id = $api_id;
        }

        private function doAuth()
        {
            $authurl = 
                $this->baseurl . '/http/auth?' .
                'user=' . $this->user .
                '&password=' . $this->password .
                '&api_id=' . $this->api_id;

            //do auth
            //successfull response will look like "OK: 9ff1a0fe0dd7a487e8933cafaa73074c"
            $res = file($authurl);
            $session = explode(":", $res[0]);

            if ($session[0] == "OK")
                $this->session_id = trim($session[1]);
        }

        public function setBaseUrl($url)
        {
            $this->baseurl = $url;
        }

        /*
        *   Send message using session key
        */
        public function sendMessage($phone, $text)
        {
            if($this->session_id == "")
            {
                $this->doAuth();
            }
            else
            {
                return "{" . "\"successmsgid\":" . json_encode(var_dump($this->session_id)) . "}";

                $url = $this->baseurl . '/http/sendmsg?' . 
                    'session_id=' . $this->session_id .
                    '&to=' . $phone .
                    '&text=' . urlencode($text);

                //do sendmsg call
                $res = file($url);
                $send = explode(":", $res[0]);

                if($send[0] == "ID")
                    return "{" . "\"successmsgid\":" . json_encode($send[1]) . "}";
                else
                    return "{" . "\"failed\"" . json_encode($ret[0]) . "}";
            }
        }

        /*
        *   Send message with authentication
        */
        public function sendMessageWAuth($phone, $text)
        {
            $url = $this->baseurl . '/http/sendmsg?' .
                'user=' . $this->user .
                '&password=' . $this->password .
                '&api_id=' . $this->api_id .
                '&to=' . $phone . 
                '&text=' . urlencode($text);

            $ret = file($url);
            $send = explode(":", $ret[0]);

            if($send[0] == "ID")
                return "{" . "\"successmsgid\":" . json_encode($send[1]) . "}";
            else
                return "{" . "\"failed\":" . json_encode($ret[0]) . "}";
        }
    }
?>