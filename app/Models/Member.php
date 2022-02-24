<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Member extends Model
{
    const fieldNameTranslation = [
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'birth_date_user' => 'birth_date',
        'country_user' => 'country',
    ];

    /**
     * @param string|null $where
     * @return array
     */
    public static function getMembers(string $where = null): array
    {
        $sql = "SELECT ID,
                       display_name,
                       user_login,
                       user_pass,
                       user_email,
                       user_registered
                  FROM wp_site_oneusers
                  " . ($where ? " WHERE $where" : "") . "
                  ORDER BY ID";

        $users = DB::select($sql);

        $result = [];

        if ($users) {
            foreach ($users as $user) {
                $result[$user->ID] = [
                    'member_id' => $user->ID,
                    'member_name' => $user->display_name,
                    'member_login' => $user->user_login,
                    'member_password' => $user->user_pass,
                    'member_email' => $user->user_email,
                    'created_at' => $user->user_registered
                ];
            }
        }

        return (array)$result;
    }

    /**
     * @param array $member_id
     * @return array
     */
    public static function getMemberFields(array $member_id): array
    {
        $sql = "SELECT user_id,
                       meta_key,
                       meta_value
                  FROM wp_site_oneusermeta
                 WHERE user_id IN (" . implode(",", $member_id) . ")
                   AND meta_key IN ('" . implode("','", array_keys(self::fieldNameTranslation)) . "')
              ORDER BY user_id";

        $fields = DB::select($sql);

        $result = [];

        if ($fields) {
            foreach ($fields as $field) {
                $fields_organized[$field->user_id][$field->meta_key] = $field->meta_value;
            }

            foreach ($fields_organized as $user_id => $field) {
                foreach (self::fieldNameTranslation as $old => $new) {
                    if (array_key_exists($old, $field)) {
                        $result[$user_id][$new] = $field[$old];
                    } else {
                        $result[$user_id][$new] = "";
                    }
                }
            }
        }

        return (array)$result;
    }
}
