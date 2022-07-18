<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    var $error;
    var $message;

    function fn_DateFormat($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    function FormatAmount($input)
    {
        return str_replace(",", "", str_replace("$", "", $input));
    }

    function ImportUsers(Request $request)
    {
        $userid = Auth::user()->id;
        $userip = $request->ip();
        $batch_no = $source_path = '';
        $col_user = $phonenumbers = array();

        foreach ($request->input() as $k => $v) {
            $response['input_' . $k] = $v;
        }

        foreach ($request->file() as $k => $v) {
            $response['file_' . $k] = $v;
        }

        if ($request->file('import_file')) { } else {
            $error['message'] = 'File is required';
        }

        if (empty($error)) {
            $allowed_extensions = array("xls", "xlsx", "xlm", "xla", "xlc", "xlt", "xlw");

            $extension = $request->file('import_file')->getClientOriginalExtension();

            if (!in_array($extension, $allowed_extensions)) {
                $error['message'] = 'Invalid file type: .' . $extension;
            }
        }

        if (empty($error)) {
            if ($request->file('import_file')->getRealPath() != '') {
                $source_path = $request->file('import_file')->getRealPath();
                $response['source_path'] = $source_path;
            } else {
                $error['message'] = 'ERR' . __LINE__ . ': Service unavailable';
            }
        }

        if (empty($error)) {
            // Import Users worksheet
            try {
                $col_user = (new FastExcel)->sheet(1)->import($source_path);
                $response['users'] = $col_user;
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__ . ': Service unavailable';
            }
        }

        if (empty($error)) {
            if (count($col_user) > 0) {
                $i = 0;
                foreach ($col_user as $k => $v) {
                    if (isset($v['Username']) && isset($v['Password']) && isset($v['Phone Number']) && isset($v['Bank Account Holder Name']) && isset($v['Bank Name']) && isset($v['Bank Branch']) && isset($v['Bank Account Number']) && isset($v['Referral By']) && isset($v['Security Pin']) && isset($v['Is Active'])) {
                        $username = $v['Username'];
                        $password = $v['Password'];
                        $phonenumber = $v['Phone Number'];
                        $bankaccountholdername = $v['Bank Account Holder Name'];
                        $bankname = $v['Bank Name'];
                        $bankbranch = $v['Bank Branch'];
                        $bankaccountno = $v['Bank Account Number'];
                        $referral_by = $v['Referral By'];
                        $securitypin = $v['Security Pin'];

                        $i++;

                        if ($username) {
                            if (strlen($username) > 0 && strlen($username) <= 100) { } else {
                                $error['message'] = 'Line ' . $i . ': Username invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Username is required';
                        }

                        if ($password) {
                            if (strlen($password) > 0 && strlen($password) <= 100) { } else {
                                $error['message'] = 'Line ' . $i . ': Password invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Password is required';
                        }

                        if ($phonenumber) {
                            if (strlen($phonenumber) > 0 && strlen($phonenumber) <= 100) { } else {
                                $error['message'] = 'Line ' . $i . ': Phone Number invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Phone Number is required';
                        }

                        // if ($bankaccountholdername) {
                        //     if (strlen($bankaccountholdername) > 0 && strlen($bankaccountholdername) <= 100) { } else {
                        //         $error['message'] = 'Line ' . $i . ': Bank Account Holder Name is invalid';
                        //     }
                        // } else {
                        //     $error['message'] = 'Line ' . $i . ': Bank Account Holder Name is required';
                        // }

                        // if ($bankname) {
                        //     if (strlen($bankname) > 0 && strlen($bankname) <= 100) { } else {
                        //         $error['message'] = 'Line ' . $i . ': Bank Name is invalid';
                        //     }
                        // } else {
                        //     $error['message'] = 'Line ' . $i . ': Bank Name is required';
                        // }

                        // if ($bankbranch) {
                        //     if (strlen($bankbranch) > 0 && strlen($bankbranch) <= 100) { } else {
                        //         $error['message'] = 'Line ' . $i . ': Bank Branch is invalid';
                        //     }
                        // } else {
                        //     $error['message'] = 'Line ' . $i . ': Bank Branch is required';
                        // }

                        // if ($bankaccountno) {
                        //     if (strlen($bankaccountno) > 0 && strlen($bankaccountno) <= 100) { } else {
                        //         $error['message'] = 'Line ' . $i . ': Bank Account Number is invalid';
                        //     }
                        // } else {
                        //     $error['message'] = 'Line ' . $i . ': Bank Account Number is required';
                        // }

                        if ($referral_by) {
                            if (strlen($referral_by) > 0 && strlen($referral_by) <= 100) { } else {
                                $error['message'] = 'Line ' . $i . ': Referral By invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Referral By is required';
                        }

                        if ($securitypin) {
                            if (strlen($securitypin) > 0 && strlen($securitypin) <= 100) { } else {
                                $error['message'] = 'Line ' . $i . ': Security Pin invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Security Pin is required';
                        }
                    } else {
                        $error['message'] = 'Invalid template';
                    }

                    if (empty($error)) { } else {
                        break;
                    }
                }
            } else {
                $error['message'] = 'Worksheet is empty';
            }
        }

        if (empty($error)) {
            try {
                $rs = DB::select("select *
                from Users a
                where a.rec_type > 0
                ");
                $numrow = count($rs);

                if ($numrow > 0) {
                    foreach ($rs as $row) {
                        $phonenumber = $row->phonenumber;

                        if (!in_array($phonenumber, $phonenumbers)) {
                            array_push($phonenumbers, $phonenumber);
                        }
                    }
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__ . ': Service unavailable';
            }
        }

        if (empty($error)) {
            // Insert Users
            $i = 0;
            $batch_no = date('YmdHis') . Str::random(10);
            $amount = 0;

            try {
                $rs = DB::select("select *
                    from WebSettings a
                    where a.rec_type > 0
                    and a.type = 'Free Gold Coin During Register'
                    ");
                $numrow = count($rs);

                if ($numrow > 0) {
                    foreach ($rs as $row) {
                        $amount = $row->value;
                        $description = $row->description;
                    }
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__ . ': ' . translateLang('NOSERVICE'); // . $ex->getMessage();
            }

            foreach ($col_user as $k => $v) {
                $username = $v['Username'];
                $password = $v['Password'];
                $phonenumber = $v['Phone Number'];
                $bankaccountholdername = $v['Bank Account Holder Name'];
                $bankname = $v['Bank Name'];
                $bankbranch = $v['Bank Branch'];
                $bankaccountno = $v['Bank Account Number'];
                $referral_by = $v['Referral By'];
                $securitypin = $v['Security Pin'];
                $isactive = $v['Is Active'];

                if ($isactive == 'Y') {
                    $isactive = 'Active';
                } else {
                    $isactive = 'Inactive';
                }

                $i++;

                try {
                    $update = DB::update("update Users 
                    set rec_type = 0,
                    batch_no = ?,
                    updated_by = ?,
                    updated_date = NOW(),
                    updated_location = ?
                    where rec_type > 0
                    and username = ?", [$batch_no, $userid, $userip, $username]);

                    if ($update) { }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                    break; //$ex->getMessage();
                }

                $usertype = '';
                if (in_array($phonenumber, $phonenumbers)) {
                    $usertype = 'Sub';
                } else {
                    array_push($phonenumbers, $phonenumber);
                    $usertype = 'Main';
                }

                try {
                    $insert = DB::insert("insert into Users (batch_no,type,username,password,securitypin,phonenumber,referral_code,referral_by,status,rec_type,created_by,created_date,updated_by,updated_date) values (?,?,?,?,?,?,?,?,?,1,?,NOW(),?,NOW())", [$batch_no, $usertype, $username, $password, $securitypin, $phonenumber, $username, $referral_by, $isactive, $userid, $userid]);

                    if (!$insert) {
                        $error['message'] = 'Error' . __LINE__ . ' line ' . $i . ': Unable to add this row into database, please contact support for assistance.';
                        break;
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                    break; //$ex->getMessage();
                }

                try {
                    $update = DB::update("update UserBanks 
                    set rec_type = 0,
                    batch_no = ?,
                    updated_by = ?,
                    updated_date = NOW(),
                    updated_location = ?
                    where rec_type > 0
                    and username = ?", [$batch_no, $userid, $userip, $username]);

                    if ($update) { }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                    break; //$ex->getMessage();
                }

                try {
                    $insert = DB::insert("insert into UserBanks (batch_no,username,bank_name,bank_branch,bank_account_no,bank_account_holder,isDefault,status,rec_type,created_by,created_date,created_location,updated_by,updated_date,updated_location) values (?,?,?,?,?,?,1,'Active',1,?,NOW(),?,?,NOW(),?)", [$batch_no, $username, $bankname, $bankbranch, $bankaccountno, $bankaccountholdername, $userid, $userip, $userid, $userip]);

                    if (!$insert) {
                        $error['message'] = 'Error' . __LINE__ . ' line ' . $i . ': Unable to add this row into database, please contact support for assistance.';
                        break;
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                    break; //$ex->getMessage();
                }

                if ($amount > 0) {
                    try {
                        $insert = DB::insert("insert into Transactions (username,wallet_type,type,description,amount,isAvailable,status,rec_type,created_date,created_location,updated_date,updated_location) values (?,'Gold Coin','Free Gold Coin',?,?,1,'Active',1,NOW(),?,NOW(),?)", [$username, $description, $amount, $userip, $userip]);

                        if ($insert) { } else {
                            $error['message'] = 'Error' . __LINE__ . ' line ' . $i . ': Unable to add this row into database, please contact support for assistance.';
                            break;
                        }
                    } catch (\Illuminate\Database\QueryException $ex) {
                        $error['message'] = 'Error' . __LINE__ . ' line ' . $i . ': Unable to add this row into database, please contact support for assistance.';
                        break;
                    }
                }

                try {
                    $insert = DB::insert("insert into UserRatings (username,description,amount,status,rec_type,created_date,updated_date) values (?,?,?,'Active',1,NOW(),NOW())", [$username, 'Activate Account', 60]);

                    if (!$insert) {
                        $error['message'] = 'Error' . __LINE__ . ' line ' . $i . ': Unable to add this row into database, please contact support for assistance.';
                        break;
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' line ' . $i . ': Unable to add this row into database, please contact support for assistance.';
                    break;
                }

                try {
                    $insert = DB::insert("insert into UserRatings (username,description,amount,status,rec_type,created_date,updated_date) values (?,?,?,'Active',1,NOW(),NOW())", [$referral_by, 'Activate Account Referral Bonus', 10]);

                    if (!$insert) {
                        $error['message'] = 'Error' . __LINE__ . ' line ' . $i . ': Unable to add this row into database, please contact support for assistance.';
                        break;
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' line ' . $i . ': Unable to add this row into database, please contact support for assistance.';
                    break;
                }
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] =  'Successfully imported all users';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] = (isset($error['message'])) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    function ImportSellers(Request $request)
    {
        $userid = Auth::user()->id;
        $userip = $request->ip();
        $batch_no = $source_path = '';
        $col_seller = $arr_usernames = $arr_phonenumbers = array();

        foreach ($request->input() as $k => $v) {
            $response['input_' . $k] = $v;
        }

        foreach ($request->file() as $k => $v) {
            $response['file_' . $k] = $v;
        }

        if ($request->file('fileseller')) { } else {
            $error['message'] = 'File is required';
        }

        if (empty($error)) {
            $allowed_extensions = array("xls", "xlsx", "xlm", "xla", "xlc", "xlt", "xlw");

            $extension = $request->file('fileseller')->getClientOriginalExtension();

            if (!in_array($extension, $allowed_extensions)) {
                $error['message'] = 'Invalid file type: .' . $extension;
            }
        }

        if (empty($error)) {
            if ($request->file('fileseller')->getRealPath() != '') {
                $source_path = $request->file('fileseller')->getRealPath();
                $response['source_path'] = $source_path;
            } else {
                $error['message'] = 'ERR' . __LINE__ . ': Service unavailable';
            }
        }

        if (empty($error)) {
            // Import Sellers worksheet
            try {
                $col_seller = (new FastExcel)->sheet(1)->import($source_path);
                $response['sellers'] = $col_seller;
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__ . ': Service unavailable';
            }
        }

        if (empty($error)) {
            try {
                $rs = DB::select("select a.username, a.phonenumber
                from Users a
                where a.rec_type > 0
                and ifnull(a.isCompany,0) = 0");
                $numrow = count($rs);

                if ($numrow > 0) {
                    foreach ($rs as $row) {
                        $username = $row->username;
                        $phonenumber = $row->phonenumber;

                        if (!in_array($username, $arr_usernames)) {
                            array_push($arr_usernames, $username);
                        }

                        if (!in_array($phonenumber, $arr_phonenumbers)) {
                            array_push($arr_phonenumbers, $phonenumber);
                        }
                    }
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__ . ': Service unavailable';
            }
        }

        if (empty($error)) {
            if (count($col_seller) > 0) {
                $i = 0;
                foreach ($col_seller as $k => $v) {
                    if (isset($v['Seller Username']) && isset($v['Package ID']) && isset($v['Package']) && isset($v['Payment Type']) && isset($v['Seller Phone Number']) && isset($v['Seller Bank Account Holder Name']) && isset($v['Seller Bank Name']) && isset($v['Seller Bank Branch']) && isset($v['Seller Bank Account Number']) && isset($v['Bought Price']) && isset($v['ROI Days']) && isset($v['ROI Rate'])) {
                        $seller_username = $v['Seller Username'];
                        $packageid = $v['Package ID'];
                        $package = $v['Package'];
                        $paymenttype = $v['Payment Type'];
                        $seller_phonenumber = $v['Seller Phone Number'];
                        $seller_bankaccountholdername = $v['Seller Bank Account Holder Name'];
                        $seller_bankname = $v['Seller Bank Name'];
                        $seller_bankbranch = $v['Seller Bank Branch'];
                        $seller_bankaccountno = $v['Seller Bank Account Number'];
                        $boughtprice = $v['Bought Price'];
                        $roidays = $v['ROI Days']; // 这个影响宠物
                        $roirate = $v['ROI Rate'];

                        $i++;

                        if ($seller_username) {
                            if (strlen($seller_username) > 0 && strlen($seller_username) <= 100) {
                                if (in_array($seller_username, $arr_usernames)) {
                                    $error['message'] = 'Line ' . $i . ': Seller Username exists';
                                }
                            } else {
                                $error['message'] = 'Line ' . $i . ': Seller Username invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Seller Username is required';
                        }

                        if ($packageid) {
                            if (strlen($packageid) > 0 && strlen($packageid) <= 100) { } else {
                                $error['message'] = 'Line ' . $i . ': Package ID invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Package ID is required';
                        }

                        if ($package) {
                            if (strlen($package) > 0 && strlen($package) <= 100) { } else {
                                $error['message'] = 'Line ' . $i . ': Package invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Package is required';
                        }

                        if ($paymenttype) {
                            if (strlen($paymenttype) > 0 && strlen($paymenttype) <= 100) { } else {
                                $error['message'] = 'Line ' . $i . ': Payment Type invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Payment Type is required';
                        }

                        if ($seller_phonenumber) {
                            if (strlen($seller_phonenumber) > 0 && strlen($seller_phonenumber) <= 100) {
                                if (in_array($seller_phonenumber, $arr_phonenumbers)) {
                                    $error['message'] = 'Line ' . $i . ': Seller Phone Number exists';
                                }
                            } else {
                                $error['message'] = 'Line ' . $i . ': Seller Phone Number invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Seller Phone Number is required';
                        }

                        if ($seller_bankaccountholdername) {
                            if (strlen($seller_bankaccountholdername) > 0 && strlen($seller_bankaccountholdername) <= 100) { } else {
                                $error['message'] = 'Line ' . $i . ': Seller Bank Account Holder Name is invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Seller Bank Account Holder Name is required';
                        }

                        if ($seller_bankname) {
                            if (strlen($seller_bankname) > 0 && strlen($seller_bankname) <= 100) { } else {
                                $error['message'] = 'Line ' . $i . ': Bank Name is invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Bank Name is required';
                        }

                        if ($seller_bankbranch) {
                            if (strlen($seller_bankbranch) > 0 && strlen($seller_bankbranch) <= 100) { } else {
                                $error['message'] = 'Line ' . $i . ': Bank Branch is invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Bank Branch is required';
                        }

                        if ($seller_bankaccountno) {
                            if (strlen($seller_bankaccountno) > 0 && strlen($seller_bankaccountno) <= 100) { } else {
                                $error['message'] = 'Line ' . $i . ': Bank Account Number is invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Bank Account Number is required';
                        }

                        if ($boughtprice) {
                            if (is_numeric($boughtprice)) {
                                if ($boughtprice > 0) { } else {
                                    $error['message'] = 'Line ' . $i . ': Bought Price must greater than 0';
                                }
                            } else {
                                $error['message'] = 'Line ' . $i . ': Bought Price is invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Bought Price is required';
                        }
                    } else {
                        $error['message'] = 'Invalid template';
                        break;
                    }

                    if (empty($error)) { } else {
                        break;
                    }
                }
            } else {
                $error['message'] = 'Worksheet is empty';
            }
        }

        if (empty($error)) {
            // Insert Sellers
            $i = 0;
            $batch_no = date('YmdHis') . Str::random(10);

            foreach ($col_seller as $k => $v) {
                $seller_username = $v['Seller Username'];
                $packageid = $v['Package ID'];
                $package = $v['Package'];
                $paymenttype = $v['Payment Type'];
                $seller_phonenumber = $v['Seller Phone Number'];
                $seller_bankaccountholdername = $v['Seller Bank Account Holder Name'];
                $seller_bankname = $v['Seller Bank Name'];
                $seller_bankbranch = $v['Seller Bank Branch'];
                $seller_bankaccountno = $v['Seller Bank Account Number'];
                $boughtprice = $v['Bought Price'];
                $roidays = $v['ROI Days'];
                $roirate = $v['ROI Rate'];

                $i++;

                $reservation_id = '';

                //  delete user
                try {
                    $update = DB::update("update Users 
                    set rec_type = 0,
                    batch_no = ?,
                    updated_by = ?,
                    updated_date = NOW(),
                    updated_location = ?
                    where rec_type > 0
                    and username = ?
                    and ifnull(isCompany,0) = 1", [$batch_no, $userid, $userid, $seller_username]);

                    if ($update) { }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                    break; //$ex->getMessage();
                }

                // add user
                try {
                    $insert = DB::insert("insert into Users (batch_no,type,username,phonenumber,referral_code,rank,isCompany,status,rec_type,created_by,created_date,updated_by,updated_date) values (?,'Main',?,?,?,1,1,'Active',1,?,NOW(),?,NOW())", [$batch_no, $seller_username, $seller_phonenumber, $seller_username, $userid, $userid]);

                    if (!$insert) {
                        $error['message'] = 'Error' . __LINE__ . ' line ' . $i . ': Unable to add this row into database, please contact support for assistance.';
                        break;
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                    break; //$ex->getMessage();
                }

                // delete bank
                try {
                    $update = DB::update("update UserBanks 
                    set rec_type = 0,
                    batch_no = ?,
                    updated_by = ?,
                    updated_date = NOW(),
                    updated_location = ?
                    where rec_type > 0
                    and username = ?", [$batch_no, $userid, $userid, $seller_username]);

                    if ($update) { }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                    break; //$ex->getMessage();
                }

                // add bank
                try {
                    $insert = DB::insert("insert into UserBanks (batch_no,type,username,recipient_phonenumber,bank_name,bank_branch,bank_account_no,bank_account_holder,isDefault,status,rec_type,created_by,created_date,created_location,updated_by,updated_date,updated_location) values (?,?,?,?,?,?,?,?,1,'Active',1,?,NOW(),?,?,NOW(),?)", [$batch_no, $paymenttype, $seller_username, $seller_phonenumber, $seller_bankname, $seller_bankbranch, $seller_bankaccountno, $seller_bankaccountholdername, $userid, $userip, $userid, $userip]);

                    if (!$insert) {
                        $error['message'] = 'Error' . __LINE__ . ' line ' . $i . ': Unable to add this row into database, please contact support for assistance.';
                        break;
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                    break; //$ex->getMessage();
                }

                // On sale
                try {
                    $insert = DB::insert("insert into PackageReservations (batch_no,username,package_id,package_description,status,rec_type,created_by,created_date,created_location,updated_by,updated_date,updated_location) values (?,?,?,?,'On Sale',1,?,NOW(),?,?,NOW(),?)", [$batch_no, $seller_username, $packageid, $package, $userid, $userip, $userid, $userip]);

                    if ($insert) {
                        $reservation_id = DB::getPDO()->lastInsertId();
                    } else {
                        $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                        break; //$ex->getMessage();
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                    break; //$ex->getMessage();
                }

                // pricing
                try {
                    $insert = DB::insert("insert into ReservationMatching (batch_no,package_reservation_id,username,recipient_amount,roi_days,roi_rate,status,rec_type,created_by,created_date,created_location,updated_by,updated_date,updated_location) values (?,?,?,?,?,?,'Pending',1,?,NOW(),?,?,NOW(),?)", [$batch_no, $reservation_id, $seller_username, $boughtprice, $roidays, $roirate, $userid, $userip, $userid, $userip]);

                    if ($insert) { } else {
                        $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                        break; //$ex->getMessage();
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                    break; //$ex->getMessage();
                }
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] =  'Success';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] = (isset($error['message'])) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    function ImportReservations(Request $request)
    {
        $userid = Auth::user()->id;
        $userip = $request->ip();
        $batch_no = $source_path = '';
        $col_reservations = array();

        foreach ($request->input() as $k => $v) {
            $response['input_' . $k] = $v;
        }

        foreach ($request->file() as $k => $v) {
            $response['file_' . $k] = $v;
        }

        if ($request->file('filereservations')) { } else {
            $error['message'] = 'File is required';
        }

        if (empty($error)) {
            $allowed_extensions = array("xls", "xlsx", "xlm", "xla", "xlc", "xlt", "xlw");

            $extension = $request->file('filereservations')->getClientOriginalExtension();

            if (!in_array($extension, $allowed_extensions)) {
                $error['message'] = 'Invalid file type: .' . $extension;
            }
        }

        if (empty($error)) {
            if ($request->file('filereservations')->getRealPath() != '') {
                $source_path = $request->file('filereservations')->getRealPath();
                $response['source_path'] = $source_path;
            } else {
                $error['message'] = 'ERR' . __LINE__ . ': Service unavailable';
            }
        }

        if (empty($error)) {
            // Import Reservations worksheet
            try {
                $col_reservations = (new FastExcel)->sheet(1)->import($source_path);
                $response['reservations'] = $col_reservations;
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__ . ': Service unavailable';
            }
        }

        if (empty($error)) {
            if (count($col_reservations) > 0) {
                $i = 0;
                foreach ($col_reservations as $k => $v) {
                    if (isset($v['Username']) && isset($v['Package'])) {
                        $buyer_username = $v['Username'];
                        $package = $v['Package'];

                        $i++;

                        if ($buyer_username) {
                            if (strlen($buyer_username) > 0 && strlen($buyer_username) <= 100) { } else {
                                $error['message'] = 'Line ' . $i . ': Buyer Username invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Buyer Username is required';
                        }

                        if ($package) {
                            if (strlen($package) > 0 && strlen($package) <= 100) { } else {
                                $error['message'] = 'Line ' . $i . ': Package invalid';
                            }
                        } else {
                            $error['message'] = 'Line ' . $i . ': Package is required';
                        }
                    } else {
                        $error['message'] = 'Invalid template';
                        break;
                    }

                    if (empty($error)) { } else {
                        break;
                    }
                }
            } else {
                $error['message'] = 'Worksheet is empty';
            }
        }

        if (empty($error)) {
            // Insert Reservations
            $i = 0;
            $batch_no = date('YmdHis') . Str::random(10);

            foreach ($col_reservations as $k => $v) {
                $buyer_username = $v['Username'];
                $package = $v['Package'];

                $i++;

                //  delete user reservations
                try {
                    $update = DB::update("update PackageReservations 
                    set rec_type = 0,
                    batch_no = ?,
                    updated_by = ?,
                    updated_date = NOW(),
                    updated_location = ?
                    where rec_type > 0
                    and username = ?", [$batch_no, $userid, $userid, $buyer_username]);

                    if ($update) { }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                    break; //$ex->getMessage();
                }

                // Reserve Success
                try {
                    $insert = DB::insert("insert into PackageReservations (batch_no,username,package_id,package_description,buy_amount,time_from,time_to,min_cost,max_cost,logo,status,rec_type,created_by,created_date,created_location,updated_by,updated_date,updated_location) 
                    select ?, a.username, a2.package_id, a2.package_description, a2.buy_amount, a2.time_from, a2.time_to, a2.min_cost, a2.max_cost, a2.logo, 'Reserve Success', 1, ?, NOW(), ?, ?, NOW(), ?
                    from Users a
                    join (select a1.id as package_id, a1.description as package_description, a1.buy_amount, a1.time_from, a1.time_to, a1.min_cost, a1.max_cost, a1.logo from Packages a1
                    where a1.description = ?
                    and a1.rec_type > 0
                    and a1.status = 'Active'
                    limit 1) a2
                    where a.rec_type > 0
                    and a.status = 'Active'
                    and a.username = ?
                    limit 1
                    ", [$batch_no, $userid, $userip, $userid, $userip, $package, $buyer_username]);

                    if ($insert) { } else {
                        $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                        break; //$ex->getMessage();
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                    break; //$ex->getMessage();
                }

                // Deduct Gold Coin
                try {
                    $insert = DB::insert("insert into Transactions (batch_no,username,wallet_type,type,description,amount,isAvailable,status,rec_type,created_by,created_date,created_location,updated_by,updated_date,updated_location)
                    select ?, a.username, 'Gold Coin', 'Reserve', a2.package_description, if(a2.buy_amount>0,a2.buy_amount*-1,0) as buy_amount, 1, 'Active', 1, ?, NOW(), ?, ?, NOW(), ?
                    from Users a
                    join (select a1.id as package_id, a1.description as package_description, a1.buy_amount, a1.time_from, a1.time_to, a1.min_cost, a1.max_cost, a1.logo from Packages a1
                    where a1.description = ?
                    and a1.rec_type > 0
                    and a1.status = 'Active'
                    limit 1) a2
                    where a.rec_type > 0
                    and a.status = 'Active'
                    and a.username = ?
                    limit 1", [$batch_no, $userid, $userip, $userid, $userip, $package, $buyer_username]);

                    if (!$insert) {
                        $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                        break; //$ex->getMessage();
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                    break; //$ex->getMessage();
                }
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] =  'Success';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] = (isset($error['message'])) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    function ImportTransactions(Request $request)
    {
        $userid = Auth::user()->id;
        $userip = $request->ip();
        $batch_no = $source_path = '';
        $col_transactions = $allowed_transaction_types = array();

        foreach ($request->input() as $k => $v) {
            $response['input_' . $k] = $v;
        }

        foreach ($request->file() as $k => $v) {
            $response['file_' . $k] = $v;
        }

        if ($request->file('import_file')) { } else {
            $error['message'] = 'File is required';
        }

        if (empty($error)) {
            $allowed_extensions = array("xls", "xlsx", "xlm", "xla", "xlc", "xlt", "xlw");

            $extension = $request->file('import_file')->getClientOriginalExtension();

            if (!in_array($extension, $allowed_extensions)) {
                $error['message'] = 'Invalid file type: .' . $extension;
            }
        }

        if (empty($error)) {
            if ($request->file('import_file')->getRealPath() != '') {
                $source_path = $request->file('import_file')->getRealPath();
                $response['source_path'] = $source_path;
            } else {
                $error['message'] = 'ERR' . __LINE__ . ': Service unavailable';
            }
        }

        if (empty($error)) {
            // Import Transactions worksheet
            try {
                $col_transactions = (new FastExcel)->sheet(1)->import($source_path);
                $response['transactions'] = $col_transactions;
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__ . ': Service unavailable';
            }
        }

        if (empty($error)) {
            // Get allowed transaction types
            try {
                $rs = DB::select("select a.type as wallet_type, b.type as wallet_transaction_types
                from WalletTypes a
                inner join WalletTransactionTypes b
                on a.type = b.wallet_type
                where a.rec_type > 0
                and b.rec_type > 0");
                $numrow = count($rs);

                if ($numrow > 0) {
                    foreach ($rs as $row) {
                        $wallet_type = $row->wallet_type;
                        $wallet_transaction_types = $row->wallet_transaction_types;

                        $allowed_transaction_types[] = $wallet_type . '-' . $wallet_transaction_types;
                    }
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'Error' . __LINE__ . ': Service unavailable'; // . $ex->getMessage();
            }
        }

        if (empty($error)) {
            if (count($col_transactions) > 0) {
                $i = 0;
                foreach ($col_transactions as $k => $v) {
                    if (isset($v['Username']) && isset($v['Wallet Type']) && isset($v['Type']) && isset($v['Description']) && isset($v['Amount'])) {
                        $username = $v['Username'];
                        $wallet_type = $v['Wallet Type'];
                        $type = $v['Type'];
                        $description = $v['Description'];
                        $amount = $v['Amount'];

                        $i++;

                        if (count($allowed_transaction_types) > 0) {
                            if (!in_array($wallet_type . '-' . $type, $allowed_transaction_types)) {
                                $error['message'] = 'Line ' . $i . ': Invalid Wallet Type and Type';
                            }
                        }

                        // if ($fullname) { } else {
                        //     $error['message'] = 'Line ' . $i . ': Full Name is required';
                        // }

                        // if ($username) { } else {
                        //     $error['message'] = 'Line ' . $i . ': Username is required';
                        // }

                        // if ($email) { } else {
                        //     $error['message'] = 'Line ' . $i . ': Email is required';
                        // }

                        // if ($nric) { } else {
                        //     $error['message'] = 'Line ' . $i . ': NRIC is required';
                        // }

                        // if ($dateofbirth) { } else {
                        //     $error['message'] = 'Line ' . $i . ': Date Of Birth is required';
                        // }

                        // if ($password) { } else {
                        //     $error['message'] = 'Line ' . $i . ': Password is required';
                        // }

                        // if ($country) { } else {
                        //     $error['message'] = 'Line ' . $i . ': Country is required';
                        // }

                        // if ($phonenumber >= 0) { } else {
                        //     $error['message'] = 'Line ' . $i . ': Phone Number is required';
                        // }

                        // if ($referralcode >= 0) { } else {
                        //     $error['message'] = 'Line ' . $i . ': Referral Code is required';
                        // }

                        // if ($referralby >= 0) { } else {
                        //     $error['message'] = 'Line ' . $i . ': Referral By is required';
                        // }

                        // if ($rank >= 0) { } else {
                        //     $error['message'] = 'Line ' . $i . ': Rank is required';
                        // }

                        // if ($status >= 0) { } else {
                        //     $error['message'] = 'Line ' . $i . ': Status is required';
                        // }
                    } else {
                        $error['message'] = 'Invalid template';
                        break;
                    }

                    if (empty($error)) { } else {
                        break;
                    }
                }
            } else {
                $error['message'] = 'Worksheet is empty';
            }
        }

        if (empty($error)) {
            $i = 0;
            $batch_no = date('YmdHis') . Str::random(10);

            foreach ($col_transactions as $k => $v) {
                $username = $v['Username'];
                $wallet_type = $v['Wallet Type'];
                $type = $v['Type'];
                $description = $v['Description'];
                $amount = $v['Amount'];

                $i++;

                try {
                    $insert = DB::insert("insert into Transactions (batch_no,username,wallet_type,type,description,amount,isAvailable,status,rec_type,created_by,created_date,updated_by,updated_date) values (?,?,?,?,?,?,1,'Active',1,?,NOW(),?,NOW())", [$batch_no, $username, $wallet_type, $type, $description, $amount, $userid, $userid]);

                    if (!$insert) {
                        $error['message'] = 'Error' . __LINE__ . ' line ' . $i . ': Unable to add this row into database, please contact support for assistance.';
                        break;
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Unable to add this row into database, please contact support for assistance.'; // . $ex->getMessage();
                    break;
                }
            }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] =  'Successfully imported all transactions';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] = (isset($error['message'])) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }

    function ImportReservationMatching(Request $request)
    {
        $userid = Auth::user()->id;
        $userip = $request->ip();
        $source_path = '';
        $col_reservations = $arr_reservations = array();
        $arr_package = array();
        $package = '';
        $batch_no = date('YmdHis') . Str::random(10);

        foreach ($request->input() as $k => $v) {
            $response['input_' . $k] = $v;
        }

        foreach ($request->file() as $k => $v) {
            $response['file_' . $k] = $v;
        }

        if ($request->file('import_file')) { } else {
            $error['message'] = 'File is required';
        }

        if (empty($error)) {
            $allowed_extensions = array("xls", "xlsx", "xlm", "xla", "xlc", "xlt", "xlw");

            $extension = $request->file('import_file')->getClientOriginalExtension();

            if (!in_array($extension, $allowed_extensions)) {
                $error['message'] = 'Invalid file type: .' . $extension;
            }
        }

        if (empty($error)) {
            if ($request->file('import_file')->getRealPath() != '') {
                $source_path = $request->file('import_file')->getRealPath();
                $response['source_path'] = $source_path;
            } else {
                $error['message'] = 'ERR' . __LINE__ . ': Service unavailable';
            }
        }

        if (empty($error)) {
            // Import Transactions worksheet
            try {
                $col_reservations = (new FastExcel)->sheet(1)->import($source_path);
                $response['reservations'] = $col_reservations;
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__ . ': Service unavailable';
            }
        }

        if (empty($error)) {
            // Check valid data
            try {
                $rs = DB::select("select a.username, a.package_description, a.id, DATE_FORMAT(a.created_date,'%Y-%m-%d %h:%i:%s %p') as created_date
                from PackageReservations a
                where a.rec_type > 0
                and a.status = 'Reserve Success'
                order by a.id");
                $numrow = count($rs);

                if ($numrow > 0) {
                    foreach ($rs as $row) {
                        $id = $row->id;
                        $username = $row->username;
                        $package_description = $row->package_description;
                        $created_date = $row->created_date;

                        $arr_reservations[] = $id . $username . $package_description . $created_date;
                    }
                } else {
                    $error['message'] = 'Matching list is empty'; //.$ex->getMessage();
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'ERR' . __LINE__ . ': Service unavailable'; //.$ex->getMessage();
            }
        }

        if (empty($error)) {
            if (count($col_reservations) > 0) {
                $i = 1;
                foreach ($col_reservations as $k => $v) {
                    if (isset($v['Username']) && isset($v['Package']) && isset($v['Amount']) && isset($v['ROIDays']) && isset($v['ROIRate']) && isset($v['ID']) && isset($v['ReservationsDateTime'])) {
                        $id = $v['ID'];
                        $createddate = $v['ReservationsDateTime'];
                        $username = $v['Username'];
                        $package = $v['Package'];

                        $i++;

                        if (!in_array($id . $username . $package . $createddate, $arr_reservations)) {
                            $error['message'] = 'Line ' . $i . ': Invalid data';
                            break;
                        } else {
                            if (!in_array($package, $arr_package)) {
                                $arr_package[] = $package;
                            }
                        }
                    } else {
                        $error['message'] = 'Service error';
                        break;
                    }

                    if (empty($error)) { } else {
                        break;
                    }
                }
            } else {
                $error['message'] = 'Document is empty';
            }
        }

        if (empty($error)) {
            if (count($arr_package) != 1) {
                $error['message'] = 'Error' . __LINE__ . ': Only 1 package allow at a time'; // . $ex->getMessage();
            }
        }

        // get the data from excel instead of database
        if (empty($error)) {
            if (count($col_reservations) > 0) {
                $i = 1;
                foreach ($col_reservations as $k => $v) {
                    if (isset($v['ID']) && isset($v['ReservationsDateTime']) && isset($v['Username']) && isset($v['Package']) && isset($v['MatchID']) && isset($v['Amount']) && isset($v['ROIDays']) && isset($v['ROIRate'])) {
                        $id = $v['ID'];
                        $matchid = $v['MatchID'];
                        $roidays = $v['ROIDays'];
                        $roirate = $v['ROIRate'];

                        $i++;

                        if (isset($matchid)) {
                            if ($matchid != '') {
                                try {
                                    $insert = DB::insert("insert into ReservationMatching (package_reservation_id,match_id,username,recipient_username,recipient_phonenumber,recipient_name,recipient_bank,recipient_bank_branch,recipient_bank_account_no,recipient_amount,roi_days,roi_rate,status,rec_type,created_by,created_date,created_location,updated_by,updated_date,updated_location,batch_no)
                                    select a.package_reservation_id, a1.match_id, a.username, a1.recipient_username, a1.recipient_phonenumber, a1.recipient_name, a1.recipient_bank, a1.recipient_bank_branch, a1.recipient_bank_account_no, round(ifnull(a1.recipient_amount,0),2) as recipient_amount, ?, ?, 'Pending',1,?,NOW(),?,?,NOW(),?,?
                                    from temp_reservation_matching a
                                    join temp_reservation_matching a1
                                    on a1.match_id = ?
                                    and a1.rec_type > 0
                                    where  a.package_reservation_id = ?
                                    and a.rec_type > 0
                                    ", [$roidays, $roirate, $userid, $userip, $userid, $userip, $batch_no, $matchid, $id]);

                                    if (!$insert) {
                                        $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Invalid data'; // . $ex->getMessage();
                                        break;
                                    }
                                } catch (\Illuminate\Database\QueryException $ex) {
                                    $error['message'] = 'Error' . __LINE__ . ' on line ' . $i . ': Invalid data'; // . $ex->getMessage();
                                    break;
                                }
                            }
                        }
                    } else {
                        $error['message'] = 'Error' . __LINE__ . ': Service unavailable'; // . $ex->getMessage();
                        break;
                    }

                    if (empty($error)) { } else {
                        break;
                    }
                }
            } else {
                $error['message'] = 'Error' . __LINE__ . ': Service unavailable'; // . $ex->getMessage();
            }
        }

        if (empty($error)) {
            try {
                $update = DB::update("update PackageReservations a
                left join ReservationMatching a1
                on a.id = a1.package_reservation_id
                and a1.rec_type > 0
                set a.status = 'Matching Success',
                a.batch_no = ?,
                a.updated_by = ?,
                a.updated_date = NOW(),
                a.updated_location = ?
                where a.rec_type > 0
                and a.package_description = ?
                and a.status = 'Reserve Success'
                and ifnull(a1.id,'') <> ''
                ", [$batch_no, $userid, $userip, $package]);

                if ($update) { }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'Error' . __LINE__ . ': Service error'; //. $ex->getMessage();
            }
        }

        if (empty($error)) {
            try {
                $update = DB::update("update PackageReservations a
                left join ReservationMatching a1
                on a.id = a1.package_reservation_id
                and a1.rec_type > 0
                set a.status = 'Matching Failed',
                a.batch_no = ?,
                a.updated_by = ?,
                a.updated_date = NOW(),
                a.updated_location = ?
                where a.rec_type > 0
                and a.package_description = ?
                and a.status = 'Reserve Success'
                and ifnull(a1.id,'') = ''
                ", [$batch_no, $userid, $userip, $package]);

                if ($update) { }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'Error' . __LINE__ . ': Service error'; //. $ex->getMessage();
            }
        }

        if (empty($error)) {
            try {
                $insert = DB::insert("insert into Transactions (batch_no,username,wallet_type,type,description,amount,isAvailable,status,rec_type,created_by,created_date,created_location,updated_by,updated_date,updated_location)
                select ?, a.username, 'Gold Coin', 'Reserve', a.status, a.buy_amount, 1, 'Active', 1, ?, NOW(), ?, ?, NOW(), ?
                from PackageReservations a
                where a.rec_type > 0
                and a.status = 'Matching Failed'
                and a.package_description = ?", [$batch_no, $userid, $userip, $userid, $userip, $package]);

                if ($insert) { }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error['message'] = 'Error' . __LINE__ . ': Service error'; //. $ex->getMessage();
            }
        }

        if (empty($error)) {
            try {
                $update = DB::update("update temp_reservation_matching
                    set rec_type = 0,
                    batch_no = ?,
                    updated_by = ?,
                    updated_date = NOW(),
                    updated_location = ?
                    where rec_type > 0
                    ", [$batch_no, $userid, $userip, $id]);

                if ($update) { }
            } catch (\Illuminate\Database\QueryException $ex) { }
        }

        if (empty($error)) {
            $response['status'] = 'success';
            $response['message'] =  'Upload successful';
        } else {
            $response['status'] = 'error';
            $response['error'] =  $error;
            $response['message'] = (isset($error['message'])) ? $error['message'] : 'Please check the form';
        }

        return response()->json($response);
    }
}
