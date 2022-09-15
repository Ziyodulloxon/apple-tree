<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Apple $model */

$this->title = 'Create Apple';
$this->params['breadcrumbs'][] = ['label' => 'Apples', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apple-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
