<?php

namespace Shikakunhq\VNDBClient;

use Shikakunhq\VNDBClient\lib\Client;

class VNDBRequest
{
    public static function getInfobyId($id)
    {
        try {
            ob_start();
            $data = self::pipelining($id);

            //Characters
            if(!$data->characters->data['num'] == 0) {
                foreach ($data->characters->data['items'] as $character) {
                    $charaArray[] = [
                        'id' => $character['id'],
                        'name' => $character['name'],
                        'original' => $character['original'],
                        'description' => htmlspecialchars(preg_replace('/\[.*\]/', '', $character['description'])),
                        'gender' => $character['gender'],
                        'bloodt' => $character['bloodt'],
                        'bust' => $character['bust'],
                        'waist' => $character['waist'],
                        'hip' => $character['hip'],
                        'height' => $character['height'],
                        'weight' => $character['weight'],
                        'image' => preg_replace('#^https?://#', '', $character['image']),
                        'aliases' => $character['aliases'],
                        'role' => $character['vns'][0][3],
                        'traits' => [
                            'list' => $character['traits'],
                        ],

                    ];
                }
            } else {
                $charaArray[] = null;
            }

            //Visual Novel Information

            if (self::skipRedundancy($charaArray, 'id')) {
                foreach (self::skipRedundancy($charaArray, 'id') as $character) {
                    $character2[] = $character['id'];
                }
            } else {
                $producer2 = null;
            }

            if ($data->producers) {
                foreach ($data->producers as $producer) {
                    $producer2[] = $producer['id'];
                }
            } else {
                $producer2[] = null;
            }

            if ($data->vn['staff']) {
                foreach ($data->vn['staff'] as $staff) {
                    $staff2[] = $staff['aid'];
                }
            } else {
                $staff2[] = null;
            }

            if ($data->vn['tags']) {
                foreach ($data->vn['tags'] as $tags) {
                    $tags2[] = $tags[0];
                }
            } else {
                $tags2[] = null;
            }

            $result = (object) [
                'status' => 'ok',
                'data' => (object) array(
                    'id'          => $data->vn['id'],
                    'title'       => $data->vn['title'],
                    'original'    => $data->vn['original'],
                    'aliases'     => $data->vn['aliases'],

                    'released'         => $data->vn['released'],
                    'description'      => $data->vn['description'],
                    'image'            => preg_replace('#^https?://#', '', $data->vn['image']),
                    'image_nsfw'       => $data->vn['image_nsfw'],
                    'relation'         => $data->vn['relations'],
                    'characters'       => [
                        'item'  => implode(',', $character2),
                        'list'  => self::skipRedundancy($charaArray, 'id'),
                    ],
                    'staff'       => [
                        'item'  => implode(',', $staff2),
                        'list'  => $data->vn['staff'],
                    ],
                    'tags'       => [
                        'item'  => implode(',', $tags2),
                        'list'  => $data->vn['tags'],
                    ],
                    'producers'       => [
                        'item'  => implode(',', $producer2),
                        'list'  => $data->producers,
                    ],
                )

            ];

            return $result;
            clearstatcache();
            unset($result);
            exit();

        } catch (\ErrorException $e) {
            return (object) array(
              'status' => 'error',
              'message' => 'Limit request reached '. $e->getMessage(),
            );

            exit();
        }

        exit();
    }

    public static function pipelining($id)
    {
        $connect = new Client();
        $connect->connect();
        $connect->login(config('vndb.username'), config('vndb.password'));

        $result = (object) [
            'characters'    => $connect->sendCommand('get character basic,details,voiced,vns,meas,traits (vn="'.$id.'") {"results":25}'),
            'vn'            => $connect->sendCommand('get vn basic,details,relations,staff,tags (id="'.$id.'")')->data['items'][0],
            'producers'     => $connect->sendCommand('get release producers (vn="'.$id.'")')->data['items'][0]['producers'],
            'staff'         => $connect->sendCommand('get staff basic (id="'.$id.'")')->data['items'],

        ];
        $connect->isConnected();

        return $result;
    }

    public static function importData() {
        $connect = new Client();
        $connect->connect();
        $connect->login(config('vndb.username'), config('vndb.password'));

        return (object) $connect->sendCommand('get vnlist basic (uid="37836")');
    }


    private static function test($id) {
        $connect = new Client();
        $connect->connect();
        $connect->login(config('vndb.username'), config('vndb.password'));
        $test = $connect->sendCommand('get character basic,details,voiced,vns,meas,traits (vn="'.$id.'") {"results":25}');
        $connect->isConnected();
        return $test;

    }

    private static function throwoutAppears($id, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['0'] === $id) {
                return $key;
            }
        }
    }

    private static function skipRedundancy($array, $key)
    {
        $temp_array = [];
        $i = 0;
        $key_array = [];

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }

        return $temp_array;
    }
}
