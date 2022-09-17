<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Apple;

/**
 * AppleSearch represents the model behind the search form of `common\models\Apple`.
 */
class AppleSearch extends Model
{
    public $id;

    public $status;

    public $remained;

    public $created_date;

    public $fallen_date;

    public $color;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['status'], 'safe'],
            [['remained'], 'number', 'min' => 1, 'max' => 100],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Apple::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->status === Apple::STATUS["ROTTEN"]) {
            $time = time() - Apple::EXPIRATION_TIME;
            $query->andFilterWhere(["<", "fallen_date", $time]);
        } elseif ($this->status === Apple::STATUS["FALLEN"]) {
            $time = time() - Apple::EXPIRATION_TIME;
            $query->andFilterWhere([">=", "fallen_date", $time]);
        } elseif ($this->status) {
            $query->andFilterWhere(['status' => $this->status]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'remained' => $this->remained ? (int)$this->remained / 100 : '',
        ]);

        return $dataProvider;
    }
}
