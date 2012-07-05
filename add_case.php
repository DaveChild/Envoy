<?php

    // Settings
        $account_alias = 'demo'; // From Envoy, e.g., for http://example.envoyapp.com/ this would be "example".
        $api_key = 'CB4BH8FF2ZH9BD333HTVDD85NYMCPMC1'; // Generate this in Envoy (Settings > API).

    // Demo case data for this example
        $fields = array(
            'case_title'                => urlencode('This is an example case addition'),
            'due'                       => urlencode(strtotime("+21 days")),
            'owner_user_id'             => urlencode(1),        // Numeric user ID
            'client_id'                 => urlencode(1),        // Numeric client ID
            'parent_case_id'            => urlencode(1),        // Numeric parent case ID
            'depends_on'                => urlencode(''),       // Space-separated list of case IDs on which this case depends
            'estimate'                  => urlencode(6),        // Estimate in hours.
            'priority'                  => urlencode(3),        // Numeric index for priority
            'status'                    => urlencode('active'), 
            'private_case'              => urlencode(0),        // 0 or 1
            'private_update'            => urlencode(0),        // 0 or 1
            'update_message'            => urlencode('This is the case change message.'),
            'tags'                      => urlencode('api')
        );


    // You shouldn't need to change below here until the end of the file, where the response is handled

    // Build URL.
        $url = 'http://' . $account_alias . '.envoyapp.com/api/cases/add/';

    // Sign the request
        $request_time = date("U"); // Used to sign request
        $api_signature = md5($api_key . $request_time);

    // Concatenate fields into correct format for post.
        $fields_string = '';
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        $fields_string = trim($fields_string, '& ');

    // Get and decode data
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X_API_SIGNATURE: ' . $api_signature, 'X_API_REQUEST_TIME: ' . $request_time));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $file = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode != 200) {
            // Code other than 200 means there was an error. Handle as appropriate.
            echo '<p>There was an error.</p>';
            die();
        }

    // Convert JSON to array.
        $remote = json_decode(trim($file), true);

    // If remote isn't set, there was something wrong with the response. 
        if (!$remote) {
            echo '<p>There was a problem with the response.</p>';
            /*echo '<pre>';
            var_dump($file);
            echo '</pre>';*/
            die();
        }

    // Check response is OK
        if ($remote['result'] == 'error') {
            // There was a problem. $remote['messages'] will contain information about what went wrong.
            echo '<p>Error updating case.</p><ul>';
            foreach ($remote['messages'] as $message) {
                echo '<li>' . $message . '</li>';
            }
            echo '</ul>';
            die();
        } elseif ($remote['result'] != 'success') {
            // There was a problem, and the response was not in the right format.
            echo '<p>Error updating case. Response in wrong format.</p>';
            /*echo '<pre>';
            var_dump($remote);
            echo '</pre>';*/
            die();
        }

    // Still here? Everything went as planned.
        echo '<p>New case sent to Envoy successfully.</p>';