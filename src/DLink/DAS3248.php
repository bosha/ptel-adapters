<?php

namespace PTel_Adapters\DLink;

use PTel\TelnetException,
    PTel\SocketClientException;

class DAS3248 extends Base {

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

        $this->send(" ");

        $timestart = time();
        $buff = '';
        while (true) {
            $buff = $this->recvLine();
            $timerun = time() - $timestart;

            if (preg_match("/(fail|wrong|incorrect|failed)/i", $buff)) {
                throw new TelnetException("Username or password wrong! Login failed");
            }

            if (preg_match("/(#|>|\$)/", $buff)) {
                break;
            }

            if ($timerun >= $maxtimeout) {
                throw new TelnetException("Could not get reply from device. Login failed.");
            }
        }

        $this->prompt = "$";
        return true;
    }

    /**
     * This can take a while, so better to move it to custom function which handles long wait and response.
     */
    public function saveConfiguration() {
        try {
            $this->send("commit");
            $this->waitFor("Set Done", 20);
            return $this;
        } catch (TelnetException $e) {
            throw new TelnetException("There was a problem while saving configuration: ".$e->getMessage());
        }
    }

    /**
     * Send "logout" and close socket connection
     *
     * @return $this    Instance of current class
     */
    public function disconnect() {
        try {
            $this->send("exit");
            parent::disconnect();
        } catch (SocketClientException $e) { }
        return $this;
    }

} // END: class DAS3248 {}
