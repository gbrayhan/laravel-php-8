<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Person;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class PersonController extends Controller {
    public function __construct() {
        $this->middleware('check.jwt', ['except' => ['store']]);
    }

    /**
     * @OA\Get(
     *      path="/person",
     *      operationId="getProjectsList",
     *      tags={"Person"},
     *      summary="Get list of persons",
     *      description="Returns list of persons",
     *      security={{"bearer_token":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function showAll(): Collection|array {
        return Person::all();
    }


    /**
     * @OA\Post(
     *      path="/person",
     *      operationId="storePerson",
     *      tags={"Person"},
     *      summary="Register Person",
     *      description="Register Person",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      description="name",
     *                      example="Alex"
     *                  ),
     *                  @OA\Property(
     *                      property="last_name",
     *                      type="string",
     *                      description="last name",
     *                      example="Guerrero"
     *                  ),
     *                  @OA\Property(
     *                      property="phone",
     *                      type="string",
     *                      description="phone",
     *                      example="+554422331122"
     *                  ),
     *                  @OA\Property(
     *                      property="curp",
     *                      type="string",
     *                      description="CURP",
     *                      example="GGAA887777HDFBRR01"
     *                  ),
     *                  @OA\Property(
     *                      property="address",
     *                      type="string",
     *                      description="Address",
     *                      example="Av Elementia, Zapopan Jalisco, Mexico"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Person created successfully"),
     *              @OA\Property(property="person", type="object", ref="#/components/schemas/Person")
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *   )
     *
     */
    public function store(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'phone' => 'required|numeric',
            'curp' => 'required|max:255|unique:people',
            'address' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        $person = Person::create($validator->validated());

        return response()->json([
            'message' => 'Person created successfully',
            'person' => $person
        ], 201);
    }


    /**
     * @OA\Post(
     *      path="/person/associate-person",
     *      operationId="associatePerson",
     *      tags={"Person"},
     *      summary="Associate Person to the current user",
     *      description="ssociate Person to the current user",
     *      security={{"bearer_token":{}}},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="person_id",
     *                      type="integer",
     *                      description="Person Identifier",
     *                      example="1"
     *                  ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Person successfully associated"),
     *              @OA\Property(property="person", type="object", ref="#/components/schemas/Person")
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *   )
     *
     */
    public function associatePerson(Request $request): JsonResponse {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'person_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        $person = Person::where('id', $validator->validated()["person_id"])->update(['user_id' => $user->id]);

        if ($person === 0) {
            return response()->json([
                'message' => 'Person not associated, please check your ID',
            ], 400);
        }


        return response()->json([
            'message' => 'Person successfully associated',
            'person' => Person::find($validator->validated()["person_id"]),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function showByID(int $id): JsonResponse {
        $person = Person::where('id', $id)->first();

        if ($person === null) {
            return response()->json([
                'message' => 'Person not found, please check your ID',
            ], 400);
        }

        return response()->json([
            'person' => Person::where('id', $id)->first()
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse {
        $validator = Validator::make($request->all(), [
            'phone' => 'sometimes|required|numeric',
            'address' => 'sometimes|required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        $updated = Person::whereId($id)->update($validator->validated());

        if ($updated === 0) {
            return response()->json([
                'message' => 'Person not found, please check your ID',
            ], 400);
        }

        return response()->json([
            'message' => 'Person successfully updated',
            'person' => Person::find($id),
        ]);

    }

}