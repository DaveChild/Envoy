<?php

    // Settings
        $account_alias = 'demo'; // From Envoy, e.g., for http://example.envoyapp.com/ this would be "example".
        $api_key = 'CB4BH8FF2ZH9BD333HTVDD85NYMCPMC1'; // Generate this in Envoy (Settings > API).

    // Build URL.
        $url = 'http://' . $account_alias . '.envoyapp.com/api/cases/user1-open/';

    // Sign the request
        $request_time = date("U"); // Used to sign request
        $api_signature = md5($api_key . $request_time);

    // Get and decode data
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X_API_SIGNATURE: ' . $api_signature, 'X_API_REQUEST_TIME: ' . $request_time));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
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
            echo '<p>Error fetching data.</p><ul>';
            foreach ($remote['messages'] as $message) {
                echo '<li>' . $message . '</li>';
            }
            echo '</ul>';
            die();
        } elseif ($remote['result'] != 'success') {
            // There was a problem, and the response was not in the right format.
            echo '<p>Error fetching cases. Response in wrong format.</p>';
            /*echo '<pre>';
            var_dump($remote);
            echo '</pre>';*/
            die();
        }

    // Handle data as required ...
        echo '<pre>';
        var_dump($remote);
        echo '</pre>';