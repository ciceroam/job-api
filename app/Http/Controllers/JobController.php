<?php

namespace App\Http\Controllers;

use App\Models\Job;

class JobController extends Controller
{
    private $Job;

    public function __construct()
    {
        $this->Job = new Job();
    }

    /**
     * @OA\Get(
     *     tags={"Jobs"},
     *     summary="List jobs",
     *     description="List all resgistered jobs.",
     *     path="/jobs",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *       response="200",
     *       description="OK",
     *       @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *               example={
     *                          {
     *                            "job_id": 379,
     *                            "job_title": "Analyst",
     *                            "job_description": "System Analyst with experience",
     *                            "created_at": "2022-02-18 12:13:59",
     *                            "job_location": "Brazil",
     *                            "job_expires": "2022-03-20",
     *                            "application_email": "hr@bigcompany.com",
     *                            "company_name": "Big Company",
     *                            "company_website": "http://www.bigcompany.com.br",
     *                            "company_tagline": "",
     *                            "company_twitter": "@bigcompany",
     *                            "company_video": "",
     *                            "application_deadline": "2022-02-03",
     *                            "company_logo": "http://localhost/wp-content/uploads/2022/02/bigcompany.png",
     *                            "extra_fields": {
     *                              "experience_needed": "Yes"
     *                            }
     *                          }
     *                       }
     *         )
     *       )
     *     )
     * )
     *
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        $result = $this->Job::getJobs("post_type = 'job_listing' AND post_status = 'publish'");

        $fields = $this->Job::getJobFields(array_keys($result));

        if ($fields) {
            foreach ($fields as $post_id => $field) {
                $result[$post_id] = array_merge($result[$post_id], $field);
            }
        }

        $side_fields = $this->Job::getJobSideFields(array_keys($result));
        if ($side_fields) {
            foreach ($side_fields as $post_id => $field) {
                $result[$post_id] = array_merge($result[$post_id], $field);
            }
        }

        $extra_fields = $this->Job::getJobExtraFields(array_keys($result));

        if ($extra_fields) {
            foreach ($extra_fields as $post_id => $field) {
                $result[$post_id]['extra_fields'] = $field;
            }
        }

        return response()->json(array_values($result));
    }

    /**
     * @OA\Get(
     *     tags={"Jobs"},
     *     summary="Search job by ID",
     *     description="Gets the data of a job through an ID.",
     *     path="/jobs/{ID}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *      description="Job ID",
     *      in="path",
     *      name="ID",
     *      required=true,
     *      @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *       response="200",
     *       description="OK",
     *       @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *               example={
     *                          "job_id": 1,
     *                          "job_title": "Analyst",
     *                          "job_description": "System Analyst with experience",
     *                          "created_at": "2022-02-18 12:13:59",
     *                          "job_location": "Brazil",
     *                          "job_expires": "2022-03-20",
     *                          "application_email": "hr@bigcompany.com",
     *                          "company_name": "Big Company",
     *                          "company_website": "http://www.bigcompany.com.br",
     *                          "company_tagline": "",
     *                          "company_twitter": "@bigcompany",
     *                          "company_video": "",
     *                          "application_deadline": "2022-02-03",
     *                          "company_logo": "http://localhost/wp-content/uploads/2022/02/bigcompany.png",
     *                          "extra_fields": {
     *                            "experience_needed": "Yes"
     *                          }
     *                       }
     *         )
     *       )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     *
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->Job::getJobs("ID = " . $id);

        if (!$result) {
            abort(404);
        }

        $fields = $this->Job::getJobFields([$id]);

        if ($fields) {
            foreach ($fields as $post_id => $field) {
                $result = array_merge($result[$post_id], $field);
            }
        }

        $side_fields = $this->Job::getJobSideFields([$id]);

        if ($side_fields) {
            foreach ($side_fields as $field) {
                $result = array_merge($result, $field);
            }
        }

        $extra_fields = $this->Job::getJobExtraFields([$id]);

        if ($extra_fields) {
            foreach ($extra_fields as $field) {
                $result['extra_fields'] = $field;
            }
        }

        return response()->json($result);
    }
}
