<?php
    $this->title = 'Dashboard';

?>

<h1>Hello <?= ucfirst(Yii::$app->user->identity->first_name) ?></h1>

<div class="row">
    <div class="container">
        <ul class="list-inline text-center" id="iconsContainer">
            <li>
                <a href = "/mastermind/manage/index">
                    <i class="fas fa-gamepad fa-10x text-primary"></i><br>
                    <span>Play Word MasterMind</span>
                </a>
            </li>
            <li>
                <a href="/ranking/manage/index">
                    <i class="fas fa-trophy fa-10x text-success"></i><br>
                    <span>Top Ranking Board</span>
                </a>
            </li>
            <li>
                <a href = "/logout">
                    <i class="fas fa-sign-out-alt fa-10x text-danger"></i><br>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>
