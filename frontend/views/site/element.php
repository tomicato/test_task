<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<div class="container table-responsive">
    <h2 style="margin: 50px auto;" class="text-center">Тестовое задание Yii2 - Import CSV to MySql</h2>
    <table class="table table-bordered">

        <thead>
        <tr>
            <th>ID</th>
            <th>Наименование</th>
            <th>Категория 1</th>
            <th>Категория 2</th>
            <th>Категория 3</th>
            <th>Артикул</th>
            <th>Описание</th>
        </tr>
        </thead>
        <tbody>

        <?php if (isset($res)) foreach ($res as $val): ?>
            <tr>
                <td><?= $val['id'] ?></td>
                <td><?= $val['title'] ?></td>
                <td><?= $val['category1'] ?></td>
                <td><?= $val['category2'] ?></td>
                <td><?= $val['category3'] ?></td>
                <td><?= $val['vendor_name'] ?></td>
                <td><?= $val['description'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>

    <!--Форма загрузк документа-->
    <?php if (isset($model)): ?>
        <?php $form = ActiveForm::begin([
                'action' => ['element'],
                'method' => 'post',
                'options' => [
                        'data-pjax' => true,
                        'enctype' => 'multipart/form-data'
                ],
        ]); ?>
        <?= $form->field($model, 'file')->fileInput(); ?>

        <div class="form-group">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
            <?= Html::submitButton('Удалить', ['class' => 'btn btn-danger', 'id' => 'd', 'style' => 'float:right;']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    <?php endif; ?>
</div>

<form action="element" method="get">
    <input type="hidden" id="del" name="del">

</form>
<script>
$(document).ready(function () {

    $('#d').on('click', function(e){
        e.preventDefault();

        $.ajax({
            url: 'element',
            type: 'GET',
            data: {id: 53},
            success: function () {
                console.log('Success!');
                window.location.reload();
            },
            error: function () {
               console.log('Error!');
            }
        });
    });
});

</script>

