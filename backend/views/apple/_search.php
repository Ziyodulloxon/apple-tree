<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\search\AppleSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="apple-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'color') ?>

    <?= $form->field($model, 'created_date') ?>

    <?= $form->field($model, 'fallen_date') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'remained') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
