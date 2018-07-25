<?php
    use yii\bootstrap\ActiveForm;
    $this->title = 'Word MasterMind';
?>

<h1 class="text-center">
    <i class="fas fa-gamepad fa-3x text-primary"></i><br>
    Word MasterMind
</h1>

<div class="container">
    <div class="col-md-8 col-md-offset-2 col-sm-12" id="playMenuContainer">
       <?php $form = ActiveForm::begin([
            'id' => 'search-place-form',
            'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
        ]); ?>
            <div class="col-md-5" id="attempts">
                <input class="form-control" placeholder="Number" type='text' id='attempt' name='attempt' value='' maxlength='4' />
            </div>
            <div class="col-md-7">
                <input class="btn btn-success" type='submit' class='submit' value='Mark number' />
                <input class="btn btn-danger" type='submit' class='submit' name="quit" value='Give up' />
                <input type='hidden' id='task' name='task' value='score' />
            </div>
        <?php echo $html; ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
