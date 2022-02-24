<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Job extends Model
{
    const fieldNameTranslation = [
        '_job_location' => 'job_location',
        '_job_expires' => 'job_expires',
        '_application' => 'application_email',
        '_company_name' => 'company_name',
        '_company_website' => 'company_website',
        '_company_tagline' => 'company_description',
        '_company_twitter' => 'company_twitter',
        '_company_video' => 'company_video',
        '_application_deadline' => 'application_deadline'
    ];

    const sideFieldNameTranslation = [
        '_wp_attached_file' => 'company_logo'
    ];

    /**
     * @param string|null $where
     * @return array
     */
    public static function getJobs(string $where = null): array
    {
        $sql = "SELECT ID,
                       post_title,
                       post_content,
                       post_date
                  FROM wp_site_oneposts
                  " . ($where ? " WHERE $where" : "") . "
                  ORDER BY ID";

        $jobs = DB::select($sql);

        $result = [];

        if ($jobs) {
            foreach ($jobs as $job) {
                $result[$job->ID] = [
                    'job_id' => $job->ID,
                    'job_title' => $job->post_title,
                    'job_description' => $job->post_content,
                    'created_at' => $job->post_date
                ];
            }
        }

        return (array)$result;
    }

    /**
     * @param array $job_id
     * @return array
     */
    public static function getJobFields(array $job_id): array
    {
        $sql = "SELECT post_id,
                       meta_key,
                       meta_value
                  FROM wp_site_onepostmeta
                 WHERE post_id IN (" . implode(",", $job_id) . ")
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
     * @param array $job_id
     * @return array
     */
    public static function getJobSideFields(array $job_id): array
    {
        $sql = "SELECT wp_site_oneposts.post_parent,
                       wp_site_onepostmeta.meta_key,
                       wp_site_onepostmeta.meta_value
                  FROM wp_site_onepostmeta
                  JOIN wp_site_oneposts ON (wp_site_oneposts.ID = wp_site_onepostmeta.post_id)
                 WHERE wp_site_oneposts.post_parent IN (" . implode(',', $job_id) . ")
                   AND wp_site_onepostmeta.meta_key IN ('" . implode("','", array_keys(self::sideFieldNameTranslation)) . "')
              ORDER BY post_id";

        $fields = DB::select($sql);

        $result = [];

        if ($fields) {
            foreach ($fields as $field) {
                $fields_organized[$field->post_parent][$field->meta_key] = $field->meta_value;
            }

            foreach ($fields_organized as $post_id => $field) {
                foreach (self::sideFieldNameTranslation as $old => $new) {
                    if (array_key_exists($old, $field)) {
                        if ($old == '_wp_attached_file') {
                            $result[$post_id][$new] = env('URL_WEBSITE') . '/wp-content/uploads/' . $field[$old];
                        } else {
                            $result[$post_id][$new] = $field[$old];
                        }
                    } else {
                        $result[$post_id][$new] = "";
                    }
                }
            }
        }

        return (array)$result;
    }

    /**
     * @param array $job_id
     * @return array
     */
    public static function getJobExtraFields(array $job_id): array
    {
        $sql = "SELECT post_id,
                       meta_key,
                       meta_value
                  FROM wp_site_onepostmeta
                 WHERE post_id IN (" . implode(",", $job_id) . ")
                   AND meta_key in (SELECT SUBSTR(meta_key, 2)
                                      FROM wp_site_onepostmeta
                                     WHERE post_id IN (" . implode(",", $job_id) . ")
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
