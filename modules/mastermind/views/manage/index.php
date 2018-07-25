<?php
    $this->title = 'Word MasterMind';

?>

<h1 class="text-center">
    <i class="fas fa-gamepad fa-3x text-primary"></i><br>
    Word MasterMind
</h1>

<div class="container">
    <div class="col-md-8 col-md-offset-2 col-sm-12" id="playMenuContainer">
        Word MasterMind is a letter version of the tradition MasterMind game.
        <ul>
            <li>Each Player has 1- turns to guess the word</li>
            <li>The word must contain 5 letters to be a valid guess, if not then it does not lose you a turn</li>
            <li>
                For each turn, it will show the result of each letter. Each letter results one of these 3 possibilities:
                <ul class="list-inline text-center">
                    <li>
                        <i class="fas fa-check-double fa-3x"></i> <br />
                        <span>Correct Letter in the right position</span>
                    </li>
                    <li>
                        <i class="fas fa-exchange-alt fa-3x"></i> <br />
                        <span>Correct Letter but in the wrong position</span>
                    </li>
                    <li>
                        <i class="fas fa-times fa-3x"></i> <br />
                        <span>Incorrect Letter</span>
                    </li>
                </ul>
            </li>
        </ul>
        <strong>
            Note: You can be in the Top Ranking Board if you get a least number of moves with the shortest time.
        </strong>
        <ul class="list-inline text-center">
            <li>
                <a href="/" class="btn btn-danger btn-lg">Back to Menu</a>
            </li>
            <li>
                <a href="/mastermind/manage/game" class="btn btn-primary btn-lg">Play Game</a>
            </li>
        </ul>
    </div>
</div>
