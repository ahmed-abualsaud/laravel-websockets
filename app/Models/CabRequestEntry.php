<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CabRequestEntry extends Model
{
    protected $guarded = [];

    protected $primaryKey = 'request_id';

    public $timestamps = false;

    public static function getRoute($args)
    {
        $last_location = self::getLastLocation($args['request_id']);
        if($last_location) {
            return [ 
                'distance' => (self::sphereDistance(
                    $args['latitude'], 
                    $args['longitude'], 
                    $last_location->latitude, 
                    $last_location->longitude
                ) + $last_location->distance),

                'path' => $last_location->path.'|'.$args['latitude'].','.$args['longitude']
            ];
        }
        return ['distance' => 0, 'path' => $args['latitude'].','.$args['longitude']];
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
