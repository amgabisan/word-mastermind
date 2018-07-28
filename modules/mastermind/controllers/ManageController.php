<?php

namespace app\modules\mastermind\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\Ranks;

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
                'only'  => ['index', 'game'],
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

    /**
    * This function renders the game page of mastermind.
    *
    */
    public function actionGame()
    {
        return $this->render('game');
    }
    
    /**
    * This function is where the server generates
    * all the session that will be used during the game.
    *
    * returns true after generating the session.
    */
    public function actionPlay()
    {
        $randomWord = $this->getRandomWord();

        $session = Yii::$app->session;
        $session->open();
        $session['mastermind'] = [
            'word'  => $randomWord, // Word to be guess
            'turns' => 10,          // Number of turns allowed
            'moves' => 0,           // Number of moves made
            'attempt' => []         // History of guesses made by the user
        ];
        $session->close();

        return true;
    }
    
    /**
    * This function gets all post event by clicking "Guess word" on the game page.
    *
    * returns json_encode(data)
    */
    public function actionCheck()
    {
        if (Yii::$app->request->isPost) {
            $result = [];
            
            $user = Yii::$app->user->identity;

            $word = $guessWord = Yii::$app->request->post('word');
            $timeSpent = Yii::$app->request->post('timeSpent');

            $session = Yii::$app->session;
            $session->open();
            
            $mastermindSession = $session['mastermind'];

            if (Yii::$app->request->post('giveUp')) {
                $correctWord = $session['mastermind']['word'];
                unset($session['mastermind']);
                $session->close();
                return json_encode($correctWord);
            }

            if (in_array($word, $this->getData())) {            
                $mastermindSession['turns'] = $mastermindSession['turns'] - 1;
                $mastermindSession['moves'] = $mastermindSession['moves'] + 1;

                if (strtolower($session['mastermind']['word']) === strtolower($word)) { // Guess is correct
                    $mastermindSession['result'] = 'correct';
                    $attempt[$guessWord] = true;
                    
                    // Save data in the database
                    $rankModel = new Ranks;
                    $rankModel->saveRanks($mastermindSession, gmdate("H:i:s", $timeSpent - 1), $user);
                } else {
                    $randomWord = str_split($session['mastermind']['word']);
                    $word = str_split($word);

                    $arr = [];

                    for ($i = 0; $i < count($word); $i++) {
                        if (!array_key_exists($i, $arr)) { // If letter is not yet in the array so that the letter won't be overridden
                            $arr[$i]['letter'] = $word[$i];
                            if ($randomWord[$i] == $word[$i]) { // if currrent letter in the word is equal to the current letter in the guess word
                                $arr[$i]['result'] = 1;
                            } else if (in_array($word[$i], $randomWord)) { // if letter in the guess word is present in the word
                                $letterDuplicateRandomWord  = $this->getLetterDuplicates($word[$i], $randomWord); 
                                $letterDuplicateWord = $this->getLetterDuplicates($word[$i], $word); 

                                // If duplicates / repeatable letters in the guess word is more than the duplicates in the word (to be guess)
                                if (count($letterDuplicateRandomWord) < count($letterDuplicateWord)) {
                                    $commonIndexes = array_intersect($letterDuplicateRandomWord, $letterDuplicateWord); // get the common indexes of the duplicates which is considered correct letter in correct position

                                    if (!empty($commonIndexes)) {
                                        foreach ($commonIndexes as $value) {
                                            // If the index is already present in the array, value will be overriden
                                            if (array_key_exists($value, $arr)) {
                                                $arr[$value]['result'] = 1;
                                            } else if ($value == $i) { // if the index is the same as the current index in the loop
                                                $arr[$i]['result'] = 1;
                                            } else { // if the index does not yet exist
                                                $arr[$value]['letter'] = $word[$value];
                                                $arr[$value]['result'] = 1;
                                            }
                                        }
                                    }

                                    $diffIndexes = array_diff($letterDuplicateWord, $letterDuplicateRandomWord); // get the different indexes of the guess word to the word to be guess

                                    if (!empty($diffIndexes)) {
                                        foreach ($diffIndexes as $value) {
                                            if (array_key_exists($value, $arr)) {
                                                $arr[$value]['result'] = 0;
                                            } else if ($value == $i) {
                                                $arr[$i]['result'] = 0;
                                            } else {
                                                $arr[$value]['letter'] = $word[$value];
                                                $arr[$value]['result'] = 0;
                                            }
                                        }
                                    }

                                    /* if there is no common but there are differences
                                     * Word to be guess: Ravel [1]
                                     * Guess word: Abaca [0,2,4]
                                     * Current Letter evaluated: A
                                     * Assign the other letters with correct letter but not in the right position, if it reach the number of
                                     * duplicates in the word to be guess, others will stay as incorrect letter
                                    */
                                    if (empty($commonIndexes) && !empty($diffIndexes)) {
                                        $count = count($letterDuplicateRandomWord);
                                        $j = 0;
                                        foreach ($diffIndexes as $value) {
                                            if ($count != $j) {
                                                $arr[$value]['result'] = 2;
                                                $j++;
                                            }
                                        }
                                    }
                                } else { 
                                    $arr[$i]['result'] = 2;
                                }
                            } else {
                                $arr[$i]['result'] = 0;
                            }
                        }
                    }
                    
                    ksort($arr);
                    $attempt[$guessWord] = $mastermindSession['result'] = array_column($arr, 'result');
                }        
            } else {
                $mastermindSession['result'] = 'invalid'; 
                $attempt[$guessWord] = false; 
            }
            
            // If the guess word has already been guessed and the result is not invalid. Add value to turns and remove one to moves
            if (array_key_exists($guessWord, $mastermindSession['attempt']) && $mastermindSession['result'] != 'invalid') {
                $mastermindSession['turns'] = $mastermindSession['turns'] + 1;
                $mastermindSession['moves'] = $mastermindSession['moves'] - 1;
            }

            $mastermindSession['attempt'] = $mastermindSession['attempt'] + $attempt;
            $session['mastermind'] = $mastermindSession;
            $result = $mastermindSession;
            
            if ($mastermindSession['turns'] != 0) {
                unset($result['word']);
            }
            
            $session->close();
            
            return json_encode($result);
        }
    }
    
    private function getLetterDuplicates($letter, $word)
    {
        $arr = [];
        $startIndex = array_search($letter, $word);
        
        for ($i = $startIndex; $i < count($word); $i++) {
            if ($word[$i] == $letter) {
                array_push($arr, $i);
            }
        }
        
        return $arr;
    }
    
    /**
    * This function gets a random word from the dictionary that contains only 5 letters.
    *
    * returns $randOutput
    */
    private function getRandomWord()
    {
        $words = $this->getData();
        
        $randOutput = $words[array_rand($words)];
        return $randOutput;
    }
    
    /**
    * This function gets all the data from the dictionary that contains 5 letters.
    *
    * returns $outputArr
    */
    private function getData()
    {
        $file = Yii::$app->basePath . '/web/resources/files/dictionary.txt';
        $file_open = fopen($file, 'r');
  
        //For read uploaded file and process
        $file_read = fread($file_open,filesize($file));
 
        fclose($file_open);
 
        $newline_ele = "\n";
        $data_split = explode($newline_ele, $file_read);
        
        $outputArr = array();
        foreach ($data_split as $ds) {
            if (strlen($ds) == 5) {
                $outputArr[] = strtolower($ds);
            }
        } 
        
        return $outputArr;
    }
}
