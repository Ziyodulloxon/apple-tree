<?php

namespace common\components;

use common\models\Apple;
use yii\db\Exception;

class AppleFactory
{
    /**
     * @return Apple[]
     *
     * @throws \Exception
     */
    public function makeSeveralApples(): array
    {
        $numberOfApples = random_int(2, 5);
        $resultArray = [];
        for ($i = 0; $i < $numberOfApples; $i++) {
            $apple = $this->make();
            if (!$apple->save()) {
                throw new Exception("Error while creating apples");
            }
            $resultArray[] = $apple;
        }
        return $resultArray;
    }

    public function make(): Apple
    {
        $apple = new Apple();
        $apple->color = $this->generateColor();
        $apple->created_date = time();
        $apple->remained = 1.00;
        $apple->status = Apple::STATUS["ON_TREE"];
        $apple->refreshState();
        return $apple;
    }

    private function generateColor(): string
    {
        return sprintf('%06X', mt_rand(0xFF9999, 0xFFFF00));
    }
}
