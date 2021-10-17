<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller {
    public function __construct() {
        $this->middleware('check.jwt');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|max:100',
            'person_id' => 'required|numeric',
            'product' => 'required|max:30',
            'balance' => 'required|numeric',
            'nip' => 'required|max:30',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $userId= $request->input('is_user_account') ? auth()->user()->id : null;


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
            'persona' => $account
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }
}
