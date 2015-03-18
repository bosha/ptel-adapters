<?php

namespace PTel_Adapters\DLink;

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
            $this->send("enable admin");
            if ($this->find("privileges")) { return $this; }
            if ($this->findAll('(P|p)ass((W|w)ord|)(:|)')) {
                $this->send($password);
            } else {
                throw new TelnetException("Could not find password request. Seems like something broken.");
            }
            while ($out = $this->recvLine()) {
                if (strpos($out, "Fail") !== false) {
                    $this->send('');
                    $this->send('');
                    throw new TelnetException('incorrect enable password!');
                } elseif (strpos($out, "#") !== false) {
                    $this->prompt = $out;
                    $this->recvAll();
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
            $this->send("save");
            if ($this->find("Available commands")) {
                throw new TelnetException("You don't have privileges to save configuration!");
            }
            $this->waitFor("Done.", 45);
            return $this;
        } catch (TelnetException $e) {
            throw new TelnetException("There was a problem while saving configuration: ".$e->getMessage());
        }
    }

    /**
     * Alias for disconect();
     *
     * @return $this  Instance of current class
     */
    public function logout() { return $this->disconnect(); }

    /**
     * Send "logout" and close socket connection
     *
     * @return $this    Instance of current class
     */
    public function disconnect() {
        try {
            $this->send("logout");
            parent::disconnect();
        } catch (SocketClientException $e) { }
        return $this;
    }

    /**
     * In case we didn't disconnected manually, when everyting comes to end - it's always good idea to cleanup.
     */
    public function __destruct() { $this->disconnect(); }

} // END: class DES {}
