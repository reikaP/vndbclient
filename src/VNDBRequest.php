<?php

namespace Shikakunhq\VNDBClient;
use Shikakunhq\VNDBClient\lib\Client;

class VNDBRequest
{

    private static function client()
    {
        $connect = new Client();
        $connect->connect();
        $connect->login(config('vndb.username'), config('vndb.password'));
        return $connect;
    }

    public static function getInfo($title)
    {
        try {
            $vn = self::vn($title)->data['items']['0'];
            $publisher = self::producerById($vn['id'])->data['items']['0']['producers']['0'];
            $result = (object)array(
                'id' => $vn['id'],
                'producer_id' => $publisher['id'],
                'title' => $vn['title'],
                'producer' => $publisher['name'],
                'original' => $vn['original'],
                'aliases' => $vn['aliases'],
                'released' => $vn['released'],
                'description' => $vn['description'],
                'image' => $vn['image'],
                'image_nsfw' => $vn['image_nsfw'],
            );
        } catch (\ErrorException $e) {
            $vn = self::vn2nd($title)->data['items']['0'];
            $publisher = self::producerById($vn['id'])->data['items']['0']['producers']['0'];
            $result = (object)array(
                'id' => $vn['id'],
                'producer_id' => $publisher['id'],
                'title' => $vn['title'],
                'producer' => $publisher['name'],
                'original' => $vn['original'],
                'aliases' => $vn['aliases'],
                'released' => $vn['released'],
                'description' => $vn['description'],
                'image' => $vn['image'],
                'image_nsfw' => $vn['image_nsfw'],
            );
        }
        return $result;
    }

    public static function producerById($producer)
    {
        return self::client()->sendCommand('get release producers (vn="' . $producer . '")');
    }

    public static function vn($title)
    {
        return self::client()->sendCommand('get vn basic,details (search~"' . $title . '")');
    }

    public static function vn2nd($title)
    {
        return self::client()->sendCommand('get vn basic,details (title="' . $title . '")');
    }

    public static function command($command)
    {
        return self::client()->sendCommand($command);
    }


}
