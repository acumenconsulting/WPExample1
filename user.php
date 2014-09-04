<?php

class Users {
    public $users = null;
    public $huddle = null;
    
    public function __construct($get_users = false) {
        if ($get_users) {
            $this->get_all_users();
        }
    }

    private function get_all_users() {
        global $wpdb;
        $results = $wpdb->get_results( "SELECT * FROM " . OUA_USERS_TABLE . " ORDER BY 'user_login'");
        $this->users = $results;
    }

    public function render_all_users() {
        $first = true;

        echo '<table class="pure-table">';
        foreach ($this->users as &$user) {
            if ($first) {
                $first = false;
                echo '<tr>';
                foreach($user as $key => $value) {
                    echo '<th>' . $this->key_to_nice_name($key) . '</th>';
                }
                echo '</tr>';
            }

            echo '<tr>';
            foreach($user as $value) {
                echo '<td>' . $value . '</td>';
            }
            echo '<tr>';
        }
        echo '</table>';
    }

    public function key_to_nice_name($key) {
        switch ($key) {
            case "ID": return "ID";
            case "user_login": return "User Login";
            case "huddle_username": return "Huddle Username";
            case "huddle_oauth": return "Huddle OAuth Key";
        }
    }
}
