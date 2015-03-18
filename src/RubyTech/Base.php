<?php

namespace PTel_Adapters\RubyTech;

use PTel\PTel,
    PTel\TelnetException,
    PTel\SocketClientException;

abstract class Base extends PTel {

    /**
     * This can take a while, so better to move it to custom function which handles long wait and response.
     */
    public function saveConfiguration() {
        try {
            $this->send("save start");
            $this->waitFor("Successfully", 30);
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
            parent::disconnect();
        } catch (SocketClientException $e) { }
        return $this;
    }

    /**
     * In case we didn't disconnected manually, when everyting comes to end - it's always good idea to cleanup.
     */
    public function __destruct() { $this->disconnect(); }

}