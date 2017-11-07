<?php
/**
 * Created by PhpStorm.
 * User: axlyo
 * Date: 11/7/2017
 * Time: 3:00 PM
 */

namespace Shikakunhq\VNDBClient;

use Illuminate\Support\Facades\Facade;
use Shikakunhq\VNDBClient\lib\Client;


class VNDBRequest extends Facade
{

    public static function getVN($title) {
        $request = new Client();
        $request->connect();
        $request->login(config('vndb.username'),config('vndb.password'));
        $data1 = $request->sendCommand('get vn basic,details (title="'. $title .'")');
        $new1 = new \ReflectionObject($data1);
        $rep1 = $new1->getProperty('data');
        $rep1->setAccessible(TRUE);
        $decode1 = $rep1->getValue($data1);
        $data2 = $request->sendCommand('get release basic,producers (vn="' . $decode1['items'][0]['id'] . '")');
        $new2 = new \ReflectionObject($data2);
        $rep2 = $new2->getProperty('data');
        $rep2->setAccessible(TRUE);
        $decode2 = $rep2->getValue($data2);
        return array($decode1,$decode2);

    }

    private static function process($title) {
        $request = new Client();
        $data1 = $request->sendCommand('get vn basic,details (title="' . $title . '")');
        $new1 = new \ReflectionObject($data1);
        $rep1 = $new1->getProperty('data');
        $rep1->setAccessible(TRUE);
        $decode1 = $rep1->getValue($data1);



        $data2 = $request->sendCommand('get release basic,producers (vn="' . $decode1['items'][0]['id'] . '")');
        $new2 = new \ReflectionObject($data2);
        $rep2 = $new2->getProperty('data');
        $rep2->setAccessible(TRUE);
        $decode2 = $rep2->getValue($data2);


        return response()->json(array($decode1,$decode2));
    }


    // Access a property with no restrictions
    function stole($object,$property){
        $dict = (array)$object;
        $class = get_class($object);
        return isset($dict[$property])?
            $dict[$property]:(isset($dict["\0*\0$property"])?
                $dict["\0*\0$property"]:(isset($dict["\0$class\0$property"])?
                    $dict["\0$class\0$property"]:null));
    }
    // Modify a property with no restrictions
    public static function inject(&$object,$property,$value){
        $dict = (array)$object;
        $class = get_class($object);
        if (isset($dict["\0$class\0$property"])) {
            $dict["\0$class\0$property"] = $value;
            $object = castToClass($dict,$class);
        } elseif (isset($dict["\0*\0$property"])) {
            $dict["\0*\0$property"] = $value;
            $object = castToClass($dict,$class);
        } elseif (isset($dict[$property])) {
            $object->$property = $value;
        } else {
            return ;
        }
    }
    // Required because we don't know if className has a __set_state defined.
    function castToClass(array $array,$className) {
        return unserialize(
            'O:'.strlen($className).':"'
            .$className.'"'.strstr(serialize($array), ':')
        );
    }


}