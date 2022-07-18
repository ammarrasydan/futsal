<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use DataTables;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;
use App\Http\Controllers\SSP;
use DatePeriod;
use Image;

class AjaxController extends Controller
{
    var $message;

    function DateFormat($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function ChangeLanguage(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        // return response()->json($response);

        $selectedlanguage = $request->input('listlanguage');

        // What languages do we support
        $available_langs = array('EN', 'CN');

        if (session()->has('lang')) {
            // check if the language is one we support
            if (in_array($selectedlanguage, $available_langs)) {
                session()->put('lang', $selectedlanguage);
            } else {
                session()->put('lang', 'EN');
            }
        } else {
            session()->put('lang', 'EN');
        }

        if (empty($error)) {
            $response['status'] = 'success';
        } else {
            $response['status'] = 'error';
        }

        return response()->json($response);
    }

    public function GetCurrentLanguage()
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        // return response()->json($response);

        // What languages do we support
        $available_langs = array('EN', 'CN');
        $k = 1;

        if (session()->has('lang')) {
            // check if the language is one we support
            if (in_array(session()->get('lang'), $available_langs)) {
                $k = array_search(session()->get('lang'), $available_langs);
                return $available_langs[$k];
            }
        }

        return $available_langs[$k];
    }

    public function Login(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        // return response()->json($response);
        $error = array();
        $userip = $request->ip();
        $username = $request->input('username');
        $password = $request->input('password');
        $remember = true;
        $userfullname = '';

        if ($username) { } else {
            $error['username'] = 'Username is required';
        }

        if ($password) { } else {
            $error['password'] = 'Password is required';
        }

        if (empty($error)) {
            try {
                $rs_login = DB::select("select *
                from admins a
                where a.rec_type > 0
                and a.status = 'Active'
                and a.username = ?
                and a.password = ?
                and ifnull(a.password,'') <> ''", [$username, $password]);
                $numrow_login = count($rs_login);

                if ($numrow_login == 1) {
                    foreach ($rs_login as $row_login) {
                        $userid = $row_login->id;
                        $userfullname = $row_login->full_name;

                        Auth::loginUsingId($userid, $remember);
                    }
                } else {
                    $error['message'] = 'Invalid username or password';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__ . $ex->getMessage();
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] = 'Login successful. Redirecting to Home';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Error';
        }

        return response()->json($response);
    }

    public function GetUsers(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        //return response()->json($response);

        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $error = $arr = $arrs = $arr_statuses = array();
        $searchusername = $request->input('searchusername');
        $searchfullname = $request->input('searchfullname');
        $searchphonenumber = $request->input('searchphonenumber');
        $searchstatus = $request->input('searchstatus');
        $transactiondatefrom = $request->input('transactiondatefrom');
        $transactiondateto = $request->input('transactiondateto');
        $sql_search_username = $sql_search_fullname = $sql_search_phonenumber = $sql_search_status = $sql_search_transaction_date_from = $sql_search_transaction_date_to = '';

        if ($searchusername) {
            $sql_search_username = '%' . $searchusername . '%';
        } else {
            $sql_search_username = '%';
        }

        if ($searchfullname) {
            $sql_search_fullname = '%' . $searchfullname . '%';
        } else {
            $sql_search_fullname = '%';
        }

        if ($searchphonenumber) {
            $sql_search_phonenumber = '%' . $searchphonenumber . '%';
        } else {
            $sql_search_phonenumber = '%';
        }

        try {
            $rs = DB::select("select distinct a.status
            from users a
            ");
            $numrow = count($rs);

            if ($numrow > 0) {
                foreach ($rs as $row) {
                    $arr_statuses[] = $row->status;
                }
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
        }

        if ($searchstatus) {
            $sql_search_status = $searchstatus;
        } else {
            $sql_search_status = '%';
        }

        if ($transactiondatefrom) {
            if ($this->DateFormat($transactiondatefrom, 'Y-m-d h:i:s A')) {
                $sql_search_transaction_date_from = $transactiondatefrom;
            } else {
                $error['transactiondatefrom'] = 'Invalid transaction date from';
            }
        } else {
            $sql_search_transaction_date_from = '2000-01-01 12:00:00 AM';
        }

        if ($transactiondateto) {
            if ($this->DateFormat($transactiondateto, 'Y-m-d h:i:s A')) {
                $sql_search_transaction_date_to = $transactiondateto;
            } else {
                $error['transactiondateto'] = 'Invalid transaction date to';
            }
        } else {
            $sql_search_transaction_date_to = '2099-12-31 11:59:59 PM';
        }

        $primaryKey = "a.id";

        $fields = "a.id, a.username, a.fullname, a.phonecode, a.phonenumber, a.password, a.status, DATE_FORMAT(a.created_date,'%Y-%m-%d %h:%i %p') as created_date";

        $table = " Users a ";

        $where = "where a.rec_type > 0
        and ifnull(a.username,'') like ?
        and ifnull(a.fullname,'') like ?
            and concat(ifnull(a.phonecode,''),ifnull(a.phonenumber,'')) like ?
            and ifnull(a.status,'') like ?
            and DATE_FORMAT(a.created_date,'%Y%m%d%H%i%s') >= DATE_FORMAT(STR_TO_DATE(?, '%Y-%m-%d %h:%i:%s %p'),'%Y%m%d%H%i%s')
            and DATE_FORMAT(a.created_date,'%Y%m%d%H%i%s') <= DATE_FORMAT(STR_TO_DATE(?, '%Y-%m-%d %h:%i:%s %p'),'%Y%m%d%H%i%s')
            ";

        $response = SSP::complex($request, $fields, $table, $primaryKey, $where, [$sql_search_username, $sql_search_fullname, $sql_search_phonenumber, $sql_search_status, $sql_search_transaction_date_from, $sql_search_transaction_date_to]);

        if (is_array($response['data'])) {
            if (count($response['data']) > 0) {
                foreach ($response['data'] as $row) {
                    $userid = $row->id;
                    $username = $row->username;
                    $fullname = $row->fullname;
                    $phonecode = $row->phonecode;
                    $phonenumber = $row->phonenumber;
                    $password = $row->password;
                    $status = $row->status;
                    $created_date = $row->created_date;

                    $arr['id'] = $userid;
                    $arr['username'] = $username;
                    $arr['fullname'] = $fullname;
                    $arr['phonenumber'] = $phonecode . $phonenumber;
                    $arr['password'] = $password;
                    $arr['status'] = $status;
                    $arr['createddate'] = $created_date;

                    $arrs[] = $arr;
                }
                $response['data'] = $arrs;
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['users'] = $arrs;
            $response['searchusername'] = $searchusername;
            $response['searchfullname'] = $searchfullname;
            $response['searchphonenumber'] = $searchphonenumber;
            $response['statuses'] = $arr_statuses;
            $response['searchstatus'] = $searchstatus;
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Error';
        }

        return response()->json($response);
    }

    public function GetBookings(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        //return response()->json($response);

        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $error = $arr = $arrs = $arr_statuses = array();
        $searchusername = $request->input('searchusername');
        $searchfullname = $request->input('searchfullname');
        $searchphonenumber = $request->input('searchphonenumber');
        $searchstatus = $request->input('searchstatus');
        $transactiondatefrom = $request->input('transactiondatefrom');
        $transactiondateto = $request->input('transactiondateto');
        $sql_search_username = $sql_search_fullname = $sql_search_phonenumber = $sql_search_status = $sql_search_transaction_date_from = $sql_search_transaction_date_to = '';

        if ($searchusername) {
            $sql_search_username = '%' . $searchusername . '%';
        } else {
            $sql_search_username = '%';
        }

        if ($searchfullname) {
            $sql_search_fullname = '%' . $searchfullname . '%';
        } else {
            $sql_search_fullname = '%';
        }

        if ($searchphonenumber) {
            $sql_search_phonenumber = '%' . $searchphonenumber . '%';
        } else {
            $sql_search_phonenumber = '%';
        }

        try {
            $rs = DB::select("select distinct a.status
            from booking a
            ");
            $numrow = count($rs);

            if ($numrow > 0) {
                foreach ($rs as $row) {
                    $arr_statuses[] = $row->status;
                }
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
        }

        if ($searchstatus) {
            $sql_search_status = $searchstatus;
        } else {
            $sql_search_status = '%';
        }

        if ($transactiondatefrom) {
            if ($this->DateFormat($transactiondatefrom, 'Y-m-d h:i:s A')) {
                $sql_search_transaction_date_from = $transactiondatefrom;
            } else {
                $error['transactiondatefrom'] = 'Invalid transaction date from';
            }
        } else {
            $sql_search_transaction_date_from = '2000-01-01 12:00:00 AM';
        }

        if ($transactiondateto) {
            if ($this->DateFormat($transactiondateto, 'Y-m-d h:i:s A')) {
                $sql_search_transaction_date_to = $transactiondateto;
            } else {
                $error['transactiondateto'] = 'Invalid transaction date to';
            }
        } else {
            $sql_search_transaction_date_to = '2099-12-31 11:59:59 PM';
        }

        $primaryKey = "a.id";

        $fields = "a.id, a.fullname, a.phonecode, a.phonenumber, a.payment, a.court, a.status, DATE_FORMAT(a.created_date,'%Y-%m-%d %h:%i %p') as created_date";

        $table = " Booking a ";

        $where = "where a.rec_type > 0
        and ifnull(a.username,'') like ?
        and ifnull(a.fullname,'') like ?
            and concat(ifnull(a.phonecode,''),ifnull(a.phonenumber,'')) like ?
            and ifnull(a.status,'') like ?
            and DATE_FORMAT(a.created_date,'%Y%m%d%H%i%s') >= DATE_FORMAT(STR_TO_DATE(?, '%Y-%m-%d %h:%i:%s %p'),'%Y%m%d%H%i%s')
            and DATE_FORMAT(a.created_date,'%Y%m%d%H%i%s') <= DATE_FORMAT(STR_TO_DATE(?, '%Y-%m-%d %h:%i:%s %p'),'%Y%m%d%H%i%s')
            ";

        $response = SSP::complex($request, $fields, $table, $primaryKey, $where, [$sql_search_username, $sql_search_fullname, $sql_search_phonenumber, $sql_search_status, $sql_search_transaction_date_from, $sql_search_transaction_date_to]);

        if (is_array($response['data'])) {
            if (count($response['data']) > 0) {
                foreach ($response['data'] as $row) {
                    $userid = $row->id;
                    $fullname = $row->fullname;
                    $phonecode = $row->phonecode;
                    $phonenumber = $row->phonenumber;
                    $payment = $row->payment;
                    $court = $row->court;
                    $status = $row->status;
                    $created_date = $row->created_date;

                    $arr['id'] = $userid;
                    $arr['fullname'] = $fullname;
                    $arr['phonenumber'] = $phonecode . $phonenumber;
                    $arr['payment'] = $payment;
                    $arr['court'] = $court;
                    $arr['status'] = $status;
                    $arr['createddate'] = $created_date;

                    $arrs[] = $arr;
                }
                $response['data'] = $arrs;
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['booking'] = $arrs;
            $response['searchusername'] = $searchusername;
            $response['searchfullname'] = $searchfullname;
            $response['searchphonenumber'] = $searchphonenumber;
            $response['statuses'] = $arr_statuses;
            $response['searchstatus'] = $searchstatus;
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Error';
        }

        return response()->json($response);
    }

    public function GetProductDiscountRate(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        //return response()->json($response);

        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $error = $arr = $arrs = $arr_paymentmethods = $arr_paymentmethod = array();
        $searchpaymentmethod = $request->input('searchpaymentmethod');
        $searchfullname = $request->input('searchfullname');
        $transactiondatefrom = $request->input('transactiondatefrom');
        $transactiondateto = $request->input('transactiondateto');
        $sql_search_fullname = $sql_search_search_paymentmethod = $sql_search_transaction_date_from = $sql_search_transaction_date_to = '';

        if ($searchpaymentmethod) {
            $sql_search_search_paymentmethod = '%' . $searchpaymentmethod . '%';
        } else {
            $sql_search_search_paymentmethod = '%';
        }

        if ($searchfullname) {
            $sql_search_fullname = '%' . $searchfullname . '%';
        } else {
            $sql_search_fullname = '%';
        }

        if ($transactiondatefrom) {
            if ($this->DateFormat($transactiondatefrom, 'Y-m-d h:i:s A')) {
                $sql_search_transaction_date_from = $transactiondatefrom;
            } else {
                $error['transactiondatefrom'] = 'Invalid transaction date from';
            }
        } else {
            $sql_search_transaction_date_from = '2000-01-01 12:00:00 AM';
        }

        if ($transactiondateto) {
            if ($this->DateFormat($transactiondateto, 'Y-m-d h:i:s A')) {
                $sql_search_transaction_date_to = $transactiondateto;
            } else {
                $error['transactiondateto'] = 'Invalid transaction date to';
            }
        } else {
            $sql_search_transaction_date_to = '2099-12-31 11:59:59 PM';
        }

        try {
            $rs = DB::select("select a.name, a.description, a.logo
            from PaymentMethods a 
            where a.rec_type > 0 
            and a.status = 'Active'
            ");
            $numrow = count($rs);

            if ($numrow > 0) {
                foreach ($rs as $row) {
                    $name = $row->name;
                    $logo = $row->logo;

                    $arr_paymentmethod['name'] = $name;
                    $arr_paymentmethod['logo'] = url('img/icons/' . $logo);

                    $arr_paymentmethods[] = $arr_paymentmethod;
                }
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
        }

        $primaryKey = "a.id";

        $fields = "a.id, a.payment_method, a.operator, a.rate, a1.full_name as fullname, a.status, DATE_FORMAT(a.created_date,'%Y-%m-%d %h:%i %p') as created_date";

        $table = " ProductDiscountRate a inner join admins a1 on a.created_by = a1.id ";

        $where = "where a.rec_type > 0
            and ifnull(a.payment_method,'') like ?
            and ifnull(a1.full_name,'') like ?
            and DATE_FORMAT(a.created_date,'%Y%m%d%H%i%s') >= DATE_FORMAT(STR_TO_DATE(?, '%Y-%m-%d %h:%i:%s %p'),'%Y%m%d%H%i%s')
            and DATE_FORMAT(a.created_date,'%Y%m%d%H%i%s') <= DATE_FORMAT(STR_TO_DATE(?, '%Y-%m-%d %h:%i:%s %p'),'%Y%m%d%H%i%s')
            ";

        $order = " order by FIELD(a.status, 'Active', 'Expired'), a.created_date desc ";

        $response = SSP::complex($request, $fields, $table, $primaryKey, $where, [$sql_search_search_paymentmethod, $sql_search_fullname, $sql_search_transaction_date_from, $sql_search_transaction_date_to], $order);

        if (is_array($response['data'])) {
            if (count($response['data']) > 0) {
                foreach ($response['data'] as $row) {
                    $id = $row->id;
                    $payment_method = $row->payment_method;
                    $operator = $row->operator;
                    $rate = $row->rate;
                    $fullname = $row->fullname;
                    $status = $row->status;
                    $created_date = $row->created_date;

                    if ($status == 'Active') {
                        $status = 'Active';
                    } elseif ($status == 'Expired') {
                        $status = 'Expired';
                    }

                    $arr['id'] = $id;
                    $arr['paymentmethod'] = $payment_method;
                    $arr['rate'] = $operator . '' . (float) $rate . '%';
                    $arr['fullname'] = $fullname;
                    $arr['status'] = $status;
                    $arr['createddate'] = $created_date;

                    $arrs[] = $arr;
                }
                $response['data'] = $arrs;
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['users'] = $arrs;
            $response['searchpaymentmethod'] = $searchpaymentmethod;
            $response['paymentmethods'] = $arr_paymentmethods;
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Error';
        }

        return response()->json($response);
    }

    public function EditProductPricePercentage(Request $request)
    {
        foreach ($request->input() as $k => $v) {
            $response[$k] = $v;
        }

        // return response()->json($response);

        $error = array();
        $userid = Auth::user()->id;
        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $paymentmethod = $request->input('paymentmethod');
        $percentage = $request->input('percentage');
        $percentageoperator = $request->input('percentageoperator');

        if ($percentage != '') {
            if (is_numeric($percentage)) {
                if ($percentage < 0) {
                    $error['percentage'] = 'Percentage is invalid'; // . $ex->getMessage();   
                } else {
                    $percentage = floor($percentage * 10000) / 10000;
                }
            } else {
                $error['percentage'] = 'Percentage is invalid'; // . $ex->getMessage();    
            }
        } else {
            $error['percentage'] = 'Percentage is required'; // . $ex->getMessage();
        }

        if (in_array($percentageoperator, array('+', '-'))) { } else {
            $error['percentage'] = 'Percentage is required'; // . $ex->getMessage();
        }

        if ($paymentmethod) {
            try {
                $rs = DB::select("select a.name, a.description, a.logo
                from PaymentMethods a 
                where a.rec_type > 0 
                and a.status = 'Active'
                and a.name = ?
                ", [$paymentmethod]);
                $numrow = count($rs);

                if ($numrow != 1) {
                    $error['paymentmethod'] = 'Payment method is invalid'; // . $ex->getMessage();        
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
            }
        } else {
            $error['paymentmethod'] = 'Payment method is required'; // . $ex->getMessage();
        }


        if (empty($error)) {
            try {
                $update = DB::update("update ProductDiscountRate
                set status = 'Expired',
                rec_type = rec_type + 1,
                updated_date = NOW()
                where rec_type > 0
                and status = 'Active'
                and payment_method = ?
                ", [$paymentmethod]);

                if ($update) { }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('Service unavailable') . $ex->getMessage();
            }
        }

        if (empty($error)) {
            try {
                $insert = DB::insert("insert into ProductDiscountRate (payment_method,operator,rate,status,rec_type,created_by,created_date,created_location,updated_by,updated_date,updated_location) values (?,?,?,'Active',1,?,NOW(),0,?,NOW(),0)", [$paymentmethod, $percentageoperator, $percentage, $userid, $userid]);

                if ($insert) { } else {
                    $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('Service unavailable');
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('Service unavailable') . $ex->getMessage();
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] = 'New percentage updated successfully'; // . $ex->getMessage();
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function PrepareEditOrder(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        // return response()->json($response);

        $error = array();
        $orderno = $request->input('orderno');
        $order = $products = array();
        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $shippingdetails = $billingdetails = $paymentstatus = $paymentstatusclass = $orderfor = $convertAmt = '';
        $totalamount = $total_paid_amount = $balance_amount = $balance_amount_text = 0;

        try {
            $rs = DB::select("select distinct ifnull(a9.payment_amount,0) as payment_amount
            from (
            select a.order_no, a.member_id, ifnull(a2.payment_amount,0) as payment_amount 
            from (
                select aa.id, aa.order_no, aa.member_id, aa.product_name, aa.category_2, aa.capacity, aa.uom, ifnull(aa.quantity,0) as quantity, ifnull(aa.price,0) as price, aa.image from Orders aa where aa.rec_type > 0) a
                left join (select aa.order_no, sum(ifnull(aa.amount,0)) as shipping_amount from OrderShippings aa where aa.rec_type > 0 group by aa.order_no) a1
                on a.order_no = a1.order_no
                left join (select aa.order_no, sum(ifnull(aa.amount,0)) as payment_amount from OrderPayments aa where aa.rec_type > 0 group by aa.order_no) a2
                on a.order_no = a2.order_no
            ) a9
            left join Users a8
            on a9.member_id = a8.referral_code
            and a8.rec_type > 0
            where a9.order_no = ?
            ", [$orderno]);
            $numrow = count($rs);

            if ($numrow > 0) {
                foreach ($rs as $row) {
                    $total_paid_amount = $row->payment_amount;

                    if ($total_paid_amount < 0) {
                        $total_paid_amount = $total_paid_amount * -1;
                    }
                }
            } else {
                $error['message'] = 'Invalid order'; // . $ex->getMessage();
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $error['message'] = 'ERR' . __LINE__; // . $ex->getMessage();
        }

        if (empty($error)) {
            try {
                // $rs = DB::select("select a.product_name, a.category_2, a.capacity, a.uom, ifnull(a.quantity,0) as quantity, ifnull(a.price,0) as price, a.image
                // from Orders a
                // where a.rec_type > 0
                // and a.status = 'Active'
                // and a.member_phonecode = ?
                // and a.member_phonenumber = ?
                // and a.order_no = ?
                // order by a.id desc
                // ", [$phonecode, $phonenumber, $orderno]);

                $rs = DB::select("
            
            select distinct a9.id, a4.name as product_name, a4.short_description, a4.description, ifnull(a9.quantity,0) as quantity, ifnull(a9.price,0) as price, a9.callback_content
            from (select a.id, a.order_no, a.member_id, a.product_slug, a.product_name, ifnull(a.quantity,0) as quantity, ifnull(a.price,0) as price, a5.callback_content from (select aa.id, aa.order_no, aa.member_id, aa.product_slug, aa.product_name, ifnull(aa.quantity,0) as quantity, ifnull(aa.price,0) as price from Orders aa where aa.rec_type > 0) a
            left join (select aa.order_no, sum(ifnull(aa.amount,0)) as shipping_amount from OrderShippings aa where aa.rec_type > 0 group by aa.order_no) a1
            on a.order_no = a1.order_no
            left join (select aa.order_no, sum(ifnull(aa.amount,0)) as payment_amount from OrderPayments aa where aa.rec_type > 0 group by aa.order_no) a2
            on a.order_no = a2.order_no
            left join (select aa.order_no, aa.content as callback_content
            from OrderCallbacks aa
            inner join (select max(a.id) as id, a.order_no
            from OrderCallbacks a
            where a.rec_type > 0
            group by a.order_no) a3
            on aa.id = a3.id
            ) a5
            on a.order_no = a5.order_no
            ) a9
            left join Users a8
            on a9.member_id = a8.referral_code
            and a8.rec_type > 0
            left join (select a2.lang, a2.id, a2.slug, a2.name, a2.short_description, a2.description from (
            select 'EN' as lang, a.id, a.slug, a.name, a.short_description, a.description
            from Products a
            where a.rec_type > 0
            UNION
            select a1.language as lang, a1.id, a1.slug, a1.name, a1.short_description, a1.description
            from TranslateProducts a1
            where a1.rec_type > 0 and a1.id in (select distinct id from Products where rec_type > 0)
            ) a2
            where a2.lang = ?) a4
            on a9.product_slug = a4.slug
            where a9.order_no = ?
            ", [$this->GetCurrentLanguage(), $orderno]);
                $numrow = count($rs);

                if ($numrow > 0) {
                    foreach ($rs as $row) {
                        $product_name = $row->product_name;
                        $short_description = $row->short_description;
                        $description = $row->description;
                        $quantity = $row->quantity;
                        $price = $row->price;
                        $callback_content = $row->callback_content;

                        $callback_content = json_decode($callback_content);

                        $convertAmt = '';

                        if (isset($callback_content->txnAmt)) {
                            if (!empty($callback_content->txnAmt)) {
                                if (is_numeric($callback_content->txnAmt)) {
                                    $convertAmt = '¥' . number_format($callback_content->txnAmt / 100, 2);
                                }
                            }
                        }

                        $totalamount += ($price * $quantity);

                        $order['name'] = $product_name;
                        $order['shortdescription'] = $short_description;
                        $order['description'] = $description;
                        $order['quantity'] = (int) $quantity;
                        $order['price'] = $price;
                        $order['itemtotalprice'] = number_format($price * $quantity, 2) . ' RMB';
                        $order['pricetext'] = number_format($price, 2) . ' RMB';

                        $products[] = $order;
                    }

                    if ($total_paid_amount < 0) {
                        $total_paid_amount = $total_paid_amount * -1;
                    }

                    $balance_amount = $totalamount - $total_paid_amount;
                    $balance_amount_text = $balance_amount;

                    if ($total_paid_amount <= 0) {
                        $paymentstatus = 'unPay';
                        $paymentstatusclass = 'text-danger';
                    } elseif ($total_paid_amount < $totalamount) {
                        $paymentstatus = 'Partial payment';
                        $paymentstatusclass = 'text-warning';
                    } elseif ($total_paid_amount >= $totalamount) {
                        $paymentstatus = 'Paid';
                        $paymentstatusclass = 'text-success';
                    } else {
                        $paymentstatus = '-';
                    }
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERROR' . __LINE__; // . $ex->getMessage();
            }
        }

        if (empty($error)) {
            try {
                $rs = DB::select("select distinct a.member_id, a.member_fullname, a.member_phonecode, a.member_phonenumber, a.address1_billing, a.address1_shipping, a.address2_billing, a.address2_shipping, a.city_billing, a.city_shipping, a.companyname_billing, a.companyname_shipping, a.member_id, a.member_fullname, a.member_phonenumber, a.name_billing, a.name_shipping, a.phone_billing, a.phonecode_billing, a.phone_shipping, a.phonecode_shipping, a.postcode_billing, a.postcode_shipping, a.state_billing, a.state_shipping, a.country_billing, a.country_shipping 
            from Orders a
            left join Users a8
            on a.member_id = a8.referral_code
            and a8.rec_type > 0
            where a.order_no = ?
            ", [$orderno]);
                $numrow = count($rs);

                if ($numrow > 0) {
                    foreach ($rs as $row) {
                        $member_id = $row->member_id;
                        $member_fullname = $row->member_fullname;
                        $member_phonecode = $row->member_phonecode;
                        $member_phonenumber = $row->member_phonenumber; //
                        $name_shipping = $row->name_shipping;
                        $companyname_shipping = $row->companyname_shipping;
                        $phone_shipping = $row->phone_shipping;
                        $phonecode_shipping = $row->phonecode_shipping;
                        $address1_shipping = $row->address1_shipping;
                        $address2_shipping = $row->address2_shipping;
                        $city_shipping = $row->city_shipping;
                        $postcode_shipping = $row->postcode_shipping;
                        $state_shipping = $row->state_shipping;
                        $country_shipping = $row->country_shipping; //
                        $address1_billing = $row->address1_billing;
                        $address2_billing = $row->address2_billing;
                        $city_billing = $row->city_billing;
                        $companyname_billing = $row->companyname_billing;
                        $name_billing = $row->name_billing;
                        $phone_billing = $row->phone_billing;
                        $phonecode_billing = $row->phonecode_billing;
                        $postcode_billing = $row->postcode_billing;
                        $state_billing = $row->state_billing;
                        $country_billing = $row->country_billing;

                        $orderfor = $member_fullname . "\n" . $member_phonecode . $member_phonenumber;

                        $shippingdetails = $name_shipping . "\n" . $companyname_shipping . "\n" . $phonecode_shipping . $phone_shipping . "\n" . $address1_shipping . ", " . $address2_shipping . ", " . $city_shipping . ", " . $postcode_shipping . ", " . $state_shipping . ", " . $country_shipping;

                        if ($address1_billing != "") {
                            $billingdetails = $name_billing . "\n" . $companyname_billing . "\n" . $phonecode_billing . $phone_billing . "\n" . $address1_billing . ", " . $address2_billing . ", " . $city_billing . ", " . $postcode_billing . ", " . $state_billing . ", " . $country_billing;
                        } else {
                            $billingdetails = $name_shipping . "\n" . $companyname_shipping . "\n" . $phone_shipping . "\n" . $address1_shipping . ", " . $address2_shipping . ", " . $city_shipping . ", " . $postcode_shipping . ", " . $state_shipping . ", " . $country_shipping;
                        }
                    }
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . $ex->getMessage();
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['orderno'] = $orderno;
            $response['paymentstatus'] = $paymentstatus;
            $response['paymentstatusclass'] = $paymentstatusclass;
            $response['orderfor'] = $orderfor;
            $response['shippingdetails'] = $shippingdetails;
            $response['billingdetails'] = $billingdetails;
            $response['products'] = $products;
            $response['ordersubtotal'] = number_format($totalamount, 2) . ' RMB';
            $response['shippingtotal'] = '0.00 RMB';
            $response['ordertotal'] = number_format($totalamount, 2) . ' RMB';
            $response['paidtotal'] = number_format($total_paid_amount, 2) . ' RMB';
            $response['converttotal'] = $convertAmt;
            $response['balancetotaltext'] = number_format($balance_amount_text, 2) . ' RMB';
            $response['balancetotal'] = $balance_amount;
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] = (isset($error['message'])) ? $error['message'] : 'ERR' . __LINE__;
        }

        return response()->json($response);
    }

    public function GetOrders(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        //return response()->json($response);

        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $error = $arr = $arrs = $arr_status = $arr_statuses = array();
        $searchorderno = $request->input('searchorderno');
        $searchbuyername = $request->input('searchbuyername');
        $searchbuyerphonenumber = $request->input('searchbuyerphonenumber');
        $searchproduct = $request->input('searchproduct');
        $searchstatus = $request->input('searchstatus');
        $transactiondatefrom = $request->input('transactiondatefrom');
        $transactiondateto = $request->input('transactiondateto');
        $where = $primaryKey = $sql_search_orderno = $sql_search_member_name = $sql_search_member_phone = $sql_search_product = $sql_search_status = $sql_search_transaction_date_from = $sql_search_transaction_date_to = '';

        if ($searchorderno) {
            $sql_search_orderno = '%' . $searchorderno . '%';
        } else {
            $sql_search_orderno = '%';
        }

        if ($searchbuyername) {
            $sql_search_member_name = '%' . $searchbuyername . '%';
        } else {
            $sql_search_member_name = '%';
        }

        if ($searchbuyerphonenumber) {
            $sql_search_member_phone = '%' . $searchbuyerphonenumber . '%';
        } else {
            $sql_search_member_phone = '%';
        }

        if ($searchproduct) {
            $sql_search_product = '%' . $searchproduct . '%';
        } else {
            $sql_search_product = '%';
        }

        try {
            $rs = DB::select("select a.name, a.type 
            from OrderStatuses a 
            where a.rec_type > 0 
            and a.status = 'Active'
            and a.lang = ?
            ", [$this->GetCurrentLanguage()]);
            $numrow = count($rs);

            if ($numrow > 0) {
                foreach ($rs as $row) {
                    $arr_status['value'] = $row->name;
                    $arr_status['name'] = $row->type;

                    $arr_statuses[] = $arr_status;
                }
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
        }

        if ($searchstatus) {
            foreach ($arr_statuses as $k => $v) {
                if ($v['name'] == $searchstatus) {
                    $sql_search_status = $searchstatus;
                }
            }

            if ($sql_search_status == '') {
                $sql_search_status = '%';
            }
        } else {
            $sql_search_status = '%';
        }

        if ($transactiondatefrom) {
            if ($this->DateFormat($transactiondatefrom, 'Y-m-d h:i:s A')) {
                $sql_search_transaction_date_from = $transactiondatefrom;
            } else {
                $error['transactiondatefrom'] = 'Invalid transaction date from';
            }
        } else {
            $sql_search_transaction_date_from = '2000-01-01 12:00:00 AM';
        }

        if ($transactiondateto) {
            if ($this->DateFormat($transactiondateto, 'Y-m-d h:i:s A')) {
                $sql_search_transaction_date_to = $transactiondateto;
            } else {
                $error['transactiondateto'] = 'Invalid transaction date to';
            }
        } else {
            $sql_search_transaction_date_to = '2099-12-31 11:59:59 PM';
        }

        $primaryKey = "*";

        $fields = " distinct a9.id, a9.order_no, a9.buyer_fullname, a9.buyer_phonenumber, a4.name as product_name, ifnull(a9.quantity,0) * ifnull(a9.price,0) as order_amount, ifnull(a9.quantity,0) as quantity, ifnull(a9.price,0) as price, ifnull(a9.price1,0) as price1, ifnull(a9.payment_amount,0) as payment_amount, a9.status, DATE_FORMAT(a9.created_date,'%Y-%m-%d %h:%i %p') as created_date, a9.callback_content ";

        $table = " (select a.id, a.order_no, a.member_id, a.buyer_fullname, a.buyer_phonenumber, a.product_slug, a.product_name, ifnull(a.quantity,0) as quantity, ifnull(a.price,0) as price, ifnull(a.price1,0) as price1, ifnull(a2.payment_amount,0) as payment_amount, a6.type as status, a.created_date, a5.callback_content 
        from (select aa.id, aa.order_no, aa.member_fullname as buyer_fullname, concat(aa.member_phonecode, aa.member_phonenumber) as buyer_phonenumber, aa.member_id, aa.product_slug, aa.product_name, ifnull(aa.quantity,0) as quantity, ifnull(aa.price,0) as price, ifnull(aa.price1,0) as price1, aa.status, aa.created_date from Orders aa where aa.rec_type > 0) a
        left join (select a1.name, a1.type from OrderStatuses a1 where a1.rec_type > 0 and a1.lang = ?) a6
        on a.status = a6.name
        left join (select aa.order_no, sum(ifnull(aa.amount,0)) as shipping_amount from OrderShippings aa where aa.rec_type > 0 group by aa.order_no) a1
        on a.order_no = a1.order_no
        left join (select aa.order_no, sum(ifnull(aa.amount,0)) as payment_amount from OrderPayments aa where aa.rec_type > 0 group by aa.order_no) a2
        on a.order_no = a2.order_no
        left join (select aa.order_no, aa.content as callback_content
        from OrderCallbacks aa
        inner join (select max(a.id) as id, a.order_no
        from OrderCallbacks a
        where a.rec_type > 0
        group by a.order_no) a3
        on aa.id = a3.id
        ) a5
        on a.order_no = a5.order_no
        ) a9
        left join Users a8
        on a9.member_id = a8.referral_code
        and a8.rec_type > 0
        left join (select a2.lang, a2.id, a2.slug, a2.name, a2.short_description, a2.description 
        from (
            select 'EN' as lang, a.id, a.slug, a.name, a.short_description, a.description
            from Products a
            where a.rec_type > 0
            UNION
            select a1.language as lang, a1.id, a1.slug, a1.name, a1.short_description, a1.description
            from TranslateProducts a1
            where a1.rec_type > 0 and a1.id in (select distinct id from Products where rec_type > 0)
        ) a2
        where a2.lang = ?) a4
        on a9.product_slug = a4.slug 
        ";

        $where = " where ifnull(a9.order_no,'') like ?
        and ifnull(a9.buyer_fullname,'') like ?
        and ifnull(a9.buyer_phonenumber,'') like ?
        and ifnull(a9.product_name,'') like ?
        and ifnull(a9.status,'') like ?
        and DATE_FORMAT(a9.created_date,'%Y%m%d%H%i%s') >= DATE_FORMAT(STR_TO_DATE(?, '%Y-%m-%d %h:%i:%s %p'),'%Y%m%d%H%i%s')
        and DATE_FORMAT(a9.created_date,'%Y%m%d%H%i%s') <= DATE_FORMAT(STR_TO_DATE(?, '%Y-%m-%d %h:%i:%s %p'),'%Y%m%d%H%i%s') ";

        $response = SSP::complex($request, $fields, $table, $primaryKey, $where, [$this->GetCurrentLanguage(), $this->GetCurrentLanguage(), $sql_search_orderno, $sql_search_member_name, $sql_search_member_phone, $sql_search_product, $sql_search_status, $sql_search_transaction_date_from, $sql_search_transaction_date_to]);

        if (is_array($response['data'])) {
            if (count($response['data']) > 0) {
                foreach ($response['data'] as $row) {
                    $id = $row->id;
                    $order_no = $row->order_no;
                    $buyer_fullname = $row->buyer_fullname;
                    $buyer_phonenumber = $row->buyer_phonenumber;
                    $product_name = $row->product_name;
                    $quantity = $row->quantity;
                    $price = $row->price;
                    $price1 = $row->price1;
                    $order_amount = $row->order_amount;
                    $status = $row->status;
                    $created_date = $row->created_date;
                    $callback_content = $row->callback_content;

                    $callback_content = json_decode($callback_content);

                    $arr['convertAmt'] = '';

                    if (isset($callback_content->txnAmt)) {
                        if (!empty($callback_content->txnAmt)) {
                            $txnAmt = $callback_content->txnAmt;

                            $arr['convertAmt'] = '¥' . number_format(floor($txnAmt / 100 * 100) / 100, 2);
                        }
                    }

                    $arr['id'] = $id;
                    $arr['order_no'] = $order_no;
                    $arr['buyer_fullname'] = $buyer_fullname;
                    $arr['buyer_phonenumber'] = $buyer_phonenumber;
                    $arr['product_name'] = $product_name;
                    $arr['quantity'] = number_format(floor($quantity * 1) / 1);
                    $arr['price'] = number_format(floor($price * 100) / 100, 2) . ' RMB';
                    $arr['price1'] = number_format(floor($price1 * 100) / 100, 2) . ' RMB';
                    $arr['order_amount'] = number_format(floor($order_amount * 100) / 100, 2) . ' RMB';
                    $arr['status'] = $status;
                    $arr['createddate'] = $created_date;

                    $arrs[] = $arr;
                }
                $response['data'] = $arrs;
            }
        }

        $response['zzzzzzz'] = $request['columns'];

        if (empty($error)) {
            $response['status'] = 'success';
            $response['searchorderno'] = $searchorderno;
            $response['searchbuyername'] = $searchbuyername;
            $response['searchbuyerphonenumber'] = $searchbuyerphonenumber;
            $response['searchproduct'] = $searchproduct;
            $response['statuses'] = $arr_statuses;
            $response['searchstatus'] = $searchstatus;
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Error';
        }

        return response()->json($response);
    }

    public function PrepareEditUser(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        //return response()->json($response);

        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $error = $user = $arr_phonecode = array();
        $user_id = $request->input('user_id');

        try {
            $rs = DB::select("select * 
            from Country a 
            where a.rec_type > 0 
            and a.status = 'Active'
            ");
            $numrow = count($rs);

            if ($numrow > 0) {
                foreach ($rs as $row) {
                    $arr_phonecode[] = $row->phonecode;
                }
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
        }

        if ($user_id) {
            try {
                $rs = DB::select("select a.id, ifnull(a.username,'') as username, a.fullname, a.email, a.phonecode, a.phonenumber, ifnull(a.image1,'') as image1, ifnull(a.image2,'') as image2, a.status, a.created_date
                from Users a
                where a.rec_type > 0
                and a.id = ?", [$user_id]);
                $numrow = count($rs);

                if ($numrow == 1) {
                    foreach ($rs as $row) {
                        $userid = $row->id;
                        $username = $row->username;
                        $fullname = $row->fullname;
                        $email = $row->email;
                        $phonecode = $row->phonecode;
                        $phonenumber = $row->phonenumber;
                        $status = $row->status;
                        $created_date = $row->created_date;
                        $image1 = $row->image1;
                        $image2 = $row->image2;

                        if ($image1 != '') {
                            $image1 = 'https://example.com/images/users/' . $image1;
                        }

                        if ($image2 != '') {
                            $image2 = 'https://example.com/images/users/' . $image2;
                        }

                        $user['id'] = $userid;
                        $user['username'] = $username;
                        $user['fullname'] = $fullname;
                        $user['email'] = $email;
                        $user['phonecode'] = $phonecode;
                        $user['phonenumber'] = $phonenumber;
                        $user['image1'] =  $image1;
                        $user['image2'] =  $image2;
                        $user['status'] = $status;
                        $user['createddate'] = $created_date;
                    }
                } else {
                    $error['message'] =  'Invalid user';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable';
            }
        } else {
            $error['message'] =  'User ID is required';
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['user'] = $user;
            $response['phonecode'] = $arr_phonecode;
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function PrepareNewUser(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        //return response()->json($response);

        $error = $arr_statuses = $arr_status = $arr_phonecode = array();

        try {
            $rs = DB::select("select * 
            from Country a 
            where a.rec_type > 0 
            and a.status = 'Active'
            ");
            $numrow = count($rs);

            if ($numrow > 0) {
                foreach ($rs as $row) {
                    $arr_phonecode[] = $row->phonecode;
                }
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['phonecode'] = $arr_phonecode;
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function PrepareNewBooking(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        //return response()->json($response);

        $error = $arr_statuses = $arr_status = $arr_phonecode = array();

        try {
            $rs = DB::select("select * 
            from Country a 
            where a.rec_type > 0 
            and a.status = 'Active'
            ");
            $numrow = count($rs);

            if ($numrow > 0) {
                foreach ($rs as $row) {
                    $arr_phonecode[] = $row->phonecode;
                }
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['phonecode'] = $arr_phonecode;
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function GetBookingTable(Request $request)
    {
        foreach ($request->input() as $k => $v) {
            $response[$k] = $v;
        }

        //return response()->json($response);

        $error = array();
        $searchdatetype = $request->input('searchdatetype');
        $searchdate = $request->input('searchdate');
        $tabletimes = $tabletime = array();
        $totalcourts = 7;
        $courts = $court = array();
        $bookings = array();

        for ($i = 1; $i <= $totalcourts; $i++) {
            $courts[] = 'Court ' . $i;
        }

        $response['courts'] = $courts;

        $timeopen = '08:00';
        $timeclose = '24:00';

        $period = new \DatePeriod(
            new \DateTime($timeopen),
            new \DateInterval('PT1H'),
            new \DateTime($timeclose)
        );

        foreach ($period as $k => $date) {
            if ($k % 2 == 0) {

                if ($k > 0) {
                    $tabletime['timeto'] = $date->format("ga");
                    $tabletimes[] = $tabletime;
                    $tabletime = array();
                    $tabletime['timefrom'] = $date->format("ga");
                    $tabletime['timeformat'] = $date->format("H:i");
                } else {
                    $tabletime['timefrom'] = $date->format("ga");
                    $tabletime['timeformat'] = $date->format("H:i");
                }
            } else {
                $tabletime['timeto'] = $date->format("ga");

                $tabletimes[] = $tabletime;
                $tabletime = array();
                $tabletime['timefrom'] = $date->format("ga");
                $tabletime['timeformat'] = $date->format("H:i");
            }
        }

        $tabletime['timeto'] = '12 AM';

        $tabletimes[] = $tabletime;

        $response['tabletimes'] = $tabletimes;

        if ($searchdatetype) {
            if ($searchdatetype == 'yesterday') {
                $searchdate = date("Y-m-d", strtotime('-1 day', strtotime(date('Y-m-d'))));
            } elseif ($searchdatetype == 'tomorrow') {
                $searchdate = date("Y-m-d", strtotime('+1 day', strtotime(date('Y-m-d'))));
            } else {
                $searchdate = date('Y-m-d');
            }
        } else {
            if (!$searchdate) {
                $searchdate = date('Y-m-d');
            }
        }

        try {
            $rs = DB::select("select a.id, a.fullname, DATE_FORMAT(a.time_start,'%H:%i') as time_start, ifnull(a.duration,0) as duration, a.court
            from booking a 
            where a.rec_type > 0 
            and a.date = ?
            order by a.date, a.time_start, a.court
            ", [$searchdate]);
            $numrow = count($rs);

            if ($numrow > 0) {
                $response['bookingtable'] = $rs;
                foreach ($rs as $row) {
                    $id = $row->id;
                    $fullname = $row->fullname;
                    $bookingstarttime = $row->time_start;
                    $duration = $row->duration;
                    $court = $row->court;

                    $unix_starttime = strtotime($bookingstarttime);

                    $booking = array();

                    $bookingstarttime = date('g:ia', $unix_starttime);
                    $bookingendTime = date("g:ia", strtotime('+' . $duration . ' minutes', $unix_starttime));
                    $currentstarttime = date('g:ia', $unix_starttime);

                    // bookingstarttime = booking's datetime
                    // tablestarttime = table display datetime
                    // currentstarttime = current looping datetime

                    $noofhours = $duration / 60;

                    $current_unix_starttime = $unix_starttime;

                    while ($noofhours > 0) {
                        $multiplier = 1;

                        if (date('i', $current_unix_starttime) > 0 || $noofhours < 1) {
                            $multiplier = 0.5;
                        }

                        $halfhour = $multiplier == 1 ? false : true;

                        $minutes = 60 * $multiplier;
                        $currentendtime = date("H:i", strtotime('+' . $minutes . ' minutes', $current_unix_starttime));

                        $current_unix_starttime = strtotime($currentendtime);

                        $booking['id'] = $id;
                        $booking['multiplier'] = $multiplier;
                        $booking['fullname'] = $fullname;
                        $booking['court'] = $court;
                        $booking['duration'] = $duration;
                        $booking['bookingstarttime'] = $bookingstarttime;
                        $booking['bookingendTime'] = $bookingendTime;
                        $booking['currentstarttime'] = $currentstarttime;
                        $booking['currentendtime'] = date("g:ia", strtotime($currentendtime));
                        $booking['tablestarttime'] = date("ga", strtotime($currentstarttime));
                        $booking['halfhour'] = $halfhour;
                        $bookings[] = $booking;

                        $currentstarttime = date("g:ia", strtotime($currentendtime));
                        $noofhours = $noofhours - $multiplier;
                    }
                }
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['bookings'] = $bookings;
            $response['searchdate'] = $searchdate;
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function NewUser(Request $request)
    {
        foreach ($request->input() as $k => $v) {
            $response[$k] = $v;
        }

        return response()->json($response);

        $error = array();
        $userid = Auth::user()->id;
        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $useridmsvg = $request->input('useridmsvg');
        $fullname = $request->input('fullname');
        $email = $request->input('email');
        $phonecode = $request->input('phonecode');
        $phonenumber = $request->input('phonenumber');
        $password = $request->input('password');
        $confirmpassword = $request->input('confirmpassword');
        $user_status = $request->input('user_status');

        if ($useridmsvg) {
            if (strlen($useridmsvg) >= 1 && strlen($useridmsvg) <= 100) {
                try {
                    $rs = DB::select("select * 
                        from Users a 
                        where a.rec_type > 0 
                        and a.username = ?
                        ", [$useridmsvg]);
                    $numrow = count($rs);

                    if ($numrow > 0) {
                        $error['useridmsvg'] = 'User ID (MSVG) already exists';
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['useridmsvg'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
                }
            } else {
                $error['useridmsvg'] = 'Invalid User ID (MSVG) format';
            }
        } else {
            $error['useridmsvg'] = 'User ID (MSVG) is required';
        }

        if ($phonecode) {
            try {
                $rs = DB::select("select * 
                from Country a 
                where a.rec_type > 0 
                and a.status = 'Active'
                and a.phonecode = ?
                ", [$phonecode]);
                $numrow = count($rs);

                if ($numrow > 0) { } else {
                    $error['phonenumber'] = 'Invalid country code';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['phonenumber'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
            }
        } else {
            $error['phonenumber'] = 'Country Code is required';
        }

        if ($fullname) {
            if (strlen($fullname) >= 1 && strlen($fullname) <= 100) { } else {
                $error['fullname'] = 'Invalid Full Name format';
            }
        } else {
            $error['fullname'] = 'Full Name is required';
        }

        if ($email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error['email'] = 'Invalid email format';
            } else {
                try {
                    $rs = DB::select("select * 
                    from Users a
                    where a.rec_type > 0
                    and a.email = ?
                    ", [$email]);
                    $numrow = count($rs);

                    if ($numrow > 0) {
                        $error['email'] = 'Email already exist';
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['email'] = 'ERR' . __LINE__; // . ': ' . translateLang('Service unavailable') . $ex->getMessage();
                }
            }
        } else {
            $error['email'] = 'Email is required';
        }

        if ($phonenumber) {
            if (strlen($phonenumber) >= 1 && strlen($phonenumber) <= 100) {
                try {
                    $rs = DB::select("select * 
                    from Users a
                    where a.rec_type > 0 
                    and a.phonecode = ?
                    and a.phonenumber = ?
                    ", [$phonecode, $phonenumber]);
                    $numrow = count($rs);

                    if ($numrow > 0) {
                        $error['phonenumber'] = 'Phone Number already exists';
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
                }
            } else {
                $error['phonenumber'] = 'Invalid Phone Number';
            }
        } else {
            $error['phonenumber'] = 'Phone Number is required';
        }

        if ($user_status) {
            if (!in_array($user_status, ['Pending', 'Active'])) {
                $error['user_status'] = 'Invalid status';
            }
        } else {
            $error['user_status'] = 'Status is required';
        }

        if ($password) {
            if (strlen($password) >= 6  && strlen($password) <= 100) { } else {
                $error['password'] = 'Password format is invalid';
            }
        } else {
            $error['password'] = 'Password is required';
        }

        if ($confirmpassword) {
            if (strlen($confirmpassword) >= 6  && strlen($confirmpassword) <= 100) {
                if ($password != $confirmpassword) {
                    $error['confirmpassword'] = 'Confirm Password does not match';
                }
            } else {
                $error['confirmpassword'] = 'Confirm Password format is invalid';
            }
        } else {
            $error['confirmpassword'] = 'Confirm Password is required';
        }

        if (empty($error)) {
            try {
                $insert = DB::insert("insert into Users (referral_code,fullname,username,email,phonecode,phonenumber,password,status,rec_type,created_by,created_date,created_location,updated_by,updated_date,updated_location) values (?,?,?,?,?,?,?,?,1,?,NOW(),?,?,NOW(),?)", [$phonecode . $phonenumber, $fullname, $useridmsvg, $email, $phonecode, $phonenumber, $password, $user_status, $userid, $userip, $userid, $userip]);

                if ($insert) { } else {
                    $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('Service unavailable');
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('Service unavailable') . $ex->getMessage();
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] = 'New user created successfully';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] = (isset($error['message'])) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function EditUser(Request $request)
    {
        foreach ($request->input() as $k => $v) {
            $response[$k] = $v;
        }

        // return response()->json($response);

        $error = array();
        $userip = $request->ip();
        $userid = Auth::user()->id;
        $userfullname = Auth::user()->full_name;
        $user_id = $request->input('user_id');
        $username = $request->input('username');
        $phonecode = $request->input('phonecode');
        $phonenumber = $request->input('phonenumber');
        $fullname = $request->input('fullname');
        $email = $request->input('email');
        $password = $request->input('password');
        $user_status = $request->input('user_status');

        if ($phonecode) {
            try {
                $rs = DB::select("select * 
                from Country a 
                where a.rec_type > 0 
                and a.status = 'Active'
                and a.phonecode = ?
                ", [$phonecode]);
                $numrow = count($rs);

                if ($numrow > 0) { } else {
                    $error['phonenumber'] = 'Invalid country code';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['phonenumber'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
            }
        } else {
            $error['phonenumber'] = 'Country Code is required';
        }

        if ($username) {
            if (strlen($username) >= 1 && strlen($username) <= 100) {
                try {
                    $rs = DB::select("select * 
                        from Users a 
                        where a.rec_type > 0 
                        and a.username = ?
                        and a.id <> ?
                        ", [$username, $user_id]);
                    $numrow = count($rs);

                    if ($numrow > 0) {
                        $error['username'] = 'User ID (MSVG) already exists';
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['username'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
                }
            } else {
                $error['username'] = 'Invalid User ID (MSVG) format';
            }
        } else {
            $error['username'] = 'User ID (MSVG) is required';
        }

        if ($fullname) {
            if (strlen($fullname) >= 1 && strlen($fullname) <= 100) { } else {
                $error['fullname'] = 'Invalid Full Name format';
            }
        } else {
            $error['fullname'] = 'Full Name is required';
        }

        if ($email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error['email'] = 'Invalid email format';
            } else {
                try {
                    $rs = DB::select("select * 
                    from Users a
                    where a.email = ?
                    and a.id <> ?
                    ", [$email, $user_id]);
                    $numrow = count($rs);

                    if ($numrow > 0) {
                        $error['email'] = 'Email already exist';
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['email'] = 'ERR' . __LINE__; // . ': ' . translateLang('Service unavailable') . $ex->getMessage();
                }
            }
        } else {
            $error['email'] = 'Email is required';
        }

        if ($phonenumber) {
            if (strlen($phonenumber) >= 1 && strlen($phonenumber) <= 100) {
                try {
                    $rs = DB::select("select * 
                    from Users a
                    where a.rec_type > 0 
                    and a.phonecode = ?
                    and a.phonenumber = ?
                    and a.id <> ?
                    ", [$phonecode, $phonenumber, $user_id]);
                    $numrow = count($rs);

                    if ($numrow > 0) {
                        $error['phonenumber'] = 'Phone Number already exists';
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
                }
            } else {
                $error['phonenumber'] = 'Invalid Phone Number';
            }
        } else {
            $error['phonenumber'] = 'Phone Number is required';
        }

        if ($user_id) {
            try {
                $rs = DB::select("select a.id, a.username, a.fullname, a.phonecode, a.phonenumber, a.referral_by, a.status, a.created_date
                from Users a
                where a.rec_type > 0
                and a.id = ?", [$user_id]);
                $numrow = count($rs);

                if ($numrow == 1) {
                    foreach ($rs as $row) {
                        $userstatus = $row->status;

                        if ($userstatus != 'Active' && $user_status == 'Active') {
                            $this->SetLogs($userfullname, 'Approved ' . $fullname . ' account', $userip);
                        }
                    }
                } else {
                    $error['user_id'] = 'Invalid user';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['user_id'] = 'ERR' . __LINE__; // . ': Service unavailable';
            }
        } else {
            $error['user_id'] = 'User ID is required';
        }

        if ($user_status) {
            if (!in_array($user_status, ['Pending', 'Active'])) {
                $error['user_status'] = 'Invalid status';
            }
        } else {
            $error['user_status'] = 'Status is required';
        }

        if (empty($error)) {
            try {
                $update = DB::update("update Users
                set username = ?,
                fullname = ?,
                phonecode = ?,
                phonenumber = ?,
                referral_code = ?,
                email = ?,
                status = ?,
                rec_type = rec_type + 1,
                updated_by = ?,
                updated_date = NOW(),
                updated_location = ?
                where rec_type > 0
                and id = ?", [$username, $fullname, $phonecode, $phonenumber, $phonecode . $phonenumber, $email, $user_status, $userid, $userip, $user_id]);

                if ($update) { } else {
                    $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable'; // . $ex->getMessage();
            }
        }

        if (empty($error)) {
            if ($password) {
                if (strlen($password) >= 6  && strlen($password) <= 100) {
                    try {
                        $update = DB::update("update Users
                        set password = ?,
                        updated_by = ?,
                        updated_date = NOW(),
                        updated_location = ?
                        where rec_type > 0
                        and id = ?", [$password, $userid, $userip, $user_id]);

                        if ($update) {
                            $this->SetLogs($userfullname, 'Changed ' . $fullname . ' password', $userip);
                        } else {
                            $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable';
                        }
                    } catch (\Illuminate\Database\QueryException $ex) {
                        $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable'; // . $ex->getMessage();
                    }
                } else {
                    $error['password'] = 'Password format is invalid';
                }
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] = 'User updated successfully';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function EditBookingData(Request $request) {

        $request->validate([
             $error = array(),
            'fullname'=>'required',
            'phonecode'=>'required',  
            'phonenumber'=>'required',  
            'date'=>'required',  
            'time_start'=>'required',  
            'duration'=>'required',  
            'court'=>'required',  
            'payment'=>'required',  
            'booking_status'=>'required',  
            'pricetype'=>'required',  
            'amount'=>'required',  
            'type'=>'required'
        ]);
          
        if ($request->input('booking_id')) {

            $updating = DB::table('booking')
                    ->where('id', $request->input('booking_id'))
                    ->update([
                        'fullname'=>$request->input('fullname'),
                        'phonecode'=>$request->input('phonecode'),
                        'phonenumber'=>$request->input('phonenumber'),
                        'date'=>$request->input('date'),
                        'time_start'=>$request->input('time_start'),
                        'duration'=>$request->input('duration'),
                        'court'=>$request->input('court'),
                        'payment'=>$request->input('payment'),
                        'status'=>$request->input('booking_status'),
                        'pricetype'=>$request->input('pricetype'),
                        'amount'=>$request->input('amount'),
                        'type'=>$request->input('type')
                        
                    ]);

                    if (empty($error)) {
                        $response['status'] = 'success';
                        $response['message'] = 'Booking updated successfully';
                    } else {
                        $response['status'] = 'error';
                        $response['error'] =  $error;
                        $response['message'] =  isset($error['message']) ? $error['message'] : 'Please check the form';
                    }

                    return response()->json($response);

        } else {

            $error = array();
            $userid = Auth::user()->id;
            $userip = $request->ip();
            $username = Auth::user()->full_name;
            $fullname = $request->input('fullname');
            $phonecode = $request->input('phonecode');
            $phonenumber = $request->input('phonenumber');
            $date = $request->input('date');
            $time_start = $request->input('time_start');
            $duration = $request->input('duration');
            $court = $request->input('court');
            $payment = $request->input('payment');
            $booking_status = $request->input('booking_status');
            $pricetype = $request->input('pricetype');
            $amount = $request->input('amount');
            $type = $request->input('type');

            if (empty($error)) {
                try {
                    $insert = DB::insert("insert into Booking (username,fullname,phonecode,phonenumber,date,time_start,duration,court,payment,status,pricetype,amount,type,rec_type,created_by,created_date,created_location,updated_by,updated_date,updated_location) values (?,?,?,?,?,?,?,?,?,?,?,?,?,1,?,NOW(),?,?,NOW(),?)", [$username, $fullname, $phonecode, $phonenumber, $date, $time_start, $duration, $court, $payment, $booking_status, $pricetype, $amount, $type, $userid, $userip, $userid, $userip]);
    
                    if ($insert) { } else {
                        $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('Service unavailable');
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('Service unavailable') . $ex->getMessage();
                }
            }
    
            if (empty($error)) {
                $response['status'] = 'success';
                $response['message'] = 'New booking created successfully';
            } else {
                $response['status'] = 'error';
                $response['error'] =  $error;
                $response['message'] = (isset($error['message'])) ? $error['message'] : 'Please check the form';
            }
                    return response()->json($response);
                }
            }

    public function DeleteUser(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        // return response()->json($response);

        $error = array();
        $userid = Auth::user()->id;
        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $user_id = $request->input('user_id');
        $fullname = '';

        if ($user_id) {
            try {
                $rs = DB::select("select a.id, a.username, a.fullname, a.phonecode, a.phonenumber, a.referral_by, a.status, a.created_date
                from Users a
                where a.rec_type > 0
                and a.id = ?", [$user_id]);
                $numrow = count($rs);

                if ($numrow == 1) {
                    foreach ($rs as $row) {
                        $fullname = $row->fullname;
                    }
                } else {
                    $error['message'] =  'Invalid user';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable';
            }
        } else {
            $error['message'] = 'User ID is required';
        }

        if (empty($error)) {
            try {
                $update = DB::update("update Users
                set rec_type = 0,
                updated_by = ?,
                updated_date = NOW()
                where rec_type > 0
                and id = ?", [$userid, $user_id]);

                if ($update) { } else {
                    $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable'; // . $ex->getMessage();
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] = 'User deleted successfully';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function DeleteBooking(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        // return response()->json($response);

        $error = array();
        $userid = Auth::user()->id;
        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $booking_id = $request->input('booking_id');
        $fullname = '';

        if ($booking_id) {
            try {
                $rs = DB::select("select a.id, a.fullname, a.phonecode, a.phonenumber, a.duration, a.court, a.referral_by, a.status, a.created_date
                from Booking a
                where a.rec_type > 0
                and a.id = ?", [$booking_id]);
                $numrow = count($rs);

                if ($numrow == 1) {
                    foreach ($rs as $row) {
                        $fullname = $row->fullname;
                    }
                } else {
                    $error['message'] =  'Invalid booking';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable';
            }
        } else {
            $error['message'] = 'Booking ID is required';
        }

        if (empty($error)) {
            try {
                $update = DB::update("update Booking
                set rec_type = 0,
                updated_by = ?,
                updated_date = NOW()
                where rec_type > 0
                and id = ?", [$userid, $booking_id]);

                if ($update) { } else {
                    $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable'; // . $ex->getMessage();
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] = 'Booking deleted successfully';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function DeleteImage1(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        // return response()->json($response);

        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $error = array();
        $user_id = $request->input('user_id');
        $fullname = '';

        if ($user_id) {
            try {
                $rs = DB::select("select a.id, a.fullname, a.email, a.phonecode, a.phonenumber, ifnull(a.image1,'') as image1, ifnull(a.image2,'') as image2, a.status, a.created_date
                from Users a
                where a.rec_type > 0
                and a.id = ?
                and ifnull(a.image1,'') <> ''
                ", [$user_id]);
                $numrow = count($rs);

                if ($numrow == 1) {
                    foreach ($rs as $row) {
                        $fullname = $row->fullname;
                    }
                } else {
                    $error['message'] =  'Invalid user';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable';
            }
        } else {
            $error['message'] =  'User ID is required';
        }

        if (empty($error)) {
            try {
                $update = DB::update("update Users
                set image1 = ''
                where rec_type > 0
                and id = ?
                and ifnull(image1,'') <> ''
                ", [$user_id]);

                if ($update) { }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': ' . $ex->getMessage();
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] = 'ID Front Page deleted successful';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] = (isset($error['message'])) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function DeleteImage2(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        // return response()->json($response);

        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $error = array();
        $user_id = $request->input('user_id');
        $fullname = '';

        if ($user_id) {
            try {
                $rs = DB::select("select a.id, a.fullname, a.email, a.phonecode, a.phonenumber, ifnull(a.image1,'') as image1, ifnull(a.image2,'') as image2, a.status, a.created_date
                from Users a
                where a.rec_type > 0
                and a.id = ?
                and ifnull(a.image2,'') <> ''
                ", [$user_id]);
                $numrow = count($rs);

                if ($numrow == 1) {
                    foreach ($rs as $row) {
                        $fullname = $row->fullname;
                    }
                } else {
                    $error['message'] =  'Invalid user';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable';
            }
        } else {
            $error['message'] =  'User ID is required';
        }

        if (empty($error)) {
            try {
                $update = DB::update("update Users
                set image2 = ''
                where rec_type > 0
                and id = ?
                and ifnull(image2,'') <> ''
                ", [$user_id]);

                if ($update) { }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': ' . $ex->getMessage();
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] = 'Bank Card Front deleted successful';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] = (isset($error['message'])) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function UploadImage1(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response['input_' . $k] = $v;
        // }

        // foreach ($request->file() as $k => $v) {
        //     $response['file_' . $k] = $v;
        // }

        // return response()->json($response);

        $userid = Auth::user()->id;
        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $error = array();
        $user_id = $request->input('user_id');
        $croppedImage = $request->file('croppedImage');
        $fullname = '';

        if ($user_id) {
            try {
                $rs = DB::select("select a.id, a.fullname, a.email, a.phonecode, a.phonenumber, ifnull(a.image1,'') as image1, ifnull(a.image2,'') as image2, a.status, a.created_date
                from Users a
                where a.rec_type > 0
                and a.id = ?
                and ifnull(a.image1,'') = ''
                ", [$user_id]);
                $numrow = count($rs);

                if ($numrow == 1) {
                    foreach ($rs as $row) {
                        $fullname = $row->fullname;
                    }
                } else {
                    $error['message'] =  'Invalid user';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable';
            }
        } else {
            $error['message'] =  'User ID is required';
        }

        if (empty($error)) {
            if ($croppedImage) {
                try {
                    if (file_exists('./../public')) {
                        $dest_folder_base = './../../laravel-admin/public';
                    } else {
                        $dest_folder_base = './../example.com';
                    }

                    $dest_folder = '/images/users';

                    if (!file_exists($dest_folder_base . $dest_folder)) {
                        mkdir($dest_folder_base . $dest_folder);
                    }

                    $dest_path = $dest_folder_base . $dest_folder;

                    $filename = Str::random(mt_rand(100, 190)) . '.png';

                    try {
                        $file = Image::make($croppedImage);
                        $file->save($dest_path . '/' . $filename);

                        $update = DB::update("update Users
                        set image1 = ?
                        where rec_type > 0
                        and id = ?
                        and ifnull(image1,'') = ''
                        ", [$filename, $user_id]);

                        if ($update) { }
                    } catch (\Illuminate\Database\QueryException $ex) {
                        $error['message'] = 'ERR' . __LINE__; // . ': ' . $ex->getMessage();
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'ERR' . __LINE__;
                }
            } else {
                $error['message'] = 'ERR' . __LINE__;
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] = 'ID Front Page upload successful';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] = (isset($error['message'])) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function UploadImage2(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response['input_' . $k] = $v;
        // }

        // foreach ($request->file() as $k => $v) {
        //     $response['file_' . $k] = $v;
        // }

        // return response()->json($response);

        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $error = array();
        $user_id = $request->input('user_id');
        $croppedImage = $request->file('croppedImage');
        $fullname = '';

        if ($user_id) {
            try {
                $rs = DB::select("select a.id, a.fullname, a.email, a.phonecode, a.phonenumber, ifnull(a.image1,'') as image1, ifnull(a.image2,'') as image2, a.status, a.created_date
                from Users a
                where a.rec_type > 0
                and a.id = ?
                and ifnull(a.image2,'') = ''
                ", [$user_id]);
                $numrow = count($rs);

                if ($numrow == 1) {
                    foreach ($rs as $row) {
                        $fullname = $row->fullname;
                    }
                } else {
                    $error['message'] =  'Invalid user';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable';
            }
        } else {
            $error['message'] =  'User ID is required';
        }

        if (empty($error)) {
            if ($croppedImage) {
                try {
                    if (file_exists('./../public')) {
                        $dest_folder_base = './../../laravel-admin/public';
                    } else {
                        $dest_folder_base = './../example.com';
                    }

                    $dest_folder = '/images/users';

                    if (!file_exists($dest_folder_base . $dest_folder)) {
                        mkdir($dest_folder_base . $dest_folder);
                    }

                    $dest_path = $dest_folder_base . $dest_folder;

                    $filename = Str::random(mt_rand(100, 190)) . '.png';

                    try {
                        $file = Image::make($croppedImage);
                        $file->save($dest_path . '/' . $filename);

                        $update = DB::update("update Users
                    set image2 = ?
                    where rec_type > 0
                    and id = ?
                    and ifnull(image2,'') = ''
                    ", [$filename, $user_id]);

                        if ($update) { }
                    } catch (\Illuminate\Database\QueryException $ex) {
                        $error['message'] = 'ERR' . __LINE__; // . ': ' . $ex->getMessage();
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'ERR' . __LINE__;
                }
            } else {
                $error['message'] = 'ERR' . __LINE__;
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] = 'Bank Card Front upload successful';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] = (isset($error['message'])) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function PrepareEditProfile(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }
        //return response()->json($response);

        $error = array();
        $username = Auth::user()->username;
        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();

        if (empty($error)) {
            $response['status'] = 'success';
            $response['username'] =  $username;
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function ChangePassword(Request $request)
    {
        // foreach ($request->input() as $k => $v) {
        //     $response[$k] = $v;
        // }

        // return response()->json($response);

        $error = array();
        $userid = Auth::user()->id;
        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $oldpassword = $request->input('oldpassword');
        $newpassword = $request->input('newpassword');
        $confirmpassword = $request->input('confirmpassword');

        if ($oldpassword) {
            try {
                $rs = DB::select("select *
                from admins a
                where a.rec_type > 0
                and a.id = ?
                and a.password = ?
                ", [$userid, $oldpassword]);
                $numrow = count($rs);

                if ($numrow != 1) {
                    $error['oldpassword'] = 'Invalid Old Password';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['oldpassword'] = 'ERR' . __LINE__; //. ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
            }
        } else {
            $error['oldpassword'] = 'Old Password is required';
        }

        if ($newpassword) {
            if (strlen($newpassword) >= 6 && strlen($newpassword) <= 100) { } else {
                $error['newpassword'] = 'New Password format is invalid';
            }
        } else {
            $error['newpassword'] = 'New Password is required';
        }

        if ($confirmpassword) {
            if (strlen($confirmpassword) >= 6 && strlen($confirmpassword) <= 100) {
                if ($newpassword != $confirmpassword) {
                    $error['confirmpassword'] = 'Confirm Password does not match';
                }
            } else {
                $error['confirmpassword'] = 'Confirm Password format is invalid';
            }
        } else {
            $error['confirmpassword'] = 'Confirm Password is required';
        }

        if (empty($error)) {
            try {
                $update = DB::update("update admins
                set password = ?,
                updated_by = ?,
                updated_date = NOW()
                where rec_type > 0
                and id = ?
                and password = ?
                ", [$confirmpassword, $userid, $userid, $oldpassword]);

                if ($update) { } else {
                    $error['message'] = 'Unable to change password';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] = 'Password updated successfully';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function DeleteOrder(Request $request)
    {
        foreach ($request->input() as $k => $v) {
            $response[$k] = $v;
        }

        // return response()->json($response);

        $error = array();
        $userid = Auth::user()->id;
        $userfullname = Auth::user()->full_name;
        $userip = $request->ip();
        $orderno = $request->input('order_no');

        try {
            $rs = DB::select("select *
            from Orders a
            where a.order_no = ?
            ", [$orderno]);
            $numrow = count($rs);

            if ($numrow > 0) { } else {
                $error['message'] = 'Invalid order'; // . $ex->getMessage();
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $error['message'] = 'ERR' . __LINE__; // . $ex->getMessage();
        }

        if (empty($error)) {
            try {
                $update = DB::update("update Orders
                set rec_type = 0,
                updated_by = ?,
                updated_date = NOW()
                where rec_type > 0
                and order_no = ?
                ", [$userid, $orderno]);

                if ($update) { } else {
                    $error['message'] = 'Unable to delete order';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] = 'Order deleted successfully';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] =  isset($error['message']) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function insert(Request $request)
    {

        foreach ($request->input() as $k => $v) {
            $response[$k] = $v;
        }

        $error = array();
        $bookingid = Auth::user()->id;
        $username = Auth::user()->full_name;
        $userip = $request->ip();
        $fullname = $request->input('fullname');
        $phonecode = $request->input('phonecode');
        $phonenumber = $request->input('phonenumber');
        $date = $request->input('date');
        $time_start = $request->input('time_start');
        $duration = $request->input('duration');
        $court = $request->input('court');
        $payment = $request->input('payment');
        $booking_status = $request->input('booking_status');
        $pricetype = $request->input('pricetype');
        $amount = $request->input('amount');
        $type = $request->input('type');

        if ($phonecode) {
            try {
                $rs = DB::select("select * 
                from Country a 
                where a.rec_type > 0 
                and a.status = 'Active'
                and a.phonecode = ?
                ", [$phonecode]);
                $numrow = count($rs);

                if ($numrow > 0) { } else {
                    $error['phonenumber'] = 'Invalid country code';
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['phonenumber'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
            }
        } else {
            $error['phonenumber'] = 'Country Code is required';
        }

        if ($fullname) {
            if (strlen($fullname) >= 1 && strlen($fullname) <= 100) { } else {
                $error['fullname'] = 'Invalid Full Name format';
            }
        } else {
            $error['fullname'] = 'Full Name is required';
        }

        if ($phonenumber) {
            if (strlen($phonenumber) >= 1 && strlen($phonenumber) <= 100) {
                try {
                    $rs = DB::select("select * 
                    from Booking a
                    where a.rec_type > 0 
                    and a.phonecode = ?
                    and a.phonenumber = ?
                    ", [$phonecode, $phonenumber]);
                    $numrow = count($rs);

                    if ($numrow > 0) {
                        $error['phonenumber'] = 'Phone Number already exists';
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
                }
            } else {
                $error['phonenumber'] = 'Invalid Phone Number';
            }
        } else {
            $error['phonenumber'] = 'Phone Number is required';
        }

        if ($date) {
            if ($this->DateFormat($date, 'Y-m-d')) {
                $sql_date = $date;
            } else {
                $error['date'] = 'Invalid transaction date';
            }
        } else {
            $error['date'] = 'Date is required';
        }

        if ($time_start) {
            if ($this->DateFormat($time_start, 'H:i')) {
                $sql_time_start = $time_start;
            } else {
                $error['time_start'] = 'Invalid transaction time';
            }
        } else {
            $error['time_start'] = 'Time is required';
        }

        if ($duration) { } else {
            $error['duration'] = 'Duration is required';
        }

        if ($court) {
            if (!in_array($court, ['Court 1', 'Court 2', 'Court 3', 'Court 4', 'Court 5', 'Court 6', 'Court 7'])) {
                $error['court'] = 'Invalid court';
            }
        } else {
            $error['court'] = 'Court is required';
        }

        if ($payment) {
            if (!in_array($payment, ['Paid', 'Unpaid'])) {
                $error['payment'] = 'Invalid payment';
            }
        } else {
            $error['payment'] = 'Payment is required';
        }

        if ($booking_status) {
            if (!in_array($booking_status, ['Pending', 'Active', 'Approved', 'Rejected', 'Cancelled'])) {
                $error['booking_status'] = 'Invalid status';
            }
        } else {
            $error['booking_status'] = 'Status is required';
        }

        if ($pricetype) {
            if (!in_array($pricetype, ['Normal', 'Student-Collage', 'Student-Secondary', 'Student-Primary'])) {
                $error['pricetype'] = 'Invalid price type';
            }
        } else {
            $error['pricetype'] = 'Price type is required';
        }

        if ($amount) {
            if (strlen($amount) >= 1 && strlen($fullname) <= 100) { } else {
                $error['amount'] = 'Invalid amount';
            }
        } else {
            $error['amount'] = 'Amount is required';
        }

        if ($type) {
            if (!in_array($type, ['Standby', 'Reconfirmed', 'Cancelled', 'Cancelled(Absent)'])) {
                $error['type'] = 'Invalid type';
            }
        } else {
            $error['type'] = 'Type is required';
        }

        if (empty($error)) {
            try {
                $insert = DB::insert("insert into Booking (username,fullname,phonecode,phonenumber,date,time_start,duration,court,payment,status,pricetype,amount,type,rec_type,created_by,created_date,created_location,updated_by,updated_date,updated_location) values (?,?,?,?,?,?,?,?,?,?,?,?,?,1,?,NOW(),?,?,NOW(),?)", [$username, $fullname, $phonecode, $phonenumber, $date, $time_start, $duration, $court, $payment, $booking_status, $pricetype, $amount, $type, $bookingid, $userip, $bookingid, $userip]);

                if ($insert) { } else {
                    $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('Service unavailable');
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__; // . ': ' . translateLang('Service unavailable') . $ex->getMessage();
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] = 'New booking created successfully';

            if ($payment == 'Paid') {
                $response['url'] = 'printbooking/{id}' . $bookingid;
            }
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] = (isset($error['message'])) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    public function index($id) {
        $bookings = DB::select("SELECT * FROM booking WHERE id = '$id' ");
        return view('printbooking')->with('bookings', $bookings);
        }

    public function GetBookingData(Request $request)
        {
            foreach ($request->input() as $k => $v) {
                 $response[$k] = $v;
                 
        }
    
            //return response()->json($response);
    
            $error = array();
            $dataid = $request->input('dataid');
            $booking = array();
    
            try {
                $rs = DB::select("select a.id, a.fullname, a.phonecode, a.phonenumber, a.date, DATE_FORMAT(a.time_start,'%H:%i') as time_start, ifnull(a.duration, 0) as duration, a.court, a.status, a.payment, a.pricetype, a.amount, a.type
                from booking a
                where a.rec_type > 0
                and a.id = ?
                ", [$dataid]);
                $numrow = count($rs);
    
                    if ($numrow > 0) {
                        foreach ($rs as $row) {
                            $booking_id = $row->id;
                            $fullname = $row->fullname;
                            $phonecode = $row->phonecode;
                            $phonenumber = $row->phonenumber;
                            $date = $row->date;
                            $time_start = $row->time_start;
                            $duration = $row->duration;
                            $court = $row->court;
                            $booking_status = $row->status;
                            $payment = $row->payment;
                            $pricetype = $row->pricetype;
                            $amount = $row->amount;
                            $type = $row->type;
    
                            $booking['id'] = $booking_id;
                            $booking['fullname'] = $fullname;
                            $booking['phonecode'] = $phonecode;
                            $booking['phonenumber'] = $phonenumber;
                            $booking['date'] = $date;
                            $booking['timestart'] = $time_start;
                            $booking['duration'] =  $duration;
                            $booking['court'] =  $court;
                            $booking['status'] = $booking_status;
                            $booking['payment'] = $payment;
                            $booking['pricetype'] = $pricetype;
                            $booking['amount'] = $amount;
                            $booking['type'] = $type;
                        }
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'ERR' . __LINE__; // . ': Service unavailable';
                }

                if (empty($error)) {
                    $response['status'] = 'success';
                    $response['booking'] = $booking;
                } else {
                    $response['status'] = 'error';
                    $response['error'] =  $error;
                    $response['message'] =  isset($error['message']) ? $error['message'] : 'Error';
                }
        
                return response()->json($response);
            }
}
