<?php
    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;
    $this->title = 'Register';
?>

<div id="login-wrapper">
    <div id="logo">
        <img src='http://www.deltapath.com/wp-content/uploads/Deltapath-logo1.svg' class="img-responsive">
    </div>
    <div id="content">

        <!-- Flash Messages -->
        <?php if (Yii::$app->session->hasFlash('success')) { ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Success!</strong> <?= Yii::$app->session->getFlash('success'); ?>
            </div>
        <?php } else if (Yii::$app->session->hasFlash('error')) { ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Error!</strong> <?= Yii::$app->session->getFlash('error'); ?>
            </div>
        <?php } ?>
        <?php $form = ActiveForm::begin([
                    'id' => 'register-form',
                    'method'    => 'post',
                    'fieldConfig' => [
                            'template' => "<div class=\"control-group\">{input}</div>\n<div>{error}</div>"
                    ]]);
        ?>

        <?= $form->field($model, 'first_name')->textInput(array('placeholder'=>'First Name')) ?>
        <?= $form->field($model, 'last_name')->textInput(array('placeholder'=>'Last Name')) ?>
        <?= $form->field($model, 'username')->textInput(array('placeholder'=>'Account Name')) ?>
        <?= $form->field($model, 'email_address')->textInput(array('placeholder'=>'Email Address')) ?>
        <?= $form->field($model, 'password')->passwordInput(array('placeholder'=>'Password')) ?>
        <?= $form->field($model, 'confirmPassword')->passwordInput(array('placeholder'=>'Confirm Password')) ?>
        <a href="/" class="btn btn-danger">Back</a>
        <?= Html::submitButton(Yii::t('app', 'Register'), ['class' => 'btn btn-primary pull-right', 'name' => 'register-button']) ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
