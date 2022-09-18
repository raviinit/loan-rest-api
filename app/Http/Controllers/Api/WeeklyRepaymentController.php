<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WeeklyRepaymentRequest;
use App\Models\Loan;
use App\Models\WeeklyRepayment;
use Illuminate\Http\Request;

class WeeklyRepaymentController extends Controller
{
    /**
     * Get a scheduled payment
     * @param App\Http\Requests\WeeklyRepaymentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function loanRepayment(WeeklyRepaymentRequest $request) {
        try {
            $loan = Loan::find($request['loan_id']);
            if($loan['approval_status'] === 0){
                return response()->json([
                    'success'    => false,
                    'message'   => 'Your loan is not approved yet.'
                ], 401);
            }
            $loanAmount = $loan->loan_amount;
            $loanTerm = $loan->loan_term;
            $weeklyRepayments = $loan->weeklyRepayments()->get();
            $totalPaidAmount = $loan->getTotalPaidAmount();
            $firstUnpaidRec = $weeklyRepayments->where('status', '1')->first();
            
            $totalPaidAmount = $totalPaidAmount + $request['amount'];
            if($totalPaidAmount > $loanAmount){
                return response()->json([
                    'success'    => false,
                    'message'   => 'Your loan amount is exceeded then actual amount.'
                ], 401);
            }

            if(($loanTerm - 1) == count($weeklyRepayments) && $loanAmount != $totalPaidAmount){
                return response()->json([
                    'success'    => false,
                    'message'   => 'This is your last schedule payment, please pay $'.$loanAmount - $weeklyRepayments->sum('paid_amount').' to complete loan.'
                ], 401);
            }

            if($request['amount'] < $firstUnpaidRec['amount']){
                return response()->json([
                    'success'    => false,
                    'message'   => 'Your loan amount should not less than to EMI amount.'
                ], 401);
            } 

            if($firstUnpaidRec['amount']  == $request['amount']){
                WeeklyRepayment::where('id', $firstUnpaidRec['id'])->update(
                    array('status' => 2)
                );
                if($loan->getTotalPaidAmount() == $loanAmount){
                    $this->closeLoan($loan);
                }
                return response()->json([
                    'success'    => true,
                    'message'   => 'Repayment successful'
                ], 200);
            }
            
            WeeklyRepayment::where('id', $firstUnpaidRec['id'])->update(
                array('amount' => $request['amount'], 'status' => 2)
            );
            
            $this->calculateRemainPayments($loan);
            return response()->json([
                'success'    => true,
                'message'   => 'Repayment successful'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'    => false,
                'message'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Calculation of EMI repayment amount
     * @param App\Models\Loan $loan
     */
    public function calculateRemainPayments($loan) {
        try {
            $loanAmount = $loan->loan_amount;
            $weeklyRepayments = $loan->weeklyRepayments()->get();
            $totalPaidAmount = $loan->getTotalPaidAmount();
            $unpaidRepayments = $weeklyRepayments->where('status', '1');
            $duePayment = $loanAmount  - $totalPaidAmount;
            if($duePayment == 0){
                WeeklyRepayment::where('status',1)->delete();
                $this->closeLoan($loan);
            }
            
            $dividedAmount = $duePayment / count($unpaidRepayments);
            foreach($unpaidRepayments as $payment){
                WeeklyRepayment::where('id', $payment['id'])->update(
                    array('amount' => $dividedAmount)
                );
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success'    => false,
                'message'   => $th->getMessage()
            ], 500);
        }
    }
    
    /**
     * Close Loan
     * @param App\Models\Loan $loan
     */
    public function closeLoan($loan) {
        try {
            Loan::where('id', $loan->id)->update(
                array('status' => 2)
            );
        } catch (\Throwable $th) {
            return response()->json([
                'success'    => false,
                'message'   => $th->getMessage()
            ], 500);
        }
    }
}
