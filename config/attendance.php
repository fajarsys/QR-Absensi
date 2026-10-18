<?php

return [

    /*
    |--------------------------------------------------------------------------
    | QR Code Window Size (in seconds)
    |--------------------------------------------------------------------------
    |
    | This value determines the window size for the dynamic QR code.
    | The QR code changes every this many seconds.
    |
    */

    'qr_window' => env('ATTENDANCE_QR_WINDOW', 15),

    /*
    |--------------------------------------------------------------------------
    | Saturday Pullout Time
    |--------------------------------------------------------------------------
    |
    | This value determines the normal pullout time on Saturdays.
    | Format: H:i:s
    |
    */

    'saturday_pullout_time' => env('ATTENDANCE_SATURDAY_PULLOUT_TIME', '14:00:00'),

    /*
    |--------------------------------------------------------------------------
    | Early Arrival Threshold (in minutes)
    |--------------------------------------------------------------------------
    |
    | This value determines how many minutes before the scheduled start time
    | is considered early arrival (and thus classified as masuk lembur).
    |
    */

    'early_arrival_threshold' => env('ATTENDANCE_EARLY_ARRIVAL_THRESHOLD', 60),

];