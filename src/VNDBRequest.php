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
            $result = (object) [
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
                'characters'  => self::getCharabyVNID($id),
            ];
        } catch (\ErrorException $e) {
            return 'hehe';
        }
        sleep(1);

        return $result;
    }

    public static function getCharabyVNID($id)
    {

        $pageNos = array();
        $chara = self::charactersById($id)->data['items'];
        if ($chara) {

            $new_array = array();
            $exists    = array();

            foreach ($chara as $character) {
                $charaArray[] = [
                    'id'          => $character['id'],
                    'name'        => $character['name'],
                    'original'    => $character['original'],
                    'gender'      => $character['gender'],
                    'description' => htmlspecialchars(preg_replace('/\[.*\]/', '', $character['description'])),
                    'bloodt'      => $character['bloodt'],
                    'image'       => preg_replace('#^https?://#', '', $character['image']),
                    'aliases'     => $character['aliases'],
                    'role'        => $character['vns'][0][3],
                ];

                $showChara = self::skipRedundancy($charaArray,'id');




                try {
                    if (!self::throwoutAppears($id, $character['vns'])) {
                        $charaArray[] = [
                            'id'          => $character['id'],
                            'name'        => $character['name'],
                            'original'    => $character['original'],
                            'gender'      => $character['gender'],
                            'description' => htmlspecialchars(preg_replace('/\[.*\]/', '', $character['description'])),
                            'bloodt'      => $character['bloodt'],
                            'image'       => preg_replace('#^https?://#', '', $character['image']),
                            'aliases'     => $character['aliases'],
                            'role'        => $character['vns'][0][3],
                        ];
                        $showChara = self::skipRedundancy($charaArray,'id');
                    }
                } catch (ErrorException $e) {
                    $showChara = [];
                }
            }
        } else {
            $showChara = [];
        }

        return $showChara;
    }

    public static function staffById($staff)
    {
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
    public static function skipRedundancy($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }
}
