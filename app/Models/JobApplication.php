<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JobApplication extends Model
{
    const fieldNameTranslation = [
        '_candidate_email' => 'candidate_email',
        '_rating' => 'application_rating',
        '_resume_id' => 'resume_id'
    ];

    /**
     * @param string|null $where
     * @return array
     */
    public static function getJobApplications(string $where = null): array
    {
        $sql = "SELECT ID,
                       post_title,
                       post_content,
                       post_status,
                       post_date,
                       post_parent
                  FROM wp_site_oneposts
                  " . ($where ? " WHERE $where" : "") . "
                  ORDER BY ID";

        $applications = DB::select($sql);

        $result = [];

        if ($applications) {
            foreach ($applications as $application) {
                $result[$application->ID] = [
                    'application_id' => $application->ID,
                    'candidate_name' => $application->post_title,
                    'application_description' => $application->post_content,
                    'application_status' => $application->post_status,
                    'created_at' => $application->post_date,
                    'job_id' => $application->post_parent
                ];
            }
        }

        return (array)$result;
    }

    /**
     * @param array $application_id
     * @return array
     */
    public static function getJobApplicationFields(array $application_id): array
    {
        $sql = "SELECT post_id,
                       meta_key,
                       meta_value
                  FROM wp_site_onepostmeta
                 WHERE post_id IN (" . implode(",", $application_id) . ")
                   AND meta_key IN ('" . implode("','", array_keys(self::fieldNameTranslation)) . "')
              ORDER BY post_id";

        $fields = DB::select($sql);

        $result = [];

        if ($fields) {
            foreach ($fields as $field) {
                $fields_organized[$field->post_id][$field->meta_key] = $field->meta_value;
            }

            foreach ($fields_organized as $post_id => $field) {
                foreach (self::fieldNameTranslation as $old => $new) {
                    if (array_key_exists($old, $field)) {
                        $result[$post_id][$new] = $field[$old];
                    } else {
                        $result[$post_id][$new] = "";
                    }
                }
            }
        }

        return (array)$result;
    }

    /**
     * @param array $application_id
     * @return array
     */
    public static function getJobApplicationExtraFields(array $application_id): array
    {
        $sql = "SELECT post_id,
                       meta_key,
                       meta_value
                  FROM wp_site_onepostmeta
                 WHERE post_id IN (" . implode(",", $application_id) . ")
                   AND meta_key in (SELECT SUBSTR(meta_key, 2)
                                      FROM wp_site_onepostmeta
                                     WHERE post_id IN (" . implode(",", $application_id) . ")
                                       AND meta_value like 'field_%')
              ORDER BY post_id";

        $fields = DB::select($sql);

        $result = [];

        if ($fields) {
            foreach ($fields as $field) {
                $result[$field->post_id][$field->meta_key] = $field->meta_value;
            }
        }

        return (array)$result;
    }
}
