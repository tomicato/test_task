<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<!--Форма загрузк документа-->
<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data']
]);
?>
<?php if(isset($model))?>
<?= $form->field($model, 'file')->fileInput(); ?>

<div class="form-group">
    <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
