<?php
namespace
class hello
{
    private static $obj;
    
    public function __construct(array $data=[] , $split = '')
    {
        $this->data = $data;
        $this->splite = $split;
    }
    
    public function abc()
    {
        $name = $this->data;
        $arr = array(
            'name' => $this->data,
            'splite' => $this->splite
        );
    }
}
