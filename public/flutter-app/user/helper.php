<?php
    function parseUserAgent($userAgent) {
        $browser = 'Unknown';
        $version = '';
        $platform = 'Unknown';
        $platformVersion = '';
        $deviceType = 'desktop'; // default

        if (preg_match('/PostmanRuntime\/([0-9.]+)/', $userAgent, $matches)) {
            return [
                'browser' => 'Postman',
                'browser_version' => $matches[1],
                'platform' => 'API Client',
                'platform_version' => '',
                'device_type' => 'bot'
            ];
        }
        // Detect Platform
        if (preg_match('/Windows NT ([0-9.]+)/', $userAgent, $matches)) {
            $platform = 'Windows';
            $platformVersion = $matches[1];
        } elseif (preg_match('/Android ([0-9.]+)/', $userAgent, $matches)) {
            $platform = 'Android';
            $platformVersion = $matches[1];
            $deviceType = 'mobile';
        } elseif (preg_match('/iPhone OS ([0-9_]+)/', $userAgent, $matches)) {
            $platform = 'iOS';
            $platformVersion = str_replace('_', '.', $matches[1]);
            $deviceType = 'mobile';
        } elseif (preg_match('/Mac OS X ([0-9_]+)/', $userAgent, $matches)) {
            $platform = 'macOS';
            $platformVersion = str_replace('_', '.', $matches[1]);
        } elseif (preg_match('/Linux/', $userAgent)) {
            $platform = 'Linux';
        }

        // Detect Browser
        if (preg_match('/Edg\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Edge';
            $version = $matches[1];
        } elseif (preg_match('/Chrome\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Chrome';
            $version = $matches[1];
        } elseif (preg_match('/Firefox\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Firefox';
            $version = $matches[1];
        } elseif (preg_match('/Safari\/([0-9.]+)/', $userAgent) && !preg_match('/Chrome/', $userAgent)) {
            $browser = 'Safari';
            $version = $matches[1];
        }

        return [
            'browser' => $browser,
            'browser_version' => $version,
            'platform' => $platform,
            'platform_version' => $platformVersion,
            'device_type' => $deviceType
        ];
    }

    function getDeviceId()
    {
        $device_ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        $sec_ch_ua = $_SERVER['HTTP_SEC_CH_UA'] ?? '';
        $sec_ch_ua_platform = $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '';
        $sec_ch_ua_mobile = $_SERVER['HTTP_SEC_CH_UA_MOBILE'] ?? '';

        $raw_string = $user_agent . $accept_language . $sec_ch_ua . $sec_ch_ua_platform . $sec_ch_ua_mobile . $device_ip;

        return hash('sha256', $raw_string);
    }
?>