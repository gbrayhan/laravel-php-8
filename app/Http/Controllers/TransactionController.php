<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Person;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
    public function index(): Collection|array {
        return Transaction::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     */
    public function store(Request $request) {
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
        $userSourceAccount =  $sourceAccount->person->user;

        if ($userSourceAccount->id !== $user->id) {
            return response()->json([
                'message' => 'Transaction not allowed',
            ], 405);
        }


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

        return $transaction;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function showByID($id) {
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


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id) {
        //
    }
}
