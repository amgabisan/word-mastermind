<?php
    $this->title = 'Word MasterMind';
    $rank = 1;
?>

<h1 class="text-center">
    <i class="fas fa-trophy fa-3x text-primary"></i><br>
</h1>

<div class="container">
    <div class="col-md-8 col-md-offset-2 col-sm-12" id="playMenuContainer">
        <div class="col-md-6 text-center">
            <button type="button" class="btn btn-primary btn-block" aria-hidden="true" data-toggle="collapse" data-target="#personalRankingContainer" aria-expanded="false" aria-controls="personalRankingContainer">
                <span class="glyphicon glyphicon-user"></span> Personal Ranking
            </button>
        </div>
        <div class="col-md-6 text-center">
            <button type="button" class="btn btn-success btn-block" aria-hidden="true" data-toggle="collapse" data-target="#globalRankingContainer" aria-expanded="false" aria-controls="globalRankingContainer">
                <span class="glyphicon glyphicon-globe" aria-hidden="true"></span> Global Ranking
            </button>
        </div>

        <div class="clearfix"></div>
        <hr>

        <div id="personalRankingContainer" class="collapse">
            <h4>Personal Ranking</h4>

            <table class="table table-striped table-bordered">
                <thead>
                    <th class="text-center">#</th>
                    <th class="text-center">Time Consumed</th>
                    <th class="text-center">Moves Made</th>
                </thead>
                <tbody>
                    <?php
                        if (!empty($personal)) {
                            $i = 1;
                            foreach ($personal as $row) {
                    ?>
                    <tr>
                        <td class="text-center"><?= $i ?></td>
                        <td class="text-center"><?= $row['time'] ?></td>
                        <td class="text-center"><?= $row['no_of_moves'] ?></td>
                    </tr>

                    <?php $i++;  } } ?>
                </tbody>
            </table>

            <hr>
        </div>
        <div id="globalRankingContainer" class="collapse">
            <h4>Global Ranking</h4>

            <table class="table table-striped table-bordered">
                <thead>
                    <th class="text-center">#</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Time</th>
                    <th class="text-center">Moves</th>
                </thead>
                <tbody>
                    <?php
                        if (!empty($global)) {
                            $i = 1;
                            foreach ($global as $row) {
                    ?>
                    <tr <?= ($row['id'] == $id) ? 'class="success"' : '' ?>>
                        <td class="text-center"><?= $i ?></td>
                        <td class="text-center"><?= $row['name'] ?></td>
                        <td class="text-center"><?= $row['time_consumed'] ?></td>
                        <td class="text-center"><?= $row['move_made'] ?></td>
                    </tr>

                    <?php $i++;  } } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
