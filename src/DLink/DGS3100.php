<?php

namespace PTel_Adapters\DLink;

use PTel\PTel,
    PTel\TelnetException,
    PTel\SocketClientException;

class DGS3100 extends Base {

    /**
     * This can take a while, so better to move it to custom function which handles long wait and response.
     */
    public function saveConfiguration() {
        try {
            $this->send("save");
            $this->send("Yes");
            $this->waitFor("successfully", 45);
            return $this;
        } catch (TelnetException $e) {
            throw new TelnetException("There was a problem while saving configuration: ".$e->getMessage());
        }
    }

} // END: class DGS {}
