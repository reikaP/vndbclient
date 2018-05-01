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

    public static function getInfobyId($id)
    {
        try {
            $vn = self::vnbyId($id)->data['items']['0'];
            $publisher = self::producerById($vn['id'])->data['items']['0']['producers']['0'];
            $result = (object) array(
                'id'          => $vn['id'],
                'producer_id' => $publisher['id'],
                'title'       => $vn['title'],
                'producer'    => $publisher['name'],
                'original'    => $vn['original'],
                'aliases'     => $vn['aliases'],
                'released'    => $vn['released'],
                'description' => $vn['description'],
                'image'       => preg_replace('#^https?://#', '', $vn['image']),
                'image_nsfw'  => $vn['image_nsfw'],
                'relation'    => $vn['relations'],
                'characters' => self::getCharabyVNID($id),
            );
        } catch (\ErrorException $e) {
            return 'hehe';
        }
        sleep(1);

        return $result;
    }

    public static function getCharabyVNID($id)
    {
        $chara = self::charactersById($id)->data['items'];
        if($chara) {
            foreach ($chara as $character) {
                $getChara[] = [
                    'id'          => $character['id'],
                    'name'        => $character['name'],
                    'original'    => $character['original'],
                    'gender'      => $character['gender'],
                    'description' => $character['description'],
                    'bloodt'      => $character['bloodt'],
                    'image'       => preg_replace('#^https?://#', '', $character['image']),
                    'aliases'     => $character['aliases'],
                    'role'        => $character['vns'][0][3],
                ];

                try {
                    if (!self::throwoutAppears($id, $character['vns'])) {
                        $getChara[] = [
                            'id'          => $character['id'],
                            'name'        => $character['name'],
                            'original'    => $character['original'],
                            'gender'      => $character['gender'],
                            'description' => $character['description'],
                            'bloodt'      => $character['bloodt'],
                            'image'       => preg_replace('#^https?://#', '', $character['image']),
                            'aliases'     => $character['aliases'],
                            'role'        => $character['vns'][0][3],
                        ];
                    }
                } catch(ErrorException $e) {
                    $getChara[] = '';
                }
            }
        } else {
            $getChara = [];
        }

        return $getChara;
    }

    public static function staffById($staff) {
        return self::client()->sendCommand('get staff basic (id="'.$staff.'")');
    }

    public static function producerById($producer)
    {
        return self::client()->sendCommand('get release producers (vn="'.$producer.'")');
    }

    public static function charactersById($character)
    {
        return self::client()->sendCommand('get character basic,details,voiced,vns (vn="'.$character.'") {"results":25}');
    }

    public static function vn($title)
    {
        return self::client()->sendCommand('get vn basic,details,staff,relations (title="'.$title.'")');
    }

    public static function vn2nd($title)
    {
        return self::client()->sendCommand('get vn basic,details,staff,relations (search~"'.$title.'")');
    }

    public static function vnbyId($id)
    {
        return self::client()->sendCommand('get vn basic,details,relations (id="'.$id.'")');
    }

    public static function command($command)
    {
        return self::client()->sendCommand($command);
    }

    public static function throwoutAppears($id, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['0'] === $id) {
                return $key;
            }
        }
    }
}
