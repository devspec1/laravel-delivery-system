<?php

/**
 * Referrals Users DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Referrals Users
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\ReferralUser;
use Yajra\DataTables\Services\DataTable;
use DB;

class ReferralsDataTable extends DataTable
{
    protected $user_type;

    // Set the value for User Type 
    public function setUserType($user_type){
        $this->user_type = $user_type;
        return $this;
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->of($query)
            ->addColumn('rider_name', function ($referral_user) {
                return $referral_user->user->first_name;
            })
            ->addColumn('referrer_name', function ($referral_user) {
                return $referral_user->referred_user_name;
            })
            ->addColumn('earned_amount', function ($referral_user) {
                return $referral_user->where('user_id',$referral_user->user_id)->where('payment_status','Completed')->where('pending_amount',0)->sum('amount');
            })
            ->addColumn('pending_amount', function ($referral_user) {
                return $referral_user->where('user_id',$referral_user->user_id)->where('payment_status','Completed')->where('pending_amount','>',0)->sum('pending_amount');
            })
            ->addColumn('action', function ($referral_user) {
                $detail = '<a href="'.url('admin/referrals/'.$referral_user->user_id).'" class="btn btn-xs btn-primary"><i class="fa fa-eye" ></i></a>';

                return $detail;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param ReferralUser $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ReferralUser $model)
    {
        $referrals = $model->with('user','referral_user')->where('user_type',$this->user_type)->where('payment_status','!=','Expired')->groupBy('user_id')->get();
        return $referrals;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->addAction()
                    ->dom('lBfr<"table-responsive"t>ip')
                    ->orderBy(0,'DESC')
                    ->buttons(
                        ['csv', 'excel', 'print', 'reset']
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'user.id', 'name' => 'user.id', 'title' => 'Id'],
            ['data' => 'rider_name', 'name' => 'rider_name', 'title' => $this->user_type.' Name'],
            ['data' => 'referrer_name', 'name' => 'referrer_name', 'title' => 'Referrer Name'],
            ['data' => 'currency_code', 'name' => 'currency_code', 'title' => 'Currency Code'],
            ['data' => 'earned_amount', 'name' => 'amount', 'title' => 'Earned Amount'],
            ['data' => 'pending_amount', 'name' => 'amount', 'title' => 'Pending Amount'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'referrals_' . date('YmdHis');
    }
}