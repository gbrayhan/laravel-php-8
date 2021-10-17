<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller {
    public function __construct() {
        $this->middleware('check.jwt');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
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

}
