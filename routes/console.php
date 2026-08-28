<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('system:guard --full')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->onOneServer();
