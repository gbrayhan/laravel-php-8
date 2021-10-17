<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Person;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class PersonController extends Controller {
    public function __construct() {
        $this->middleware('check.jwt');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection|Person[]
     */
    public function showAll(): Collection|array {
        return Person::all();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'phone' => 'required|numeric',
            'curp' => 'required|max:255',
            'address' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $person = Person::create($validator->validated());

        return response()->json([
            'message' => 'Person created successfully',
            'persona' => $person
        ], 201);
    }


    /**
     * Associate person to a User.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function associatePerson(Request $request): JsonResponse {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'person_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
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
     */
    public function update(Request $request, int $id): JsonResponse {
        $validator = Validator::make($request->all(), [
            'phone' => 'sometimes|required|numeric',
            'address' => 'sometimes|required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id): Response {
        $person = Person::findOrFail($id);
        $person->delete();

        return redirect('/person')->with('completed', 'Person has been deleted');
    }
}