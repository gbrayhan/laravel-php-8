<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller {
    public function __construct() {
        $this->middleware('check.jwt');
    }

    /**
     * @OA\Post(
     *      path="/account",
     *      operationId="accountStore",
     *      tags={"Account"},
     *      summary="Register Account",
     *      description="Register Account",
     *      security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="account_number",
     *                      type="string",
     *                      description="Account Number",
     *                      example="222A12311"
     *                  ),
     *                  @OA\Property(
     *                      property="person_id",
     *                      type="integer",
     *                      description="Person ID",
     *                      example="12"
     *                  ),
     *                  @OA\Property(
     *                      property="product",
     *                      type="string",
     *                      description="Product",
     *                      example="bbva-credit"
     *                  ),
     *                  @OA\Property(
     *                      property="balance",
     *                      type="decimal",
     *                      description="Balance",
     *                      example="130.2"
     *                  ),
     *                  @OA\Property(
     *                      property="nip",
     *                      type="integer",
     *                      description="NIP",
     *                      example="112334"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Account created successfully"),
     *              @OA\Property(property="account", type="object", ref="#/components/schemas/Account"),
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function store(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|max:100|unique:accounts',
            'person_id' => 'required|numeric',
            'product' => 'required|max:30',
            'balance' => 'required|numeric',
            'nip' => 'required|max:30',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        $userId = $request->input('is_user_account') ? auth()->user()->id : null;

        $account = Account::create([
            'account_number' => $request->input('account_number'),
            'person_id' => $request->input('person_id'),
            'user_id' => $userId,
            'product' => $request->input('product'),
            'balance' => $request->input('balance'),
            'nip' => $request->input('nip'),
        ]);

        return response()->json([
            'message' => 'Account created successfully',
            'account' => $account
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function showByID(int $id): JsonResponse {
        $person = Account::where('id', $id)->first();

        if ($person === null) {
            return response()->json([
                'message' => 'Account not found, please check your ID',
            ], 400);
        }

        return response()->json([
            'account' => Account::where('id', $id)->first()
        ]);
    }

}
