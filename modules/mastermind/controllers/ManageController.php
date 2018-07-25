<?php

namespace app\modules\mastermind\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class ManageController extends Controller
{
    public $layout = '/main';
    public $viewPath = 'app/modules/mastermind/views';
    /**;
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                // Pages that are included in the rule set
                'only'  => ['index'],
                'rules' => [
                    [ // Pages that can be accessed when logged in
                        'allow'     => true,
                        'actions'   => [ 'index', 'game'],
                        'roles'     => ['@']
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    // Action when user is denied from accessing a page
                    if (Yii::$app->user->isGuest) {
                        $this->goHome();
                    } else {
                        $this->redirect(['/dashboard']);
                    }
                }
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGame()
    {
        $blackScore = 0;
        $diff = 5;
        $_SESSION['difficulty'] = $diff;
        $_SESSION['round'] = 1;
        $_SESSION['target'] = array(rand(1,$diff),rand(1,$diff),rand(1,$diff),rand(1,$diff));
        $_SESSION['attempts'] = array();
        $_SESSION['scores'] = array();
        $message = $this->addMessage('A number has been chosen ...');
        $legend = 'Round 1: Your first attempt';
        $html = "";

        if(isset($_POST['attempt']))
        {
            //get input attempt, trimmed of whitespace
            $attempt = trim($_POST['attempt']);
        } else {
            $attempt = "";
        }

        $round = $_SESSION['round'];
        $showTable = false;

        if(isset($_POST['quit']))
        {
            //quit message //add spaces to number for better reader intonation
            $this->addMessage('Give up? Oh well ... the number was ' . implode(' ', $_SESSION['target']) . '.');

            //if this isn't the first round
            if ($round > 1)
            {
                //show the table
                $showTable = true;
            }
        } else if(preg_match('/^[0-' . $_SESSION['difficulty'] . ']{4}$/',$attempt) && ($round == 1 || $attempt != $_SESSION['attempts'][($round-1)])) {
            //add this attempt to array
            $_SESSION['attempts'][$round] = $attempt;

            //create new score for this attempt array
            $_SESSION['scores'][$round] = array('black' => 0, 'white' => 0);

            //remember which digits and attempt characters have been marked
            $digitsMarked = array(0,0,0,0);
            $charsMarked = array(0,0,0,0);

            //for each digit in the target number
            foreach($_SESSION['target'] as $key => $digit)
            {
                //if this digit matches the same digit in the attempt
                if($digit == substr($attempt, $key, 1))
                {
                    //add one to black score
                    $_SESSION['scores'][$round]['black']++;

                    //remember this digit has been marked
                    $digitsMarked[$key] = 1;

                    //remember this character has been marked
                    $charsMarked[$key] = 1;

                    //add to black score
                    $blackScore ++;
                }

            }

            //now loop again
            //we have to do it this way rather than if/else
            //so that all characters have a chance
            //to be marked black before being assessed for white
            //otherwise you could score a white
            //for a character which is later marked black
            foreach($_SESSION['target'] as $key => $digit)
            {
                //for each character in the attempt string
                for($i=0; $i<strlen($attempt); $i++)
                {
                    //if this character matches the digit
                    if(substr($attempt, $i, 1) == $digit)
                    {
                        //if this digit and this character have not already been marked
                        if(!$charsMarked[$i] && !$digitsMarked[$key])
                        {
                            //add one to white score
                            $_SESSION['scores'][$round]['white']++;

                            //remember this digit has been marked
                            $digitsMarked[$key] = 1;

                            //remember this character has been marked
                            $charsMarked[$i] = 1;

                            //break to stop repeats being counted again
                            break;
                        }
                    }
                }
            }

            //show the table
            $showTable = true;

            //create form legend
            $legend = 'Round ' . ($round + 1) . ': Your next attempt';

            //increment round number
            $_SESSION['round']++;
        } else if(isset($_SESSION['target'])) {
		//if this isn't discounted with get value
            if(!isset($_GET['count']))
            {
                //if input was not a valid number
                if(!preg_match('/^[0-' . $_SESSION['difficulty'] . ']{4}$/',$attempt))
                {
                    //invalid input message
                    $this->addMessage('That was not a valid attempt - please enter four digits between 0 and ' . $_SESSION['difficulty'] . '.');
                }

                //else if it was the same as last time
                else if($attempt == $_SESSION['attempts'][($round-1)])
                {
                    //say so
                    $this->addMessage('That\'s what you entered last time - please try a different number.');
                }

            }

            //create form legend
            $legend = 'Round ' . ($round) . ': Your next attempt';

            //if this isn't the first round
            if($round > 1)
            {
                //show the table
                $showTable = true;
            }
        }

        if($blackScore == 4)
        {
            //victory message //add spaces to number for better reader intonation
            $this->addMessage('Fantastic! ' . implode(' ', $_SESSION['target']). ' is the right number, and you got there in ' . $round . ' attempt' . (($round > 1) ? 's' : '') . '.');
            $this->addMessage('<img src=\'resources/trophy.gif\' id=\'trophy\' width=\'128\' height=\'128\' alt=\'Congratulations!\' title=\'Congratulations!\' />');

            //unset target value so that if you press refresh from here
            //it can recognise that and put up a new game form
            //instead of an invalid input / new round form
            unset($_SESSION['target']);
        }

        //if we're showing the table
        if($showTable)
        {

            //number of previous attempts
            $last = count($_SESSION['attempts']);

            //start compiling last attempt table
            $html = ""
                . "<table cellpadding='0' cellspacing='1' border='1' summary='This table charts your last attempt'>\n\n"
                . "\t\t\t<caption>Your last attempt:</caption>\n\n";

            //add headers
            $html .= $this->addTableHeaders();

            //add a table row for the last attempt
            $html .= $this->addTableRow($last,$_SESSION['attempts'][$last]);

            //finish compiling last attempt table
            $html .= "\t\t\t</tbody>\n\n"
            . "\t\t</table>\n\n";

            //if there's more than one previous attempt
            if($last > 1)
            {

                //jump to next attempt link
                $html .= "<p class='nextAttempt'><a href='#next-attempt' title='Jump to next attempt form'>Next attempt</a></p>\n\n";

                //start compiling previous attempts table
                $html .= ""
                    . "<table cellpadding='0' cellspacing='1' border='1' summary='This table charts all your previous attempts'>\n\n"
                    . "\t\t\t<caption>All your previous attempts (in reverse order):</caption>\n\n";

                //add headers
                $html .= $this->addTableHeaders();

                //for each attempt in the array, in reverse order
                for($i=$last; $i>0; $i--)
                {
                    //add a table row
                    $html .= $this->addTableRow($i,$_SESSION['attempts'][$i]);
                }

                //finish compiling previous attempts table
                $html .= "\t\t\t</tbody>\n\n"
                . "\t\t</table>\n\n";

            }
        }

        return $this->render('game', [
            'message' => $message,
            'legend' => $legend,
            'html' => $html
        ]);
    }

        private function addMessage($text)
    {
        $message = "<p id='message'>$text</p>\n";
        return $message;
    }

    //add headers to score table
    private function addTableHeaders()
    {
        $html = ""
        . "\t\t\t<thead>\n"
            . "\t\t\t\t<tr>\n"
            . "\t\t\t\t\t<th scope='col'>Round.</th>\n"
            . "\t\t\t\t\t<th scope='col'>Number.</th>\n"
            . "\t\t\t\t\t<th scope='col'>Mark</th>\n"
            . "\t\t\t\t</tr>\n"
        . "\t\t\t</thead>\n\n"
        . "\t\t\t<tbody>\n\n";

        return $html;
    }

    private function addTableRow($key,$data)
    {
        //track total score
        $total = 0;

        //open table row //add spaces to number for better reader intonation
        $html = ""
        . "\t\t\t\t<tr>\n"
            . "\t\t\t\t\t<td scope='row'><span class='offleft'>Round </span>" . $key . ".</td>\n"
            . "\t\t\t\t\t<td>" . preg_replace("([0-9])","\\0",$data) . "</td>\n"
            . "\t\t\t\t\t<td>";

        //add black scores for this row
        for($j=0; $j<$_SESSION['scores'][$key]['black']; $j++)
        {
            $html .= "<img src='resources/black.gif' class='mark black' alt='Black ' title='Black: the right number in the right place' />";

            //increase total
            $total++;
        }

        //add white scores for this row
        for($j=0; $j<$_SESSION['scores'][$key]['white']; $j++)
        {
            $html .= "<img src='resources/white.gif' class='mark white' alt='White ' title='White: the right number but in the wrong place' />";

            //increase total
            $total++;
        }

        //if total score is zero
        if($total == 0)
        {
            //add "no score" message
            $html .= 'No score';
        }

        //close table row
        $html .= "</td>\n"
        . "\t\t\t\t</tr>\n";

        return $html;
    }
}
