<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    private $JobApplication;

    public function __construct()
    {
        $this->JobApplication = new JobApplication();
    }

    /**
     * @OA\Get(
     *     tags={"Job Applications"},
     *     summary="List job applications",
     *     description="This route list all registered job applications.",
     *     path="/applications",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *       response="200",
     *       description="OK",
     *       @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *               example={
     *                          "resume_id": 1,
     *                          "resume_candidate": "Cicero Augusto",
     *                          "resume_description": "System Analyst with experience"
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
        $result = $this->JobApplication::getJobApplications("post_type = 'job_application' AND post_status IN ('new', 'publish')");

        if ($result) {
            $fields = $this->JobApplication::getJobApplicationFields(array_keys($result));

            if ($fields) {
                foreach ($fields as $post_id => $field) {
                    $result[$post_id] = array_merge($result[$post_id], $field);
                }
            }

            $extra_fields = $this->JobApplication::getJobApplicationExtraFields(array_keys($result));

            if ($extra_fields) {
                foreach ($extra_fields as $post_id => $field) {
                    $result[$post_id]['extra_fields'] = $field;
                }
            }
        }

        return response()->json(array_values($result));
    }

    /**
     * @OA\Get(
     *     tags={"Job Applications"},
     *     summary="Search job application by job id",
     *     description="This route gets the data of a job application through a job id.",
     *     path="/applications/job/{id}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *      description="Job ID",
     *      in="path",
     *      name="ID",
     *      required=true,
     *      @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *       response="200",
     *       description="OK",
     *       @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *               example={
     *                          "resume_id": 1,
     *                          "resume_candidate": "Cicero Augusto",
     *                          "resume_description": "System Analyst with experience"
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
    public function showByJobId(int $id)
    {
        $result = $this->JobApplication::getJobApplications("post_type = 'job_application' AND post_parent = " . $id);

        if (!$result) {
            abort(404);
        }

        $application_id = array_keys($result);

        $fields = $this->JobApplication::getJobApplicationFields($application_id);

        if ($fields) {
            foreach ($fields as $post_id => $field) {
                $result[$post_id] = array_merge($result[$post_id], $field);
            }
        }

        $extra_fields = $this->JobApplication::getJobApplicationExtraFields($application_id);

        if ($extra_fields) {
            foreach ($extra_fields as $post_id => $field) {
                $result[$post_id]['extra_fields'] = $field;
            }
        }

        return response()->json(array_values($result));
    }

    /**
     * @OA\Get(
     *     tags={"Job Applications"},
     *     summary="Search job application by candidate name",
     *     description="This route gets the data of a job application through a candidate name.",
     *     path="/applications/candidate/{name}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *      description="JobApplication Candidate Name",
     *      in="path",
     *      name="Name",
     *      required=true,
     *      @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *       response="200",
     *       description="OK",
     *       @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *               example={
     *                          "resume_id": 1,
     *                          "resume_candidate": "Cicero Augusto",
     *                          "resume_description": "System Analyst with experience"
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
     * @param string $name
     * @return \Illuminate\Http\Response
     */
    public function showByCandidateName(string $name)
    {
        $result = $this->JobApplication::getJobApplications("post_type = 'job_application' AND post_title LIKE '%" . $name . "%'");

        if (!$result) {
            abort(404);
        }

        $application_id = array_keys($result);

        $fields = $this->JobApplication::getJobApplicationFields($application_id);

        if ($fields) {
            foreach ($fields as $post_id => $field) {
                $result[$post_id] = array_merge($result[$post_id], $field);
            }
        }

        $extra_fields = $this->JobApplication::getJobApplicationExtraFields($application_id);

        if ($extra_fields) {
            foreach ($extra_fields as $post_id => $field) {
                $result[$post_id]['extra_fields'] = $field;
            }
        }

        return response()->json(array_values($result));
    }
}
