<?php

return [

    /*
    |--------------------------------------------------------------------------
    | System Guard Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi utama untuk System Guard monitoring & recovery engine.
    |
    */

    'enabled' => env('SYSTEM_GUARD_ENABLED', true),

    'check_interval_seconds' => env('SYSTEM_GUARD_INTERVAL', 900),

    'timeout_seconds' => env('SYSTEM_GUARD_TIMEOUT', 10),

    'max_retries' => env('SYSTEM_GUARD_MAX_RETRIES', 3),

    'retry_delay_seconds' => env('SYSTEM_GUARD_RETRY_DELAY', 30),

    'auto_recovery_enabled' => env('SYSTEM_GUARD_AUTO_RECOVERY', false),

    /*
    |--------------------------------------------------------------------------
    | Daemon Settings (STEP 4 — 24/7 background supervisor)
    |--------------------------------------------------------------------------
    |
    | Kontrol perilaku daemon supervisor background. Daemon berjalan sebagai
    | loop tak-terbatas terpisah dari scheduler Laravel, sehingga monitoring
    | terus hidup tanpa browser / tanpa terminal terbuka / saat user logout.
    |
    */

    'daemon' => [
        'enabled'           => env('SYSTEM_GUARD_DAEMON_ENABLED', true),
        'interval_seconds'  => env('SYSTEM_GUARD_DAEMON_INTERVAL', 15), // detik antar loop
        'heartbeat_seconds' => env('SYSTEM_GUARD_DAEMON_HEARTBEAT', 60), // interval heartbeat
        'max_heartbeat_age' => env('SYSTEM_GUARD_DAEMON_MAX_AGE', 180), // dianggap mati jika > ini
        'quick_mode'        => env('SYSTEM_GUARD_DAEMON_QUICK', true), // recovery cepat saat down
        'recovery_cooldown_seconds' => env('SYSTEM_GUARD_DAEMON_COOLDOWN', 300), // min jarak antar recover
        'backoff_base_seconds'      => env('SYSTEM_GUARD_DAEMON_BACKOFF', 60),
        'backoff_max_seconds'       => env('SYSTEM_GUARD_DAEMON_BACKOFF_MAX', 900),
        'max_consecutive_quick_failures' => env('SYSTEM_GUARD_DAEMON_MAX_QUICK_FAIL', 6),
        'log_level'         => env('SYSTEM_GUARD_LOG_LEVEL', 'info'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cloudflared / Tunnel Settings (STEP 4)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk deteksi & recovery Cloudflare Tunnel.
    |
    | Penting: command template & argumen harus FIXED / predefined (allowlisted).
    | JANGAN pernah menerima input user bebas sebagai perintah.
    |
    | Tempat {placeholder}:
    |   {cloudflared_bin}   -> path biner cloudflared
    |   {log_dir}           -> direktori log
    |   {tunnel_arg}        -> argumen tunnel yang dikonfigurasi (mis. `tunnel run <uuid>`)
    |
    */

    'cloudflared' => [
        'binary_path'       => env('SYSTEM_GUARD_CLOUDFLARED_PATH', 'C:\\Program Files (x86)\\cloudflared\\cloudflared.exe'),
        'config_path'       => env('SYSTEM_GUARD_CLOUDFLARED_CONFIG', ''),
        'tunnel_id'         => env('SYSTEM_GUARD_CLOUDFLARED_TUNNEL_ID', ''),
        'process_name'      => env('SYSTEM_GUARD_CLOUDFLARED_PROCESS', 'cloudflared'),
        'log_dir'           => env('SYSTEM_GUARD_LOG_DIR', storage_path('logs/system-guard')),
        'quick_tunnel'      => env('SYSTEM_GUARD_QUICK_TUNNEL', true),
        // Template start command (FIXED args, allowlisted). Hanya placeholder predefined.
        'start_command'     => env('SYSTEM_GUARD_CLOUDFLARED_START', ''),
        'restart_command'   => env('SYSTEM_GUARD_CLOUDFLARED_RESTART', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Component Monitoring (STEP 4)
    |--------------------------------------------------------------------------
    |
    | Status dipisah per komponen: Internet / Cloudflare Tunnel / Origin.
    | Ini dipakai dashboard & recovery untuk membedakan sumber masalah.
    |
    */

    'components' => [
        'internet' => [
            'enabled' => env('SYSTEM_GUARD_COMPONENT_INTERNET', true),
            'check_targets' => [
                'https://1.1.1.1',
                'https://8.8.8.8',
                'https://www.cloudflare.com',
            ],
            'dns_targets' => ['1.1.1.1', 'google.com'],
        ],
        'tunnel' => [
            'enabled' => env('SYSTEM_GUARD_COMPONENT_TUNNEL', true),
            'check_process' => env('SYSTEM_GUARD_COMPONENT_TUNNEL_PROCESS', true),
        ],
        'origin' => [
            'enabled' => env('SYSTEM_GUARD_COMPONENT_ORIGIN', true),
            'base_url' => env('SYSTEM_GUARD_ORIGIN_URL', 'http://127.0.0.1:80'),
            'check_path' => env('SYSTEM_GUARD_COMPONENT_ORIGIN_PATH', '/'),
            'expected_status_code' => env('SYSTEM_GUARD_COMPONENT_ORIGIN_STATUS', 200),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Codes
    |--------------------------------------------------------------------------
    */

    'status' => [
        'online'       => 'ONLINE',
        'waspsda'      => 'WASPADA',
        'perhatian'    => 'PERHATIAN',
        'gangguan'     => 'GANGGUAN',
        'serious'      => 'SERIUS',
        'recovery'     => 'RECOVERY',
        'verifying'    => 'VERIFIKASI',
    ],

    /*
    |--------------------------------------------------------------------------
    | Severity Levels
    |--------------------------------------------------------------------------
    */

    'severity' => [
        'low'      => 'LOW',
        'medium'   => 'MEDIUM',
        'high'     => 'HIGH',
        'critical' => 'CRITICAL',
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Categories
    |--------------------------------------------------------------------------
    */

    'error_categories' => [
        'dns'          => 'DNS',
        'http'         => 'HTTP',
        'connection'   => 'CONNECTION',
        'timeout'      => 'TIMEOUT',
        'ssl'          => 'SSL',
        'unknown'      => 'UNKNOWN',
    ],

    /*
    |--------------------------------------------------------------------------
    | Recovery Action Whitelist
    |--------------------------------------------------------------------------
    |
    | Hanya action yang ada di daftar ini yang boleh dijalankan otomatis.
    | SEMUA action di luar whitelist TIDAK AKAN dijalankan.
    |
    */

    'recovery_whitelist' => [
        'dns_cache_flush'       => [
            'label'       => 'Flush DNS Cache',
            'type'        => 'dns',
            'risk_level'  => 'low',
            'description' => 'Membersihkan DNS cache untuk target domain',
        ],
        'retry_connection'      => [
            'label'       => 'Retry Connection',
            'type'        => 'connection',
            'risk_level'  => 'low',
            'description' => 'Mencoba koneksi ulang ke target',
        ],
        'wait_and_retry'        => [
            'label'       => 'Wait and Retry',
            'type'        => 'wait',
            'risk_level'  => 'low',
            'description' => 'Menunggu sebentar lalu mencoba ulang',
        ],
        'notify_admin'          => [
            'label'       => 'Notify Administrator',
            'type'        => 'notification',
            'risk_level'  => 'low',
            'description' => 'Mengirim notifikasi ke administrator',
        ],
        'escalate'              => [
            'label'       => 'Escalate Incident',
            'type'        => 'escalation',
            'risk_level'  => 'medium',
            'description' => 'Meningkatkan level incident ke admin',
        ],
        'start_cloudflared'     => [
            'label'       => 'Start Cloudflare Tunnel',
            'type'        => 'tunnel',
            'risk_level'  => 'medium',
            'description' => 'Menjalankan cloudflared (command template fixed, allowlisted)',
        ],
        'restart_cloudflared'   => [
            'label'       => 'Restart Cloudflare Tunnel',
            'type'        => 'tunnel',
            'risk_level'  => 'medium',
            'description' => 'Restart proses cloudflared (command template fixed, allowlisted)',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Blocked Actions (NEVER execute these)
    |--------------------------------------------------------------------------
    |
    | Action yang TIDAK BOLEH dijalankan dalam keadaan apapun.
    |
    */

    'blocked_actions' => [
        'shell_exec',
        'exec',
        'system',
        'passthru',
        'popen',
        'proc_open',
        'eval',
        'assert',
        'curl_exec_arbitrary',
        'file_put_contents_arbitrary',
        'unlink_arbitrary',
        'chmod_arbitrary',
        'chown_arbitrary',
        'sudo',
        'su',
        'kill',
        'reboot',
        'shutdown',
        'service_restart_all',
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Time Thresholds (ms)
    |--------------------------------------------------------------------------
    */

    'thresholds' => [
        'response_time_warning'   => 3000,
        'response_time_critical'  => 5000,
        'response_time_timeout'   => 10000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Incident Limits
    |--------------------------------------------------------------------------
    */

    'incident' => [
        'max_open_incidents_per_target' => 1,
        'auto_close_after_hours'        => 24,
        'history_retention_days'         => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */

    'notification' => [
        'enabled'          => env('SYSTEM_GUARD_NOTIFY', true),
        'on_recovery'      => true,
        'on_failure'       => true,
        'on_escalation'    => true,
        'notify_roles'     => ['ADMINISTRATOR'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Test / Mock Settings
    |--------------------------------------------------------------------------
    */

    'test' => [
        'dns_server'       => env('SYSTEM_GUARD_DNS_SERVER', '8.8.8.8'),
        'http_user_agent'  => 'SystemGuard/1.0',
        'follow_redirects' => true,
        'max_redirects'    => 5,
    ],

];
