<?php

namespace common\models;

use common\appleState\AppleStateInterface;
use Yii;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "apple".
 *
 * @property int $id
 * @property string $color
 * @property int $created_date
 * @property int|null $fallen_date
 * @property string $status
 * @property float $remained
 */
class Apple extends \yii\db\ActiveRecord
{
    private AppleStateInterface $state;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'apple';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['color', 'created_date'], 'required'],
            [['created_date', 'fallen_date'], 'integer'],
            [['remained'], 'number'],
            [['color'], 'string', 'max' => 6],
            [['status'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'color' => 'Color',
            'created_date' => 'Created Date',
            'fallen_date' => 'Fallen Date',
            'status' => 'Status',
            'remained' => 'Remained',
        ];
    }

    public function setState(AppleStateInterface $appleState): void
    {
        $this->state = $appleState;
    }

    public function fall(): void
    {
        $this->setState($this->state->fall());
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function biteOff(int $percent): void
    {
        $this->setState($this->state->biteOff($percent, $this));
        $this->remained -= ($percent / 100);
        if ($this->remained === 0) {
            $this->delete();
        }
    }

    /**
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function eat(): void
    {
        $this->setState($this->state->eat());
        $this->delete();
    }

    public function rot(): void
    {
        $this->setState($this->state->rot($this));
    }
}
