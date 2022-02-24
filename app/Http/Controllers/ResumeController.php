<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Http\Request;

class ResumeController extends Controller
{
    private $Resume;

    public function __construct()
    {
        $this->Resume = new Resume();
    }

    /**
     * @OA\Get(
     *     tags={"Resumes"},
     *     summary="List resumes",
     *     description="List all resgistered resumes.",
     *     path="/resumes",
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
        $result = $this->Resume::getResumes("post_type = 'resume' AND post_status = 'publish'");

        if ($result) {
            $fields = $this->Resume::getResumeFields(array_keys($result));

            if ($fields) {
                foreach ($fields as $post_id => $field) {
                    $result[$post_id] = array_merge($result[$post_id], $field);
                }
            }
        }

        return response()->json(array_values($result));
    }

    /**
     * @OA\Get(
     *     tags={"Resumes"},
     *     summary="Search resume by candidate name",
     *     description="Gets the data of a resume through a candidate name.",
     *     path="/resumes/name/{name}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *      description="Resume Name",
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
    public function showByName($name)
    {
        $result = $this->Resume::getResumes("post_type = 'resume' AND post_title LIKE '%" . $name . "%'");

        if (!$result) {
            abort(404);
        }

        $resume_id = array_keys($result);

        $fields = $this->Resume::getResumeFields($resume_id);

        if ($fields) {
            foreach ($fields as $post_id => $field) {
                $result[$post_id] = array_merge($result[$post_id], $field);
            }
        }

        $extra_fields = $this->Resume::getResumeExtraFields($resume_id);

        if ($extra_fields) {
            foreach ($extra_fields as $post_id => $field) {
                $result[$post_id]['extra_fields'] = $field;
            }
        }

        return response()->json(array_values($result));
    }
}
