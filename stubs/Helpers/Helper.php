<?php

namespace App\Helpers;

use PDO;
use Illuminate\Support\Facades\DB;
use Mail;
use Session;
use \DateTime;
use \jamesiarmes\PhpEws\Client;
use \jamesiarmes\PhpEws\Request\CreateItemType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAttendeesType;
use \jamesiarmes\PhpEws\Enumeration\BodyTypeType;
use \jamesiarmes\PhpEws\Enumeration\CalendarItemCreateOrDeleteOperationType;
use \jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use \jamesiarmes\PhpEws\Type\BodyType;
use \jamesiarmes\PhpEws\Type\CalendarItemType;
use \jamesiarmes\PhpEws\Type\ExchangeImpersonationType;
use \jamesiarmes\PhpEws\Type\ConnectingSIDType;
use Adldap\Laravel\Facades\Adldap;

class Helper
{

    /**
     * Plain PHP function to send email
     *
     * @param $from
     * @param $to
     * @param $subject
     * @param $html
     */
    public static function sendEmail($from, $to, $subject, $html)
    {
        Mail::send(array(), array(), function($message) use ($from, $html, $to, $subject)
        {
            $message->from($from);
            $message->to($to);
            $message->subject($subject);
            $message->setBody($html, 'text/html');
        });

    }

    /**
     * Get User NetID from UIN
     * @param $uin
     *
     * @return mixed|string
     */
    public static function getNetidFromUIN($uin)
    {
        $result = '';
        $search = Adldap::search()->where('extensionattribute1', '=', $uin)->get();

        if(isset($search[0]->cn[0]))
        {
            $result = $search[0]->cn[0];
        }

        return $result;
    }

    /**
     * Get User Full Name from UIN
     * @param $uin
     *
     * @return string
     */
    public static function getFullNameFromUin($uin)
    {
        $result = '';
        $search = Adldap::search()->where('extensionattribute1', '=', $uin)->get();

        if(isset($search[0]->cn[0]))
        {
            $result = $search[0]->givenname[0] . ' ' . $search[0]->sn[0];
        }

        return $result;
    }

