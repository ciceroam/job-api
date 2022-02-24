<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    private $Member;

    public function __construct()
    {
        $this->Member = new Member();
    }

    /**
     * @OA\Get(
     *     tags={"Members"},
     *     summary="List members",
     *     description="List all resgistered members.",
     *     path="/members",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *       response="200",
     *       description="OK",
     *       @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *               example={
     *                          "member_id": 1,
     *                          "member_name": "Cicero Augusto",
     *                          "member_email": "ciceroam@gmail.com"
     *                       }
     *         )
     *       )
     *     )
     * )
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->Member::getMembers();

        if ($result) {
            $fields = $this->Member::getMemberFields(array_keys($result));

            if ($fields) {
                foreach ($fields as $user_id => $field) {
                    $result[$user_id] = array_merge($result[$user_id], $field);
                }
            }
        }

        return response()->json(array_values($result));
    }

    /**
     * @OA\Get(
     *     tags={"Members"},
     *     summary="Search member by email",
     *     description="Gets the data of a member through an email.",
     *     path="/members/email/{email}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *       response="200",
     *       description="OK",
     *       @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *               example={
     *                          "member_id": 1,
     *                          "member_name": "Cicero Augusto",
     *                          "member_email": "ciceroam@gmail.com"
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
     * Display the specified resource by email.
     *
     * @param string $email
     * @return \Illuminate\Http\Response
     */
    public function showByEmail($email)
    {
        $result = $this->Member::getMembers("user_email = '$email'");

        if (!$result) {
            abort(404);
        }

        if ($result) {
            $fields = $this->Member::getMemberFields(array_keys($result));

            if ($fields) {
                foreach ($fields as $user_id => $field) {
                    $result[$user_id] = array_merge($result[$user_id], $field);
                }
            }
        }

        return response()->json(array_values($result));
    }

    /**
     * @OA\Get(
     *     tags={"Members"},
     *     summary="List members with no applications",
     *     description="List all resgistered members with no job applications.",
     *     path="/members/noapplication",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *       response="200",
     *       description="OK",
     *       @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *               example={
     *                          "member_id": 1,
     *                          "member_name": "Cicero Augusto",
     *                          "member_email": "ciceroam@gmail.com"
     *                       }
     *         )
     *       )
     *     )
     * )
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showNotGetApplication()
    {
        $result = $this->Member::getMembers("
          user_email NOT IN (SELECT meta_value
                               FROM wp_site_onepostmeta
                               JOIN wp_site_oneposts ON wp_site_oneposts.ID = wp_site_onepostmeta.post_id
                               WHERE post_type = 'job_application' AND meta_key = '_candidate_email')
        ");

        if ($result) {
            $fields = $this->Member::getMemberFields(array_keys($result));

            if ($fields) {
                foreach ($fields as $user_id => $field) {
                    $result[$user_id] = array_merge($result[$user_id], $field);
                }
            }
        }

        return response()->json(array_values($result));
    }

}
