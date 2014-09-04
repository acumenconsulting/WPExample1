<?php
global $post;

$clients = new Clients(true);
$users = new Users(true);

// Get the list of clients and users, and regex replace the prefixes 'client-' and 'user'
$data = get_post_meta( $post->ID, 'client_post_meta', true );
$client_ids_string = preg_replace('/client-/', '', preg_replace('/user-([0-9]*),/', '', $data));
$user_ids_string = preg_replace('/user-/', '', preg_replace('/client-([0-9]*),/', '', $data));

// Break the ID list into an array
$client_ids = explode(',', $client_ids_string);
$users_ids = explode(',', $user_ids_string);

// Sort the selected clients and users into arrays
$unselected_clients = array();
$selected_clients = array();
$unselected_users = array();
$selected_users = array();

if (!empty($client_ids_string)) { // We have selected clients
    foreach ($clients->clients as $client) {
        if (in_array($client->ID, $client_ids)) {
            array_push($selected_clients, $client);
        }
        else {
            array_push($unselected_clients, $client);
        }
    }
}
else { // All of the clients are unselected
    $unselected_clients = $clients->clients;
}

if (!empty($user_ids_string)) { // We have selected users
    foreach ($users->users as $user) {
        if (in_array($user->ID, $users_ids)) {
            array_push($selected_users, $user);
        }
        else {
            array_push($unselected_users, $user);
        }
    }
}
else { // All of the users are unselected
    $unselected_users = $users->users;
}
?>

<div class="row client-user-lists">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom:10px;">
        <span>To select the recipients for this notification, filter the list of Clients/Users, then drag &amp; drop the intended recipients 
        to the Selected Clients and Users list to add them. If you need to remove any from the selected list, you can drag them and 
        drop them back to the Unselected list.</span>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom:20px;">
        <label>Show: </label>
        <select id="client-users-filter">
            <option value="all-clients-and-users">All Clients and Users</option>
            <option value="all-clients">All Clients</option>
            <option value="all-users">All Users</option>
            <?php
            foreach ($clients->clients as $client) {
                echo '<option value="' . $client->ID . '">Show all ' . $client->name . ' Users</option>';
            }
            ?>
        </select>
        <?php 
        echo '<input name="client-post-meta-input" id="client-post-meta-input" value="' . $data . '" type="hidden">';
        ?>
        
        <label><input type="checkbox" name="select-all-clients" id="select-all-clients"> Select All Clients</label>
    </div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <strong>Unselected Clients and Users</strong>
        <ul id="unselected" class="connectedSortable">
        <?php
        foreach ($unselected_clients as $client) {
            echo '<li class="ui-state-default sortable-client" data-id="' . $client->ID .'" data-clientid="' . $client->ID .'">' . $client->name . ' <span class="list-float">Client</span></li>';
        }

        foreach ($unselected_users as $user) {
            echo '<li class="ui-state-default sortable-user" data-id="' . $user->ID .'" data-clientid="' . $user->client_id .'">' . $user->user_login . ' <span class="list-float">User</span></li>';
        }
        ?>
        </ul>
    </div>

	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <strong>Selected Clients and Users</strong>
        <ul id="selected" class="connectedSortable">
        <?php
        foreach ($selected_clients as $client) {
            echo '<li class="ui-state-default sortable-client" data-id="' . $client->ID .'" data-clientid="' . $client->ID .'">' . $client->name . ' <span class="list-float">Client</span></li>';
        }

        foreach ($selected_users as $user) {
            echo '<li class="ui-state-default sortable-user" data-id="' . $user->ID .'" data-clientid="' . $user->client_id .'">' . $user->user_login . ' <span class="list-float">User</span></li>';
        }
        ?>
        </ul>
    </div>
</div>
 
<script type="text/javascript">
    jQuery(function($) {        
        $("#unselected, #selected").sortable({
            connectWith: ".connectedSortable",
            stop: function(event, ui) {
                refreshHiddenInputValue($);
            }
        }).disableSelection();
        
        listManager(jQuery);
        
        selectAll(jQuery);
    });

    var refreshHiddenInputValue = function($) {
        var metaInput = $("#client-post-meta-input"),
            selectedItems = $("#selected li"),
            value = "";

        $.each(selectedItems, function(i,o) {
            value += (($(this).hasClass("sortable-client")) ? "client-" : "user-");
            value += $(this).data("id") + ",";
        });
        
        metaInput.val(value);
    }
    
    var listManager = function($) {
        $("#client-users-filter").on("change", function() {
            var container = $("#unselected");
            var value = $(this).val();
            
            container.find(".sortable-user, .sortable-client").hide();
            
            if (value == "all-clients-and-users") {
                container.find(".sortable-user, .sortable-client").show();
            }
            else if (value == "all-clients") {
                container.find(".sortable-client").show();
            }
            else if(value == "all-users") {
                container.find(".sortable-user").show();
            }
            else {
                container.find(".sortable-user[data-clientid='" + value + "']").show();
            }
        });
    }
        
    var selectAll = function($) {
        $("#select-all-clients").on("change", function() {
            var moveToSelected = $(this).is(":checked");
                
            if (moveToSelected) {
                $("#selected").append($("#unselected .sortable-client"));
                $("#unselected").prepend($("#selected .sortable-user"));
            }
            else {
                $("#unselected").prepend($("#selected .sortable-client"));
            }

            refreshHiddenInputValue($);
        });
    }
</script>