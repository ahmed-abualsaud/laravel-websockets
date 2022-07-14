<?php

namespace App\Websockets;

use App\Models\Driver;
use App\Models\CabRequestEntry;

use Illuminate\Support\Arr;

use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use BeyondCode\LaravelWebSockets\WebSockets\WebSocketHandler;

use Illuminate\Support\Facades\Log;

class UpdateDriverLocation extends WebSocketHandler
{
    public function onMessage(ConnectionInterface $connection, MessageInterface $message)
    {
        parent::onMessage($connection, $message);
        $decoded_message = json_decode($message, true);

        if ($decoded_message['event'] == 'client-update.driver.location' && 
            array_key_exists('data', $decoded_message) && $decoded_message['data']) {

            $args = json_decode($decoded_message['data'], true);
            $channel_data = explode('.', $decoded_message['channel']);
            if (in_array($channel_data[0], ['private-Update', 'Update']) && $channel_data[1] == 'Driver' && $channel_data[2] == 'Location')
            {
                Driver::where('id', $channel_data[3])->update([
                    'latitude' => $args['latitude'],
                    'longitude' => $args['longitude']
                ]);
            }

            if (array_key_exists('request_id', $args) && $args['request_id']) {
                $input = Arr::only($args, ['request_id', 'latitude', 'longitude']);
                $input['distance'] = CabRequestEntry::calculateDistance($args);
                CabRequestEntry::create($input);
            }
        }
    }
}