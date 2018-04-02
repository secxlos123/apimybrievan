<?php

namespace App\Classes\Client\Services;

use App\Classes\Client\Client;

class ServiceRestwsHc extends Client
{
    /**
     * The headers that will be sent when call the API.
     *
     * @var array
     */
    public function uri()
    {
        $base_url = config( "restapi.restwshc" );

        if ( in_array( env( "APP_ENV" ), ["local", "staging"] ) ) {
            $base_url .= json_decode( $this->body["request"] )->requestMethod;
        }

        return $base_url;
    }

    /**
     * The headers that will be sent when call the API.
     *
     * @var array
     */
    public static function getUser( $pn = null )
    {
        $pn = empty( $pn ) ? request()->header( "pn" ) : $pn;
        $user_info_service = \RestwsHc::setBody(
                [
                    "request" => json_encode(
                        [
                            "requestMethod" => "get_user_info"
                            , "requestData" => [
                                "id_cari" => $pn
                                , "id_user" => request()->header( "pn" )
                            ]
                        ]
                    )
                ]
            )
            ->post( "form_params" );

        if ( ! empty( $user_info_service ) ) {
            if ( $user_info_service["responseCode"] == "00" ) {
                $hilfm = intval( $user_info_service["responseData"]["HILFM"] );
                $position = strtolower( $user_info_service["responseData"]["ORGEH_TX"] );

                if ( in_array( $hilfm, [ 37, 38, 39, 41, 42, 43 ] ) ) {
                    $role = "ao";

                } else if ( in_array( $hilfm, [ 21, 49, 50, 51 ] ) ) {
                    $role = "mp";

                    if ( in_array( $hilfm, [ 49, 51 ] ) ) {
                        $role_user = "amp";

                    }

                } else if ( in_array( $hilfm, [ 44 ] ) ) {
                    $role = "fo";

                } else if ( in_array( $hilfm, [ 46 ] ) ) {
                    $role = "mantri";

                } else if ( in_array( $hilfm, [ 5, 11, 12, 14, 19 ] ) ) {
                    $role = "pinca";

                    if ( in_array( $hilfm, [ 19 ] ) ) {
                        $role_user = "pincapem";

                    } else if ( in_array( $hilfm, [ 5 ] ) ) {
                        $role_user = "pincasus";

                    } else if ( in_array( $hilfm, [ 11 ] ) ) {
                        $role_user = "wapincasus";

                    }

                } else if ( in_array( $hilfm, [ 59 ] ) ) {
                    $role = "prescreening";

                    if ( in_array( $position, [ "collateral appraisal", "collateral manager" ] ) ) {
                        $role = str_replace(" ", "-", $position);

                    }

                } else if ( in_array( $hilfm, [ 3 ] ) ) {
                    $role = "pinwil";

                } else if ( in_array( $hilfm, [ 9 ] ) ) {
                    $role = "wapinwil";

                } else if ( in_array( $hilfm, [ 53 ] ) ) {
                    $role = "spvkanwil";

                } else if ( in_array( $hilfm, [ 66, 71, 75 ] ) ) {
                    $role = "cs";

                } else if ( in_array( $hilfm, [ 65 ] ) ) {
                    $role = "teller";

                } else if ( in_array( $hilfm, [ 54 ] ) ) {
                    $role = "spvadk";

                } else if ( in_array( $hilfm, [58, 61] ) ) {
                    $role = "adk";

                } else {
                    $role = "staff";

                }

                $department = $user_info_service["responseData"]["STELL_TX"];
                if ( ENV( "APP_ENV" ) == "local" ) {
                    $branch = "12";
                    if ( intval( $pn ) == 70828 ) {
                        $role = "collateral-manager";
                        $department = "PJ. COLLATERAL MANAGER";

                    }

                } else {
                    $branch = $user_info_service["responseData"]["BRANCH"];
                    $pn = $user_info_service["responseData"]["PERNR"];

                }

                $superadmin = [ "00054805", "00139644", "00076898", "00079072" ];
                if ( in_array( $pn, $superadmin ) ) $role = "superadmin";

                return [
                    "name" => $user_info_service["responseData"]["SNAME"]
                    , "nip" => $user_info_service["responseData"]["NIP"]
                    , "role_id" => $user_info_service["responseData"]["HILFM"]
                    , "role" => $role
                    , "role_user" => isset( $role_user ) ? $role_user : $role
                    , "branch_id" => $branch
                    , "pn" => $pn
                    , "position" => $user_info_service["responseData"]["ORGEH_TX"]
                    , "department" => $department
                    , "hilfm" => $user_info_service["responseData"]["HILFM"]
                ];
            }
        }
        return false;
    }

    /**
     * Get service region
     *
     * @var array
     */
    public static function getRegion( $branch = null )
    {
        $getKanwil = \RestwsHc::setBody(
                [
                    "request" => json_encode(
                        [
                            "requestMethod" => "get_list_uker_from_cabang"
                            , "requestData" => [
                                "app_id" => "mybriapi"
                                , "branch_code" => $branch
                            ]
                        ]
                    )
                ]
            )
            ->post( "form_params" );

        $region["region_id"] = "Q";

        if ( $getKanwil["responseCode"] == "00" ) {
            foreach ( $getKanwil["responseData"] as $kanwil ) {
                $branchid = substr( "00000" . $kanwil["branch"], -5 );

                if ( $branchid == $branch) $region["region_id"] = $kanwil["region"];
            }
        }

        return $region;
    }
}
