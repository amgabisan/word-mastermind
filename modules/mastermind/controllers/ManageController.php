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
        return $this->render('game');
    }
    
    public function actionPlay()
    {
        $randomWord = $this->getRandomWord();
        
        // Save the word to be guess in session
        $session = Yii::$app->session;
        $session->open();
        $session['mastermind'] = [
            'word'  => $randomWord,
            'turns' => 10,
            'moves' => 0,
            'attempt' => []
        ];
        $session->close();

        return true;
    }
    
    public function actionCheck()
    {
        if (Yii::$app->request->isPost) {
            $result = [];
            
            $word = $guessWord = Yii::$app->request->post('word');
            $timeSpent = Yii::$app->request->post('timeSpent');
            
            $session = Yii::$app->session;
            $session->open();
            
            $mastermindSession = $session['mastermind'];
            if (in_array($word, $this->getData())) {            
                $mastermindSession['turns'] = $mastermindSession['turns'] - 1;
                $mastermindSession['moves'] = $mastermindSession['moves'] + 1;

                if (strtolower($session['mastermind']['word']) === strtolower($word)) { // Guess is correct
                    $mastermindSession['result'] = 'correct';
                    $attempt[$guessWord] = true; 
                    
                    // Save data in the database for top ranking board
                } else {
                    $randomWord = str_split($session['mastermind']['word']);
                    $word = str_split($word);

                    $arr = [];

                    for ($i = 0; $i < count($word); $i++) {
                        if (!array_key_exists($i, $arr)) {
                            $arr[$i]['letter'] = $word[$i];
                            if ($randomWord[$i] == $word[$i]) {
                                $arr[$i]['result'] = 1;
                            } else if (in_array($word[$i], $randomWord)) {
                                $letterDuplicateRandomWord  = $this->getLetterDuplicates($word[$i], $randomWord); 
                                $letterDuplicateWord = $this->getLetterDuplicates($word[$i], $word); 

                                if (count($letterDuplicateRandomWord) < count($letterDuplicateWord)) { 
                                    $commonIndexes = array_intersect($letterDuplicateRandomWord, $letterDuplicateWord); 

                                    if (!empty($commonIndexes)) {
                                        foreach ($commonIndexes as $value) {
                                            if (array_key_exists($value, $arr)) {
                                                $arr[$value]['result'] = 1;
                                            } else if ($value == $i) {
                                                $arr[$i]['result'] = 1;
                                            } else {
                                                $arr[$value]['letter'] = $word[$value];
                                                $arr[$value]['result'] = 1;
                                            }
                                        }
                                    }

                                    $diffIndexes = array_diff($letterDuplicateWord, $letterDuplicateRandomWord); 

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
    
    private function getRandomWord()
    {
        $words = $this->getData();
        
        $randOutput = $words[array_rand($words)];
        return $randOutput;
    }
    
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
