<?php

namespace Shikakunhq\VNDBClient\lib;

class Response
{
    private $type;
    private $data;

    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        $this->type = $type;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
    public function getData()
    {
        return $this->data;
    }

}
