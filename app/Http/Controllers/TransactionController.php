<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller {
    public function __construct() {
        $this->middleware('check.jwt');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection|Transaction[]
     */
    public function showAll(): Collection|array {
        return Transaction::all();
    }

    /**
     * @OA\Post(
     *      path="/transaction",
     *      operationId="transactionStore",
     *      tags={"Transaction"},
     *      summary="Register Transaction",
     *      description="Register Transaction",
     *      security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="source_account",
     *                      type="string",
     *                      description="Source Account",
     *                      example="222A12311"
     *                  ),
     *                  @OA\Property(
     *                      property="destination_account",
     *                      type="string",
     *                      description="Destination Account",
     *                      example="44222A12311"
     *                  ),
     *                  @OA\Property(
     *                      property="operation_type",
     *                      type="string",
     *                      description="Operation Type",
     *                      example="abono"
     *                  ),
     *                  @OA\Property(
     *                      property="amount",
     *                      type="decimal",
     *                      description="Amount",
     *                      example="120.1234"
     *                  ),
     *                  @OA\Property(
     *                      property="concept",
     *                      type="string",
     *                      description="Concept",
     *                      example="Pago de Servicio"
     *                  ),
     *                  @OA\Property(
     *                      property="reference",
     *                      type="string",
     *                      description="Reference",
     *                      example="F112334"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="transaction created successfully"),
     *              @OA\Property(property="transaction", type="object", ref="#/components/schemas/Transaction"),
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
            'source_account' => 'required|max:100',
            'destination_account' => 'required|max:100',
            'operation_type' => 'required|max:30',
            'amount' => 'required|numeric|between:0.1,99999999.99',
            'concept' => 'required|max:30',
            'reference' => 'required|max:100',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = auth()->user();
        $sourceAccount = Account::where('account_number', $request->input('source_account'))->first();
        $userSourceAccount = $sourceAccount->person->user;

        if ($userSourceAccount === null || $userSourceAccount->id !== $user->id) {
            return response()->json([
                'message' => 'Transaction not allowed',
            ], 405);
        }


        try {
            $transaction = Transaction::create([
                'source_account' => $request->input('source_account'),
                'destination_account' => $request->input('destination_account'),
                'operation_type' => $request->input('operation_type'),
                'amount' => $request->input('amount'),
                'concept' => $request->input('concept'),
                'reference' => $request->input('reference'),
                'transaction_date' => now(),
                'user_id' => $user->id,
            ]);

            return response()->json([
                'message' => 'Transaction successfully created',
                'transaction' => $transaction,
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Error',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function showByID(int $id): JsonResponse {
        $person = Transaction::where('id', $id)->first();

        if ($person === null) {
            return response()->json([
                'message' => 'Transaction not found, please check your ID',
            ], 400);
        }

        return response()->json([
            'transaction' => Transaction::where('id', $id)->first()
        ]);
    }

    /**
     * @OA\Get(
     *      path="/transaction/own-user",
     *      operationId="transactionStore",
     *      tags={"Transaction"},
     *      summary="Transaction for a Current User",
     *      description="Transaction for a Current User",
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(parameter="page", name="page",in="query", required=false, description="Page in pagination"),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example="2"),
     *              @OA\Property(property="data", type="array",  @OA\Items( type="object", ref="#/components/schemas/Transaction" ) ),
     *              @OA\Property(property="first_page_url", type="string", example="http://localhost/api/transaction/own-user?page=1"),
     *              @OA\Property(property="from", type="string", example="5"),
     *              @OA\Property(property="last_page", type="string", example="3"),
     *              @OA\Property(property="last_page_url", type="string", example="http://localhost/api/transaction/own-user?page=3"),
     *              @OA\Property(property="links", type="array",  @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="url", type="string", example="http://localhost/api/transaction/own-user?page=1" ),
     *                  @OA\Property(property="label", type="string", example="1" ),
     *                  @OA\Property(property="active", type="boolean", example="false" ),
     *                  )
     *              ),
     *              @OA\Property(property="next_page_url", type="string", example="http://localhost/api/transaction/own-user?page=4"),
     *              @OA\Property(property="path", type="string", example="http://localhost/api/transaction/own-user"),
     *              @OA\Property(property="per_page", type="integer", example="5"),
     *              @OA\Property(property="prev_page_url", type="string", example="http://localhost/api/transaction/own-user?page=4"),
     *              @OA\Property(property="to", type="integer", example="10"),
     *              @OA\Property(property="total", type="integer", example="14"),
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
    public function showOwnUser(): JsonResponse {
        $user = auth()->user();
        $transactions = Transaction::where('user_id', $user->id)->paginate(5);

        return response()->json($transactions);
    }
}
