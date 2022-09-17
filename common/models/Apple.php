<?php

namespace common\models;

use Yii;
use common\appleState\AppleStateInterface;
use common\appleState\states\BittenOffAppleState;
use common\appleState\states\FallenAppleState;
use common\appleState\states\OnTreeAppleState;
use common\appleState\states\RottenAppleState;
use yii\db\StaleObjectException;
use yii\helpers\Html;

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
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->state = new OnTreeAppleState();
    }

    const EXPIRATION_TIME = 5 * 60 * 60;

    const STATUS = [
        "ON_TREE" => "on_tree",
        "FALLEN" => "fallen",
        "BITTEN_OFF" => "bitten_off",
        "ROTTEN" => "rotten",
    ];

    private AppleStateInterface $state;

    public $remainedPercent;

    public $biteSize;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'apple';
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->remainedPercent = $this->remained * 100;
        $this->refreshState();
    }

    public function beforeValidate(): bool
    {
        if (isset($this->remainedPercent)) {
            $this->remained = $this->remainedPercent / 100;
        }

        $statuses = array_flip($this->statusMap());
        $this->status = $statuses[get_class($this->state)];

        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['color', 'created_date', 'status', 'remained'], 'required'],
            ['color', 'match', 'pattern' => '/^(\w|\d){6}$/'],
            [['created_date', 'fallen_date'], 'integer'],
            [['remained'], function () {
                $isOk = $this->remained > 0 && $this->remained <= 1;
                if (!$isOk) {
                    $this->addError("remained", "Remained value must be between 1 and 0");
                }
            }],
            [['remainedPercent'], 'integer', 'min' => 1, 'max' => 100, 'when' => function () {
                return isset($this->remainedPercent);
            }],
            ['biteSize', 'required', 'on' => 'biting-off'],
            ['biteSize', 'integer', 'min' => 1, 'max' => 100],
            [['color'], 'string', 'max' => 6],
            [['status'], 'string', 'max' => 10],
            [['status'], 'in', 'range' => self::STATUS],
        ];
    }

    public function statusMap(): array
    {
        return [
            self::STATUS["ON_TREE"] => OnTreeAppleState::class,
            self::STATUS["FALLEN"] => FallenAppleState::class,
            self::STATUS["BITTEN_OFF"] => BittenOffAppleState::class,
            self::STATUS["ROTTEN"] => RottenAppleState::class
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
        $this->fallen_date = time();
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function biteOff(): void
    {
        $this->setState($this->state->biteOff($this->biteSize, $this));
        $this->remainedPercent -= $this->biteSize;

        if ($this->remainedPercent == 0) {
            $this->delete();
        } elseif (!$this->save()) {
            $errors = $this->getFirstErrors();
            throw new \Exception(json_encode($errors));
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

    public function refreshState(): void
    {
        if ($this->fallen_date && $this->fallen_date < (time() - self::EXPIRATION_TIME)) {
            $this->status = self::STATUS["ROTTEN"];
        }
        $statusMap = $this->statusMap();
        if (isset($statusMap[$this->status])) {
            $this->state = new ($statusMap[$this->status]);
        }
    }

    public function getStatusLabel(): string
    {
        $statusLabels = self::statusLabels();
        $label = $statusLabels[$this->status];
        $statusName = ucfirst(str_replace('_', ' ', $this->status));
        return Html::tag('span', $statusName, ['class' => "label label-$label"]);
    }

    public static function statusNames(): array
    {
        return array_map(function (string $name): string {
            return ucfirst(str_replace('_', ' ', $name));
        }, array_flip(self::STATUS));
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS["ON_TREE"] => "success",
            self::STATUS["FALLEN"] => "info",
            self::STATUS["BITTEN_OFF"] => "warning",
            self::STATUS["ROTTEN"] => "danger",
        ];
    }
}
