<?php
    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;
    $this->title = 'Login';
?>

<div id="login-wrapper">
    <div id="logo">
        <img src='http://www.deltapath.com/wp-content/uploads/Deltapath-logo1.svg' class="img-responsive">
    </div>
    <div id="content">
         <?php if (Yii::$app->session->hasFlash('success')) { ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Great!</strong> <?= Yii::$app->session->getFlash('success'); ?>
            </div>
        <?php } ?>
        <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'method'    => 'post',
                    'fieldConfig' => [
                            'template' => "<div class=\"control-group\">{input}</div>\n<div>{error}</div>"
                    ]]);
        ?>

        <?= $form->field($model, 'username')->textInput(array('placeholder'=>'Username')) ?>

        <?= $form->field($model, 'password')->passwordInput(array('placeholder'=>'Password')) ?>

        <div class="row">
            <div class="col-md-6">
                <input type="submit" class="btn btn-primary btn-block" name="login-button" value="Login">
            </div>
            <div class="col-md-6">
                <a href="/register" class="btn btn-success btn-block">Register</a>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>


