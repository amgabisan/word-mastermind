<?php
    $this->title = 'Word MasterMind';
    $rank = 1;
?>

<h1 class="text-center">
    <i class="fas fa-trophy fa-3x text-primary"></i><br>
    <?php if ($type == 'personal') {?>
        Personal Ranking
    <?php } else { ?>
        World Ranking
    <?php } ?>
</h1>

<div class="container">
    <div class="col-md-8 col-md-offset-2 col-sm-12" id="playMenuContainer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 col-md-8 item-block"  >
                  <!-- sorting menu starts -->
                 <ul class="list-inline" id="categories">
                        <li><a href="/ranking/manage/index" class="active" id="p_ranking" style="color: blue;">
                          <span class="glyphicon glyphicon-user" aria-hidden="true"></span> Personal Ranking
                        </a></li>
                        <li><a href="/ranking/manage/index/world" class="active" id="w_ranking" style="color: blue;">
                          <span class="glyphicon glyphicon-globe" aria-hidden="true"></span> World Ranking
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
        <br />
        <div class="container-fluid">
            <table id="rankingTbl" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="20%" class="text-center">Rank</th>
                        <th width="15%" class="text-center">Name</th>
                        <th class="text-center">Time</th>
                        <th class="text-center">Moves</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($rankLists as $rankList) { ?>
				    <tr>
                        <td class="text-center"><?php echo $rank; ?></td>
                        <td class="text-center"><?php echo $rankList['last_name']; ?></td>
                        <td class="text-center"><?php echo $rankList['time']; ?></td>
                        <td class="text-center"><?php echo $rankList['no_of_moves']; ?></td>
				    </tr>
				    <?php
                        $rank++;
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
