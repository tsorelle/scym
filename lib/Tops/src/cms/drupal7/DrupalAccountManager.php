<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 7/10/2015
 * Time: 9:59 AM
 */

namespace Tops\cms\drupal7;


class DrupalAccountManager
{
    private static function createFieldArray($value)
    {
        $result = Array('und' => Array());
        $result['und'][0] = Array(
                'value' => $value,
                'safe_value' => $value
            );

        return $result;

    }
    public static function CreateAccount($username,$email,$password,$fullname,$meeting,$sendmail=false) {
        $result = new \stdClass();
        $result->success = false;
        $result->message = '';
        $result->exception = null;
        $result->account = null;
        try {
            $existing = user_load_by_name($username);
            if ($existing) {
                $result->message = "Failed to add $fullname. Account ($username) already exists.";
                return $result;
            }

            $existing = user_load_by_mail($email);
            if ($existing) {
                $result->message = "Failed to add $fullname. Another user has email address: $email";
                return $result;
            }

            $newAccount = new \stdClass();
            $newAccount->is_new = true;



            // add user here
            //set up the user fields
            $fields = array(
                'name' => $username,
                'mail' => $email,
                'pass' => $password,
                'status' => 1,
                'init' => 'email address',
                'roles' => array(
                    DRUPAL_AUTHENTICATED_RID => 'authenticated user',
                ),
                'field_full_name' => self::createFieldArray($fullname),
                'field_friends_meeting' => self::createFieldArray($meeting)
            );

            $newuser = user_save(null,$fields);
            if ($newuser == false) {
                $result->message = "Failed to add $fullname ($username,$email). Unknown Drupal error.";
                return $result;
            }
            $result->message = "Added $fullname ($username,$email)";
            $result->success = true;
        }
        catch(\Exception $ex) {
            $result->message = "Exception occurred";
            $result->exception = $ex;
            $result->success = false;
        }
        return $result;
    }
}