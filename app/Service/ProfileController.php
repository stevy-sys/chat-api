<?php

class ProfileController
{
    public function myProfile($user)  {
        return $user->load('profile');
    }

    public function updateProfile($auth,$data){
        $auth->profile()->update($data);

        //traitement image
        return $data ;
    }
}
