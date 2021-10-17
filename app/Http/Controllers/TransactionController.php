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
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Show the form for editing the specified resource.
     *
     *
     * @return JsonResponse
     */
    public function showOwnUser(): JsonResponse {
        $user = auth()->user();
        $transactions = Transaction::where('user_id', $user->id)->paginate(5);

        return response()->json($transactions);
    }
}
