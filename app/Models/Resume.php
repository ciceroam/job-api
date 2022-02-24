<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Resume extends Model
{
    const fieldNameTranslation = [
        '_candidate_title' => 'candidate_title',
        '_candidate_email' => 'candidate_email',
        '_candidate_location' => 'candidate_location',
        '_candidate_photo' => 'candidate_photo',
        '_candidate_video' => 'candidate_video',
        '_resume_file' => 'resume_file',
        '_resume_expires' => 'resume_expires',
        '_candidate_education' => 'candidate_education',
        '_candidate_experience' => 'candidate_experience'
    ];

    /**
     * @param string|null $where
     * @return array
     */
    public static function getResumes(string $where = null): array
    {
        $sql = "SELECT ID,
                       post_title,
                       post_content,
                       post_date
                  FROM wp_site_oneposts
                  " . ($where ? " WHERE $where" : "") . "
                  ORDER BY ID";

        $resumes = DB::select($sql);

        $result = [];

        if ($resumes) {
            foreach ($resumes as $resume) {
                $result[$resume->ID] = [
                    'resume_id' => $resume->ID,
                    'resume_candidate' => $resume->post_title,
                    'resume_description' => $resume->post_content,
                    'created_at' => $resume->post_date
                ];
            }
        }

        return (array)$result;
    }

    /**
     * @param array $resume_id
     * @return array
     */
    public static function getResumeFields(array $resume_id): array
    {
        $sql = "SELECT post_id,
                       meta_key,
                       meta_value
                  FROM wp_site_onepostmeta
                 WHERE post_id IN (" . implode(",", $resume_id) . ")
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
                        if (in_array($old, ['_candidate_education', '_candidate_experience'])) {
                            $result[$post_id][$new] = unserialize($field[$old]);
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
     * @param array $resume_id
     * @return array
     */
    public static function getResumeExtraFields(array $resume_id): array
    {
        $sql = "SELECT post_id,
                       meta_key,
                       meta_value
                  FROM wp_site_onepostmeta
                 WHERE post_id IN (" . implode(",", $resume_id) . ")
                   AND meta_key in (SELECT SUBSTR(meta_key, 2)
                                      FROM wp_site_onepostmeta
                                     WHERE post_id IN (" . implode(",", $resume_id) . ")
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
