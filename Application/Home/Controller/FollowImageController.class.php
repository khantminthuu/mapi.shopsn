<?php

namespace Home\Controller;

use Common\Logic\FollowImageLogic;
use Common\TraitClass\InitControllerTrait;


class FollowImageController
{
    use InitControllerTrait;
    public function __construct(array $args=[])
    {
        $this ->args = $args;
        $this -> logic = new FollowImageLogic($args);
    }


    public function getCategory()
    {

        $ret = $this->logic->getAllCategory();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

    public function getFollow()
    {
        $ret = $this->logic->getAllFollow();

        echo "<pre>";
        print_r($ret);
        die;

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);

    }

}