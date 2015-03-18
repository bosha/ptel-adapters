<?php

namespace PTel_Adapters\DLink;

use PTel\TelnetException,
    PTel\SocketClientException;

class DGS1210 extends Base {

    /**
     * This can take a while, so better to move it to custom function which handles long wait and response.
     */
    public function saveConfiguration() {
        try {
            $this->send("save");
            if ($this->find("Available commands")) {
                throw new TelnetException("You don't have privileges to save configuration!");
            }
            $this->waitFor("[OK]", 45);
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

}  // END: class DGS1210 {}