    /**
     * Add Event to Exchange Calendar
     *
     * @param $start_string
     * @param $end_string
     * @param $subject
     * @param $body
     * @param $cal_username
     * @param $server
     * @param $username
     * @param $password
     * @param $tz
     */
    public static function addToExchangeCalendar($start_string, $end_string, $subject, $body, $cal_username, $server, $username, $password, $tz)
    {
        $version = Client::VERSION_2013;
        $start = DateTime::createFromFormat('Y-m-d H:i:s', $start_string);
        $end_day_before = DateTime::createFromFormat('Y-m-d H:i:s', $end_string);
        $end = $end_day_before->modify('+1 day');

        $ews = new Client($server, $username, $password, $version);
        $ews->setTimezone($tz);

        //Impersonate
        $ei = new ExchangeImpersonationType();
        $sid = new ConnectingSIDType();
        $sid->PrimarySmtpAddress = $cal_username;
        $ei->ConnectingSID = $sid;
        $ews->setImpersonation($ei);


        $request = new CreateItemType();
        $request->SendMeetingInvitations = CalendarItemCreateOrDeleteOperationType::SEND_ONLY_TO_ALL;
        $request->Items = new NonEmptyArrayOfAllItemsType();

        // Build the event to be added.
        $event = new CalendarItemType();
        $event->Start = $start->format('c');
        $event->End = $end->format('c');
        $event->Subject = $subject;

        $event->LegacyFreeBusyStatus = 'Free';
        $event->ReminderIsSet = false;

        // Set the event body.
        $event->Body = new BodyType();
        $event->Body->_ = $body;
        $event->Body->BodyType = BodyTypeType::TEXT;

        // Add the event to the request. You could add multiple events to create more
        // than one in a single request.
        $request->Items->CalendarItem[] = $event;

        $response = $ews->CreateItem($request);

        // Iterate over the results, printing any error messages or event ids.
        $response_messages = $response->ResponseMessages->CreateItemResponseMessage;

        foreach ($response_messages as $response_message)
        {
            // Make sure the request succeeded.
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS)
            {
                $code = $response_message->ResponseCode;
                $message = $response_message->MessageText;
                Session::flash('success', "Event failed to create with \"$code: $message\"\n");
                continue;
            }

            // Iterate over the created events, printing the id for each.
            foreach ($response_message->Items->CalendarItem as $item)
            {
                $id = $item->ItemId->Id;
                Session::flash('success', 'The event was successfully added to the calendar.');
                //dd("Created event $id\n");
            }//end of foreach
        }

    }

    /**
     * Get Current Semester
     *
     * @return mixed|null
     */
    public static function getSemester()
    {
        $semester = DB::connection("oraclecdmpvt")->table('vw_semester')->value('current_term');
        return $semester;
    }

    /**
     * Get Supervisor from User UIN
     * @param $uin
     *
     * @return string
     */
    public static function getSupervisor($uin)
    {
        $result = '';
        $supervisor_info = DB::connection("oraclecdmpvt")->table('EMP_SUPV_DEPT_UIS')->where('uin', $uin)->first();

        if($supervisor_info)
        {
            $result = $supervisor_info->supv_lname . ', ' . $supervisor_info->supv_fname;
        }

        return $result;
    }

    /**
     * Get Provost UIN
     *
     * @return mixed|string
     */
    public static function getProvostUin()
    {
        $result = '';
        $supervisor_info = DB::connection("oraclecdmpvt")->table('EMP_SUPV_DEPT_UIS')->where('posn_title', 'VCHAN ACAD AFF & PROV')->first();

        if($supervisor_info)
        {
            $result =  $supervisor_info->uin;
        }
        return $result;
    }

    public static function isDeanDirectorAVC($uin)
    {
        $result = false;
        $result =  DB::connection("oraclecdmpvt")->table('EMP_SUPV_DEPT_UIS')
            ->where('uin', $uin)
            ->where(function($query){
                $query->whereIn('empee_coll_cd', ['SB', 'PL', 'PK', 'PH', 'PG', 'PF', 'PE']);
                $query->whereNotIn('empee_group_cd', ['S', 'E', 'G', 'H']);
                $query->where('posn_title', 'NOT LIKE', '%ASTTO%');
                $query->where('posn_title', 'NOT LIKE', '%ASST%');
                $query->where('posn_title', 'NOT LIKE', '%ASTAC%');
            })
            ->where(function($query){
                $query->orWhere('posn_title', 'LIKE', '%DEAN%');
                $query->orWhere('posn_title', 'LIKE', '%DIR%');
                $query->orWhere('posn_title', 'LIKE', '%EXEC DIR%');
                $query->orWhere('posn_title', 'LIKE', '%VCHAN%');
                $query->orWhere('posn_title', 'LIKE', 'ASSOC VC%');
                $query->orWhere('posn_title', 'LIKE', '%CIO%');
                $query->orWhere('posn_title', 'LIKE', 'PRVST%');
                $query->orWhere('posn_title', 'LIKE', 'ASSOC VICE CHANC ONLINE LEARN');
                $query->orWhere('uin', 'LIKE', '675102932');  //Marc Klingshirn - ASSOC PROF
            })->get()->isNotEmpty();

        return $result;
    }

    /**
     * Get User Title from UIN
     * @param $uin
     *
     * @return mixed|string
     */
    public static function getTitleFromUin($uin)
    {
        $result = '';
        $supervisor_info = DB::connection("oraclecdmpvt")->table('EMP_SUPV_DEPT_UIS')->where('uin', $uin)->first();

        if($supervisor_info)
        {

            $result =  $supervisor_info->posn_title;
        }
        return $result;

    }

    /**
     * Get User UIN from BarCode
     * @param $barcode
     *
     * @return false|string
     */
    public static function getUinFromBarcode($barcode)
    {
        $uin = '';
        $uin = substr($barcode, 4, 9);
        return $uin;
    }

    /**
     * Get LoggedIn User NetID
     * @return mixed|string
     */
    public static function getLoggedUserNetid()
    {
        $result = '';
        if(isset($_SERVER['cn']))
        {
            $result = $_SERVER['cn'];
        }
        return $result;
    }

    /**
     * Get LoggedIn user UIN
     *
     * @return mixed|string
     */
    public static function getLoggedUserUIN()
    {
        $result = '';
        if(isset($_SERVER['iTrustUIN']))
        {
            $result = $_SERVER['iTrustUIN'];
        }
        return $result;
    }

    /**
     * Get User picture from UIN
     *
     * @param $uin
     *
     * @return string
     */
    public static function getPic($uin)
    {
        $result = '';
        $sth = DB::connection("sqlsrv")->getPdo()->prepare('exec IDData.uspShellUINPhotoInfo ' . $uin);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_COLUMN, 4);

        $hex = $result[0];
        $n = strlen($hex);
        $sbin="";
        $i=0;
        while($i < $n)
        {
            $a =substr($hex,$i,2);
            $c = pack("H*",$a);
            if ($i == 0)
            {
                $sbin = $c;
            }
            else
            {
                $sbin .= $c;
            }
            $i += 2;
        }
        $result = base64_encode($sbin);
        return $result;
    }

    /**
     * Get Base 64 No picture
     * @return string
     */
    public static function getNopicBase64()
    {
        return "/9j/4QlQaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjYtYzE0MCA3OS4xNjA0NTEsIDIwMTcvMDUvMDYtMDE6MDg6MjEgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiLz4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8P3hwYWNrZXQgZW5kPSJ3Ij8+/+0ALFBob3Rvc2hvcCAzLjAAOEJJTQQlAAAAAAAQ1B2M2Y8AsgTpgAmY7PhCfv/bAIQAEAsLCwwLEAwMEBgPDQ8YHBUQEBUcIBcXFxcXIB8YGxoaGxgfHyQmKSYkHzExNTUxMUFBQUFBQUFBQUFBQUFBQQERDw8SFBIWExMWFREUERUaFRcXFRonGhodGhonMiMfHx8fIzIsLykpKS8sNjYyMjY2QUFBQUFBQUFBQUFBQUFB/90ABAAZ/+4ADkFkb2JlAGTAAAAAAf/AABEIAZABjwMAIgABEQECEQH/xACPAAEAAwEBAQEAAAAAAAAAAAAABQYHBAMCAQEBAQEAAAAAAAAAAAAAAAAAAAECEAACAgECAgIMCgoDAAEFAAAAAQIDBAURBiESMRMUIjVBUWFxcnOxshYy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";
    }

}
