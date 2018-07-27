<?php
    use yii\bootstrap\ActiveForm;
    use app\assets\AlertAsset;

    AlertAsset::register($this);
    $this->title = 'Word MasterMind';
?>

<h1 class="text-center">
    <i class="fas fa-gamepad fa-3x text-primary"></i><br>
    Word MasterMind
</h1>

<div class="container">
    <div class="col-md-8 col-md-offset-2 col-sm-12" id="playMenuContainer">
        <!-- Game Information -->
        <div class="col-md-6 col-sm-12 text-center">
            Number of Turns Left: <span id="numberOfTurn">10</span>
        </div>
        <div class="col-md-6 col-sm-12 text-center">
            Number of Moves Made: <span id="numberOfMoves">0</span>
        </div>
        <div class="col-md-12">
            <div id="timerContainer"></div>
        </div>
        
        <hr />
        <div class="clearfix"></div>
        
        <div id="failedContainer">
            <div class="alert alert-danger" role="alert">You failed to guess the correct word. The word is <strong><span id="correctWord"></span></strong>.
                <ul class="list-inline">
                    <li><a href='/mastermind/manage/game' class="btn btn-primary">Play again</a></li>
                    <li><a href='/' class="btn btn-danger">Back to Main Menu</a></li>
                </ul>
            </div>
        </div>
        
         <div id="successContainer">
            <div class="alert alert-success" role="alert"><strong>Congratulations!</strong> You guess the correct word.
                <ul class="list-inline">
                    <li><a href='/mastermind/manage/game' class="btn btn-primary">Play again</a></li>
                    <li><a href='/' class="btn btn-danger">Back to Main Menu</a></li>
                </ul>
             </div>
        </div>
        
        <!-- Game Form -->
       <?php $form = ActiveForm::begin([
            'id' => 'search-place-form',
            'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
        ]); ?>
            <p id="errorMsg" class="text-danger"></p>
            <div class="col-md-7" id="attempts">
                <input class="form-control" placeholder="Input Five Letter Guess Word" type='text' id='guessWord' value='' maxlength='5'  />
            </div>
            <div class="col-md-5 text-center">
                <input class="btn btn-success" type='button'  id="guessBtn" value='Guess Word' />
                <input class="btn btn-danger" type='button'  id="quitBtn" value='Give up' />
            </div>
            <div class="clearfix"></div>
        <?php ActiveForm::end(); ?>
        
        <!-- Game result -->
        <div id="resultContainer">
            <table class="table">
                <thead>
                    <th class="text-center">#</th>
                    <th class="text-center">Guessed Word</th>
                    <th class="text-center">Result</th>
                </thead>
                <tbody>
                
                </tbody>
            </table>
        </div>
    </div>
</div>
