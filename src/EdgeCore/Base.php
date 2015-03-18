<?php

namespace PTel_Adapters\EdgeCore;

use PTel\PTel,
    PTel\TelnetException,
    PTel\SocketClientException;

abstract class Base extends PTel {

    /**
     * 'Enable' privileges on device
     *
     * @param $password
     *
     * @throws TelnetException          On wrong password, and another telnet logic errors
     * @throws SocketClientException    On socket client errors (Connection lost, etc..)
     *
     * @return $this    Current instance if enable success
     */
    public function enable($password) {
        try {
            $this->send("enable");
            if (strpos($this->prompt, '#') !== false) return $this;
            $this->expect('ord:', $password);
            while ($out = $this->recvLine()) {
                if (strpos($out, "ord:") !== false) {
                    $this->send('');
                    $this->send('');
                    throw new TelnetException('incorrect enable password!');
                } elseif (strpos($out, "#") !== false) {
                    $this->prompt = $out;
                    $this->recvAll();
                    return true;
                }
            }
        } catch (TelnetException $e) { throw $e; }
        return $this;
    }

    /**
     * This can take a while, so better to move it to custom function which handles long wait and response.
     */
    public function saveConfiguration() {
        try {
            $this->send("copy run start");
            $this->send("");
            if ($this->find("Invalid input detected at")) {
                throw new TelnetException("You don't have privileges to save configuration!");
            }
            $this->waitFor("Success", 30);
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

    /**
     * In case we didn't disconnected manually, when everyting comes to end - it's always good idea to cleanup.
     */
    public function __destruct() { $this->disconnect(); }

} // END: class Base {}
