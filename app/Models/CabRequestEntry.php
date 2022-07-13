<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CabRequestEntry extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public static function calculateDistance($args)
    {
        $last_location = self::getLastLocation($args['request_id']);
        if($last_location) {
            return (self::sphereDistance(
                $args['latitude'], 
                $args['longitude'], 
                $last_location->latitude, 
                $last_location->longitude
            ) + $last_location->distance);
        }
        return 0;
    }

    public static function getLastLocation($request_id)
    {
        return  self::where('request_id', $request_id)->latest()->first();
    }

    protected static function sphereDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $rad = M_PI / 180;
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin($latitudeFrom * $rad) * sin($latitudeTo * $rad) +  cos($latitudeFrom * $rad) * cos($latitudeTo * $rad) * cos($theta * $rad);
        return acos($dist) / $rad * 60 * 1853;
    }
}
