<?php

class AccountController extends Controller
{
    /**
     * Enter
     */
    public function actionIn()
    {
        $this->pageTitle = 'Enter'; // page title

        if (!Yii::app()->user->isGuest) { // if not guest
            $this->redirect(Yii::app()->request->baseUrl.'/site/error/');
            exit;
        }else{ // if guest
            $user = new Users('in');

            if(isset($_POST['Users']))
            {
                $user->attributes = $_POST['Users'];

                if($user->validate() && $user->login())
                {
                    $this->redirect(Yii::app()->request->baseUrl.'/');
                }
            }

            $this->render('in', array('form'=>$user)); // render file
        }
    }

}
