<?php

// Client handles all of the saving, deleting, and updating for a client.
class Client {
    public $id = null;
    public $name = null;
    public $image = null;

    public function __construct($id, $name, $image) {
        $this->id = (isset($id) ? $id : null);
        $this->name = (isset($name) ? $name : null);
        $this->image = (isset($image) ? $image : null);
    }

    public function save() {
        if ($this->id == null) {
            $this->save_new();
        }
        else {
            $this->save_existing();
        }
    }

    public function delete() {
        global $wpdb;
        $wpdb->delete(OUA_CLIENTS_TABLE, array('ID' => $this->id));
    }

    public function save_new() {
        global $wpdb;
        
        $data = array( 
                'name' => $this->name,
                'image' => $this->image, 
            );
        $format = array('%s', '%s');
        
        $result = $wpdb->insert(OUA_CLIENTS_TABLE, $data, $format);

        $this->id = $wpdb->insert_id; // gets the auto incrementing ID back
    }

    public function save_existing() {
        global $wpdb;
        $wpdb->update( 
            OUA_CLIENTS_TABLE, 
            array( 
                'name' => $this->name,
                'image' => $this->image 
            ), 
            array('ID' => $this->id),
            array('%s', '%s'),
            array('%d') 
        );
    }
}

// Clients gets all of the clients from the DB and offers some helper functions
class Clients {
    public $clients = null;
    
    public function __construct($get_clients = false) {
        if ($get_clients) {
            $this->get_all_clients();
        }
    }

    private function get_all_clients() {
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM " . OUA_CLIENTS_TABLE . " ORDER BY 'name'");
        $this->clients = $results;
    }

    public function key_to_nice_name($key) {
        switch ($key) {
            case "ID": return "ID";
            case "name": return "Name";
            case "image": return "Image";
        }
    }
}
