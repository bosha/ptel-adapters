<?php

namespace PTel_Adapters\DLink;

use PTel\TelnetException,
    PTel\SocketClientException;

class DAS3216 extends Base {

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
    public function login($username, $password, $maxtimeout = 10) {
        parent::login($username, $password, $maxtimeout);
        $this->send(" "); // Sometimes this required..
    }

    /**
     * This can take a while, so better to move it to custom function which handles long wait and response.
     */
    public function saveConfiguration() {
        try {
            $this->send("save");
            $this->send("y");
            $this->waitFor("saved.", 15);
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
            $this->send("logout");
            $this->send("y");
            parent::disconnect();
        } catch (SocketClientException $e) { }
        return $this;
    }

} // END: class DAS3216 {}
