<?php

/**
 * Created by PhpStorm.
 * User: MrCraftCod
 * Date: 22/08/2016
 * Time: 11:38
 */
class SiteHelper
{
    public function millisecondsToTimeString($totalMilliseconds)
    {
        return $this->secondsToTimeString($totalMilliseconds / 1000);
    }

    public function secondsToTimeString($totalSeconds)
    {
        $hours = floor($totalSeconds / 3600);
        $minutes = floor($totalSeconds / 60 % 60);
        $seconds = floor($totalSeconds % 60);
        if ($hours > 0)
            return sprintf("%02d", $hours) . 'h' . sprintf("%02d", $minutes) . 'm' . sprintf("%02d", $seconds) . 's';
        elseif ($minutes > 0)
            return sprintf("%02d", $minutes) . 'm' . sprintf("%02d", $seconds) . 's';
        return sprintf("%02d", $seconds) . 's';
    }
}