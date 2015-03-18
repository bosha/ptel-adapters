<?php

namespace PTel_Adapters\EdgeCore;

use PTel\TelnetException,
    PTel\SocketClientException;

class ES3526S extends Base {

    /**
     * 'Enable' privileges on device. Just returns instance of class because this device doesn't support
     * privilege levels.
     *
     * @param $password
     *
     * @return $this    Current instance if enable success
     */
    public function enable($password) { return $this; }

    /**
     * Login to device.
     *
     * @param string    $user   Username
     * @param string    $pass   Password
     *
     * @throws  TelnetException         On wrong username/password
     * @throws  SocketClientException   On socket communication error
     * @return  $this                   Current instance on success
     */
    public function login($user, $pass, $maxtimeout = 10) {
        try {
            $this->expect('((U|u)ser|(L|l)ogin)((N|n)ame|)(:|)', $user);
            $this->expect('(P|p)ass((W|w)ord|)(:|)', $pass);
        } catch (TelnetException $e) {
            throw new TelnetException('Could not find password request. Login failed.');
        }

        $timestart = time();
        $buff = '';
        while (true) {
            $buff = $this->recvLine();
            $timerun = time() - $timestart;

            if (preg_match("/(fail|wrong|incorrect|failed)/i", $buff)) {
                throw new TelnetException("Username or password wrong! Login failed");
            }

            if (preg_match("/Vty/", $buff)) {
                break;
            }

            if ($timerun >= $maxtimeout) {
                throw new TelnetException("Could not get reply from device. Login failed.");
            }
        }
        $this->recvAll();
        $lines = explode("\n", $this->getBuffer());
        $prompt = array_slice($lines, -1);
        $this->prompt = $prompt[0];
        return $this;
    }

    /**
     * This can take a while, so better to move it to custom function which handles long wait and response.
     */
    public function saveConfiguration() {
        try {
            $this->send("copy run start");
            $this->send("");
            $this->waitFor("Success", 30);
            return $this;
        } catch (TelnetException $e) {
            throw new TelnetException("There was a problem while saving configuration: ".$e->getMessage());
        }
    }

}